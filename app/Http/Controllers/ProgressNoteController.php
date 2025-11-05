<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Note;
use App\Models\PatientVisit;
use App\Models\Recall;
use App\Models\Treatment;
use App\Models\BillItemTooth;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\ValidationMessages;

class ProgressNoteController extends Controller
{
    use ValidationMessages;
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,patient_id',
            'service' => 'required|uuid|exists:services,service_id',
            'tooth_id' => 'nullable|array',
            'tooth_id.*' => 'nullable|uuid|exists:tooth_list,tooth_list_id',
            'remarks' => 'nullable|string',
            'visit_date' => 'required|date',
            'followup_date' => 'nullable|date',
            'followup_reason' => 'nullable|string',
            'net_cost' => 'nullable',
        ], self::messages());

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $request) {
            $authAccount = $this->guard->user();
            $clinicId = session('clinic_id') ?? $authAccount->clinic_id;

            $remarks = $request->remarks ?? '';

            $summary = $remarks
                ? Str::limit($remarks, 100, '...')
                : 'Unknown';

            $patientVisit = PatientVisit::create([
                'patient_visit_id' => Str::uuid(),
                'account_id' => $authAccount->account_id,
                'clinic_id' => $clinicId,
                'patient_id' => $validated['patient_id'],
                'waitlist_id' => null,
                'visit_date' => $validated['visit_date'],
            ]);

            // ✅ 1. Create NOTE
            $note = Note::create([
                'note_id' => Str::uuid(),
                'account_id' => $authAccount->account_id,
                'patient_id' => $validated['patient_id'],
                'patient_visit_id' => $patientVisit->patient_visit_id,
                'note_type' => 'progress',
                'summary' => $summary,
                'note' => $remarks,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ 2. Compute monetary values
            $totalServiceAndTooth = (float) ($request->net_cost ?? 0);
            Log::info('Computed net_cost: ' . $totalServiceAndTooth);

            // ✅ 3. Try to find an existing unpaid bill for the patient
            $bill = Bill::where('patient_id', $validated['patient_id'])
                ->where('status', 'unpaid')
                ->where('clinic_id', $clinicId)
                ->first();

            // ✅ 4. If none exists, create a new one

            if (! $bill) {
                $bill = Bill::create([
                    'bill_id' => Str::uuid(),
                    'account_id' => $authAccount->account_id,
                    'patient_id' => $validated['patient_id'],
                    'associate_id' => $validated['associate_id'] ?? null,
                    'clinic_id' => $clinicId,
                    'patient_visit_id' => $patientVisit->patient_visit_id,
                    'amount' => 0,
                    'total_amount' => 0,
                    'status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Log::info('tooth id(s): ' . json_encode($request->tooth_id));
            // ✅ 5. Create BILL ITEM (for the service)
            $billItem = BillItem::create([
                'bill_item_id' => Str::uuid(),
                'bill_id' => $bill->bill_id,
                'account_id' => $authAccount->account_id,
                'item_type' => 'service',
                'medicine_id' => null,
                'service_id' => $validated['service'],
                'amount' => $totalServiceAndTooth,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // If multiple teeth were selected, create BillItemTooth rows
            if (! empty($validated['tooth_id']) && is_array($validated['tooth_id'])) {
                foreach ($validated['tooth_id'] as $toothId) {
                    if (empty($toothId)) continue;
                    BillItemTooth::create([
                        'bill_item_tooth_id' => Str::uuid(),
                        'bill_item_id' => $billItem->bill_item_id,
                        'tooth_list_id' => $toothId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            // ✅ 6. Update bill totals
            $bill->amount += $totalServiceAndTooth;
            $bill->total_amount += $totalServiceAndTooth;
            $bill->save();

            if ($bill->save()) {
                Treatment::create([
                    'treatment_id' => Str::uuid(),
                    'patient_visit_id' => $patientVisit->patient_visit_id,
                    'account_id' => $authAccount->account_id,
                    'patient_id' => $validated['patient_id'],
                    'clinic_id' => $clinicId,
                    'bill_item_id' => $billItem->bill_item_id,
                    'treatment_date' => now(),
                ]);
            }

            // ✅ 7. Recall (if needed)
            if (! empty($validated['followup_date'])) {
                Recall::create([
                    'recall_id' => Str::uuid(),
                    'account_id' => $authAccount->account_id,
                    'patient_id' => $validated['patient_id'],
                    'patient_visit_id' => $patientVisit->patient_visit_id,
                    'note_id' => $note->note_id,
                    'recall_date' => $validated['followup_date'],
                    'recall_reason' => $validated['followup_reason'] ?? null,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ✅ 8. Log activity
            LogService::record(
                $authAccount,
                $note,
                'create',
                'Progress Note',
                'User created a progress note with billing',
                "Patient ID: {$validated['patient_id']} | Note ID: {$note->note_id} | Amount: {$totalServiceAndTooth}",
                $request->ip(),
                $request->userAgent()
            );

            return back()->with('success', 'Progress note and billing added successfully.');
        });
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required|uuid|exists:notes,note_id',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $note = Note::findOrFail($request->note_id);

        $note->update([
            'note' => $request->remarks,
            'summary' => Str::limit($request->remarks, 50, '...'),
        ]);

        LogService::record(
            $this->guard->user(),
            $note,
            'update',
            'note',
            'User updated a progress note',
            'Note ID: '.$note->note_id,
            $request->ip(),
            $request->userAgent()
        );

        return back()->with('success', 'Progress note updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'note_id' => 'required|exists:notes,note_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $note = Note::findOrFail($request->note_id);

        // Check if the password matches the current user's password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($note, $request, $deletor) {

            $note->patientVisit()->delete();
            $note->recall()->delete();
            // Delete patient record
            $note->delete();

            // Logging
            LogService::record(
                $deletor,
                $note,
                'delete',
                'note',
                'User deleted a progress note',
                'Note ID: '.$note->note_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('specific-patient', ['id' => $note->patient_id])->with('success', 'Patient note deleted successfully.');
        });
    }
}
