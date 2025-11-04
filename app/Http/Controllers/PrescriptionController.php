<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Prescription;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PrescriptionController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function create(Request $request)
    {
        $authAccount = $this->guard->user();
        $clinicId = session('clinic_id') ?? $authAccount->clinic_id;

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,patient_id',
            'medicine_id' => 'required|uuid|exists:medicines,medicine_id',
            'tooth_id' => 'nullable|uuid|exists:tooth_list,tooth_list_id',
            'amount' => 'required|integer|min:1',
            'medicine_cost' => 'required|numeric|min:0',
            'dosage_instructions' => 'required|string',
            'prescription_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $medicineId = $request->medicine_id;

        // 🔍 Fetch the medicine record (and its stock)
        $medicineClinic = \App\Models\MedicineClinic::where('clinic_id', $clinicId)
            ->where('medicine_id', $medicineId)
            ->first();

        if (! $medicineClinic) {
            return back()->with('error', 'Medicine not available for this clinic.');
        }

        // 🚨 Validate stock
        if ($medicineClinic->stock < $request->amount) {
            return back()->with('error', 'Insufficient stock. Only '.$medicineClinic->stock.' remaining.');
        }

        // 🧾 Create new Prescription record
        $prescription = Prescription::create([
            'prescription_id' => Str::uuid(),
            'account_id' => $authAccount->account_id,
            'patient_id' => $request->patient_id,
            'clinic_id' => $clinicId,
            'medicine_id' => $medicineId,
            'tooth_list_id' => $request->tooth_id,
            'amount_prescribed' => $request->amount,
            'medicine_cost' => $request->medicine_cost,
            'dosage_instructions' => $request->dosage_instructions,
            'prescription_notes' => $request->prescription_notes,
            'prescribed_at' => now(),
            'status' => 'prescribed',
        ]);

        // 🪵 Log action
        LogService::record(
            $authAccount,
            $prescription,
            'create',
            'Prescription',
            'User created a new prescription',
            'Prescription ID: '.$prescription->prescription_id.' | Medicine: '.$prescription->medicine->name.' | Amount: '.$request->amount,
            $request->ip(),
            $request->userAgent()
        );

        return back()->with('success', 'Prescription created successfully.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prescription_id' => 'required|uuid|exists:prescriptions,prescription_id',
            'status' => 'required|string|in:prescribed,purchased',
            'tooth_list_id' => 'nullable|uuid|exists:tooth_list,tooth_list_id',
            'amount_prescribed' => 'required|integer|min:1', // ✅ new validation
            'medicine_cost' => 'required|numeric|min:0',
            'dosage_instructions' => 'required|string',
            'prescription_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $prescription = Prescription::findOrFail($request->prescription_id);

        $oldStatus = $prescription->status;
        $oldTooth = $prescription->tooth_list_id;
        $oldAmount = $prescription->amount_prescribed;

        $prescription->status = $request->status;
        $prescription->tooth_list_id = $request->tooth_list_id;
        $prescription->amount_prescribed = $request->amount_prescribed;
        $prescription->medicine_cost = $request->medicine_cost; // total cost from modal
        $prescription->dosage_instructions = $request->dosage_instructions;
        $prescription->prescription_notes = $request->prescription_notes;
        $prescription->save();

        // ✅ Total cost is already provided by frontend (not per-unit)
        $totalPrice = $prescription->medicine_cost;

        $authAccount = $this->guard->user();
        
        // Billing logic (only if status changed)
        if ($oldStatus !== $prescription->status) {

            if ($prescription->status === 'purchased') {
                // Create or attach to unpaid bill
                $bill = Bill::firstOrCreate(
                    [
                        'patient_id' => $prescription->patient_id,
                        'clinic_id' => $prescription->clinic_id,
                        'status' => 'unpaid',
                    ],
                    [
                        'bill_id' => Str::uuid(),
                        'account_id' => $authAccount->account_id,
                        'amount' => 0,
                        'discount' => 0,
                        'total_amount' => 0,
                    ]
                );

                // Create bill item if none exists
                $existingBillItem = BillItem::where('prescription_id', $prescription->prescription_id)
                    ->whereHas('bill', fn ($q) => $q
                        ->where('patient_id', $prescription->patient_id)
                        ->where('clinic_id', $prescription->clinic_id)
                        ->where('status', 'unpaid')
                    )
                    ->first();

                if (! $existingBillItem) {
                    BillItem::create([
                        'bill_item_id' => Str::uuid(),
                        'bill_id' => $bill->bill_id,
                        'account_id' => auth()->id(),
                        'item_type' => 'prescription',
                        'prescription_id' => $prescription->prescription_id,
                        'service_id' => null,
                        'tooth_list_id' => $prescription->tooth_list_id,
                        'amount' => $totalPrice, // ✅ respects quantity
                    ]);

                    $bill->increment('amount', $totalPrice);
                    $bill->increment('total_amount', $totalPrice);
                }

            } elseif ($prescription->status === 'prescribed') {
                // Revert unpaid item
                $billItem = BillItem::where('prescription_id', $prescription->prescription_id)
                    ->whereHas('bill', fn ($q) => $q
                        ->where('patient_id', $prescription->patient_id)
                        ->where('clinic_id', $prescription->clinic_id)
                        ->where('status', 'unpaid')
                    )
                    ->first();

                if ($billItem) {
                    $bill = $billItem->bill;
                    $amount = (float) $billItem->amount;

                    $bill->decrement('amount', $amount);
                    $bill->decrement('total_amount', $amount);
                    $billItem->forceDelete();
                }
            }
        }

        // ✅ If only tooth or amount changed, sync them to unpaid BillItem
        if ($oldTooth !== $prescription->tooth_list_id || $oldAmount !== $prescription->amount_prescribed) {
            $billItem = BillItem::where('prescription_id', $prescription->prescription_id)
                ->whereHas('bill', fn ($q) => $q
                    ->where('patient_id', $prescription->patient_id)
                    ->where('clinic_id', $prescription->clinic_id)
                    ->where('status', 'unpaid')
                )
                ->first();

            if ($billItem) {
                $oldAmountValue = (float) $billItem->amount;
                $bill = $billItem->bill;

                // Update the bill item
                $billItem->update([
                    'tooth_list_id' => $prescription->tooth_list_id,
                    'amount' => $totalPrice,
                ]);

                // Adjust bill totals
                $difference = $totalPrice - $oldAmountValue;
                $bill->increment('amount', $difference);
                $bill->increment('total_amount', $difference);
            }
        }

        // ✅ Log the update
        LogService::record(
            $this->guard->user(),
            $prescription,
            'update',
            'Prescription',
            'User updated prescription details',
            'Prescription ID: '.$prescription->prescription_id.
            ' | Status: '.$prescription->status.
            ' | Tooth: '.$prescription->tooth_list_id.
            ' | Amount Prescribed: '.$prescription->amount_prescribed,
            $request->ip(),
            $request->userAgent()
        );

        return back()->with('success', 'Prescription updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'prescription_id' => 'required|exists:prescriptions,prescription_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $prescription = Prescription::findOrFail($request->prescription_id);

        // 🔒 Verify password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($prescription, $request, $deletor) {

            // 🧾 Find related unpaid BillItem (if any)
            $billItem = BillItem::where('prescription_id', $prescription->prescription_id)
                ->whereHas('bill', fn ($q) => $q
                    ->where('patient_id', $prescription->patient_id)
                    ->where('clinic_id', $prescription->clinic_id)
                    ->where('status', 'unpaid')
                )
                ->first();

            if ($billItem) {
                $bill = $billItem->bill;
                $amount = (float) $billItem->amount;

                // ✅ Update the bill before deleting the bill item
                $bill->decrement('amount', $amount);
                $bill->decrement('total_amount', $amount);

                $billItem->delete();
            }

            // 🗑️ Delete the prescription
            $prescription->delete();

            // 🪵 Log the deletion
            LogService::record(
                $deletor,
                $prescription,
                'delete',
                'Prescription',
                'User deleted a prescription',
                'Prescription ID: '.$prescription->prescription_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('specific-patient', ['id' => $prescription->patient_id])
                ->with('success', 'Prescription deleted and bill updated successfully.');
        });
    }
}
