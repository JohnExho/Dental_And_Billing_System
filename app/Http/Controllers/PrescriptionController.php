<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Prescription;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 🧮 Decrease stock
        $medicineClinic->decrement('stock', $request->amount);

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
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $prescription = Prescription::findOrFail($request->prescription_id);
        $oldStatus = $prescription->status;
        $prescription->status = $request->status;
        $prescription->save();

        // Only perform logic if the status actually changed
        if ($oldStatus !== $prescription->status) {

            if ($prescription->status === 'purchased') {

                // Find or create an unpaid bill for this patient and clinic
                $bill = Bill::where('patient_id', $prescription->patient_id)
                    ->where('clinic_id', $prescription->clinic_id)
                    ->where('status', 'unpaid')
                    ->first();

                if (! $bill) {
                    $bill = Bill::create([
                        'bill_id' => Str::uuid(),
                        'account_id' => auth()->id(),
                        'patient_id' => $prescription->patient_id,
                        'clinic_id' => $prescription->clinic_id,
                        'amount' => 0,
                        'discount' => 0,
                        'total_amount' => 0,
                        'status' => 'unpaid',
                    ]);
                }

                // Get the price of the medicine for this clinic
                $medicine = $prescription->medicine;
                $price = $medicine->medicineClinics()
                    ->where('clinic_id', $prescription->clinic_id)
                    ->value('price') ?? 0;

                // Check if a bill item already exists for this prescription
                $existingBillItem = BillItem::where('prescription_id', $prescription->prescription_id)
                    ->whereHas('bill', function ($query) use ($prescription) {
                        $query->where('patient_id', $prescription->patient_id)
                            ->where('clinic_id', $prescription->clinic_id)
                            ->where('status', 'unpaid');
                    })
                    ->first();

                // Only create if it doesn't exist
                if (! $existingBillItem) {
                    BillItem::create([
                        'bill_item_id' => Str::uuid(),
                        'bill_id' => $bill->bill_id,
                        'account_id' => auth()->id(),
                        'item_type' => 'prescription',
                        'prescription_id' => $prescription->prescription_id,
                        'service_id' => null,
                        'tooth_list_id' => $prescription->tooth_id,
                        'amount' => $price,
                    ]);

                    $bill->increment('amount', $price);
                    $bill->increment('total_amount', $price);
                }

            } elseif ($prescription->status === 'prescribed') {
                // Reverting — remove linked bill items only if still unpaid
                $billItem = BillItem::where('prescription_id', $prescription->prescription_id)
                    ->whereHas('bill', function ($query) use ($prescription) {
                        $query->where('patient_id', $prescription->patient_id)
                            ->where('clinic_id', $prescription->clinic_id)
                            ->where('status', 'unpaid');
                    })
                    ->first();

                if ($billItem) {
                    $bill = $billItem->bill;
                    $amount = (float) $billItem->amount; // ✅ ensure numeric

                    // Decrement totals before deleting
                    $bill->decrement('amount', $amount);
                    $bill->decrement('total_amount', $amount);

                    $billItem->forceDelete();
                }
            }
        }

        // ✅ Log the change
        LogService::record(
            $this->guard->user(),
            $prescription,
            'update',
            'Prescription',
            'User updated a prescription status',
            'Prescription ID: '.$prescription->prescription_id.' | Status: '.$prescription->status,
            $request->ip(),
            $request->userAgent()
        );

        return back()->with('success', 'Prescription updated successfully.');
    }
}
