<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Note;
use App\Models\Service;
use App\Models\BillItem;
use App\Models\Treatment;
use Illuminate\Support\Str;
use App\Services\LogService;
use Illuminate\Http\Request;
use App\Models\BillItemTooth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TreatmentController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,patient_id',
            'procedure_id' => 'required|uuid|exists:services,service_id',
            'tooth_id' => 'nullable|array',
            'tooth_id.*' => 'nullable|uuid|exists:tooth_list,tooth_list_id',
            'note' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'net_cost' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $request) {
            $authAccount = $this->guard->user();
            $clinicId = session('clinic_id') ?? $authAccount->clinic_id;

            // 1. Compute cost
            $totalCost = (float) ($request->net_cost ?? 0);
            Log::info('Computed net_cost: '.$totalCost);

            // 2. Find existing unpaid bill
            $bill = Bill::where('patient_id', $validated['patient_id'])
                ->where('status', 'unpaid')
                ->where('clinic_id', $clinicId)
                ->first();

            // 3. Create bill if not existing
            if (! $bill) {
                $bill = Bill::create([
                    'bill_id' => Str::uuid(),
                    'account_id' => $authAccount->account_id,
                    'patient_id' => $validated['patient_id'],
                    'clinic_id' => $clinicId,
                    'amount' => 0,
                    'total_amount' => 0,
                    'status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 4. Create Bill Item
            $billItem = BillItem::create([
                'bill_item_id' => Str::uuid(),
                'bill_id' => $bill->bill_id,
                'account_id' => $authAccount->account_id,
                'item_type' => 'service',
                'service_id' => $validated['procedure_id'],
                'amount' => $totalCost,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. BillItem teeth linking
            if (! empty($validated['tooth_id']) && is_array($validated['tooth_id'])) {
                foreach ($validated['tooth_id'] as $toothId) {
                    if (empty($toothId)) {
                        continue;
                    }
                    BillItemTooth::create([
                        'bill_item_tooth_id' => Str::uuid(),
                        'bill_item_id' => $billItem->bill_item_id,
                        'tooth_list_id' => $toothId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 6. Update Bill totals
            $bill->amount += $totalCost;
            $bill->total_amount += $totalCost;
            $bill->save();
            $service = Service::find($validated['procedure_id']);
            // 7. Create Treatment
            $treatment = Treatment::create([
                'patient_treatment_id' => Str::uuid(),
                'patient_id' => $validated['patient_id'],
                'account_id' => $authAccount->account_id,
                'bill_item_id' => $billItem->bill_item_id,
                'clinic_id' => $clinicId,
                'status' => $validated['status'],
                'treatment_date' => now(),
                'treatment_name' => $service?->name,
            ]);

            // 8. Create Note
            if (! empty($validated['note'])) {
                $note = Note::create([
                    'note_id' => Str::uuid(),
                    'account_id' => $authAccount->account_id,
                    'patient_id' => $validated['patient_id'],
                    'patient_treatment_id' => $treatment->patient_treatment_id,
                    'summary' => Str::limit($validated['note'], 100, '...'),
                    'note' => $validated['note'],
                    'note_type' => 'treatment',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 9. Logs
            LogService::record(
                $authAccount,
                $treatment,
                'create',
                'Treatment',
                'User created a new treatment',
                "Patient ID: {$validated['patient_id']} | Treatment ID: {$treatment->patient_treatment_id}",
                $request->ip(),
                $request->userAgent()
            );

            if (! empty($validated['note'])) {
                LogService::record(
                    $authAccount,
                    $note,
                    'create',
                    'Treatment Note',
                    'User created a treatment note with billing',
                    "Patient ID: {$validated['patient_id']} | Note ID: {$note->note_id} | Amount: {$totalCost}",
                    $request->ip(),
                    $request->userAgent()
                );
            }

            return back()->with('success', 'Treatment and billing added successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:patient_treatments,patient_treatment_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $treatment = Treatment::findOrFail($request->treatment_id);

        // Check if the password matches the current user's password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($treatment, $request, $deletor) {
            // Get the bill item and its amount
            $billItem = $treatment->billItem;
            if ($billItem) {
                // Get the associated bill and update totals
                $bill = Bill::find($billItem->bill_id);
                if ($bill) {
                    $bill->amount -= $billItem->amount;
                    $bill->total_amount -= $billItem->amount;
                    $bill->save();
                }

                // Delete associated BillItemTooth records
                BillItemTooth::where('bill_item_id', $billItem->bill_item_id)->delete();
            }

            $treatment->visit()->delete();
            $treatment->billItem()->delete();
            $treatment->delete();

            // Logging
            LogService::record(
                $deletor,
                $treatment,
                'delete',
                'treatment',
                'User deleted a treatment',
                'Treatment ID: '.$treatment->patient_treatment_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('specific-patient')->with('success', 'Patient treatment deleted successfully.');
        });
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'treatment_id' => 'required|uuid|exists:patient_treatments,patient_treatment_id',
            'patient_id' => 'required|uuid|exists:patients,patient_id',
            'procedure_id' => 'required|uuid|exists:services,service_id',
            'tooth_id' => 'nullable|array',
            'tooth_id.*' => 'nullable|uuid|exists:tooth_list,tooth_list_id',
            'note' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'net_cost' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $request) {
            $authAccount = $this->guard->user();

            // 1. Get the treatment
            $treatment = Treatment::findOrFail($validated['treatment_id']);

            // 2. Compute new cost
            $totalCost = $request->net_cost ?? 0;

            // 3. Update existing bill item
            $billItem = $treatment->billItem;
            if ($billItem) {
                // Get the bill to update totals
                $bill = Bill::find($billItem->bill_id);
                if ($bill) {
                    // Subtract old amount and add new amount
                    $bill->amount = $bill->amount - $billItem->amount + $totalCost;
                    $bill->total_amount = $bill->total_amount - $billItem->amount + $totalCost;
                    $bill->save();
                }

                // Update bill item
                $billItem->service_id = $validated['procedure_id'];
                $billItem->amount = $totalCost;
                $billItem->save();

                // Always delete old teeth first no matter what
                BillItemTooth::where('bill_item_id', $billItem->bill_item_id)->delete();

                if (is_array($request->tooth_id)) {
                    foreach ($request->tooth_id as $toothId) {
                        if (empty($toothId)) {
                            continue;
                        } // 🧠 skip ghost teeth
                        BillItemTooth::create([
                            'bill_item_tooth_id' => Str::uuid(),
                            'bill_item_id' => $billItem->bill_item_id,
                            'tooth_list_id' => $toothId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

            }

            // 4. Update Treatment
            $treatment->status = $validated['status'];
            $treatment->save();

            // 5. Update or create note
            if (! empty($validated['note'])) {
                Note::updateOrCreate(
                    [
                        'patient_treatment_id' => $treatment->patient_treatment_id,
                        'note_type' => 'treatment',
                    ],
                    [
                        'note_id' => Str::uuid(),
                        'account_id' => $authAccount->account_id,
                        'patient_id' => $validated['patient_id'],
                        'summary' => Str::limit($validated['note'], 100, '...'),
                        'note' => $validated['note'],
                        'updated_at' => now(),
                    ]
                );
            }

            // 6. Logging
            LogService::record(
                $authAccount,
                $treatment,
                'update',
                'Treatment',
                'User updated a treatment',
                "Patient ID: {$validated['patient_id']} | Treatment ID: {$treatment->patient_treatment_id}",
                $request->ip(),
                $request->userAgent()
            );

            return back()->with('success', 'Treatment updated successfully.');
        });
    }
}
