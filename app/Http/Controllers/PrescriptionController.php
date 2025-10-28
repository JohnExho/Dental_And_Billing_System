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
