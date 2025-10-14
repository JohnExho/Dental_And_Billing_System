<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Certificate;
use App\Models\Note;
use App\Models\Recall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProgressNoteController extends Controller
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
            'associate_id' => 'nullable|uuid|exists:associates,associate_id',
            'service' => 'required|uuid|exists:services,service_id',
            'tooth_id' => 'nullable|uuid|exists:tooth_list,tooth_list_id',
            'discount' => 'nullable|numeric|min:0|max:100',
            'remarks' => 'nullable|string',
            'visit_date' => 'required|date',
            'followup_date' => 'nullable|date',
            'followup_reason' => 'nullable|string',
            'attachments' => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $request) {
            $authAccount = $this->guard->user();
            $clinicId = session('clinic_id') ?? $authAccount->clinic_id;

            $remarks = $request->remarks ?? '';

            $summary = $remarks
                ? Str::limit($remarks, 50, '...')
                : 'Progress Note';

            // ✅ 1. Create NOTE
            $note = Note::create([
                'note_id' => Str::uuid(),
                'account_id' => $authAccount->account_id,
                'patient_id' => $validated['patient_id'],
                'associate_id' => $validated['associate_id'] ?? null,
                'patient_visit_id' => null,
                'note_type' => 'progress',
                'summary' => $summary,
                'note' => $remarks,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ 2. Compute monetary values
            $discountPercent = $validated['discount'] ?? 0;
            $totalServiceAndTooth = (float) $request->total_cost ?? 0;
            $discountAmount = $totalServiceAndTooth * ($discountPercent / 100);
            $netAmount = $totalServiceAndTooth - $discountAmount;

            // ✅ 3. Create BILL
            $bill = Bill::create([
                'bill_id' => Str::uuid(),
                'account_id' => $authAccount->account_id,
                'patient_id' => $validated['patient_id'],
                'associate_id' => $validated['associate_id'] ?? null,
                'clinic_id' => $clinicId,
                'laboratory_id' => null,
                'patient_visit_id' => null,
                'amount' => $totalServiceAndTooth,
                'discount' => $discountAmount,
                'total_amount' => $netAmount,
                'status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ 4. Create BILL ITEM (service + optional tooth)
            BillItem::create([
                'bill_item_id' => Str::uuid(),
                'bill_id' => $bill->bill_id,
                'account_id' => $authAccount->account_id,
                'item_type' => 'service',
                'medicine_id' => null,
                'service_id' => $validated['service'],
                'tooth_id' => $validated['tooth_id'] ?? null,
                'amount' => $netAmount, // or the original amount if needed
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ 5. Recall (if follow-up date is provided)
            if (! empty($validated['followup_date'])) {
                Recall::create([
                    'recall_id' => Str::uuid(),
                    'account_id' => $authAccount->account_id,
                    'patient_id' => $validated['patient_id'],
                    'associate_id' => $validated['associate_id'] ?? null,
                    'patient_visit_id' => null,
                    'recall_date' => $validated['followup_date'],
                    'recall_reason' => $validated['followup_reason'] ?? null,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ✅ 6. Certificates (if file is uploaded)
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('certificates', $filename, 'public');

                Certificate::create([
                    'certificate_id' => Str::uuid(),
                    'account_id' => $authAccount->account_id,
                    'patient_id' => $validated['patient_id'],
                    'associate_id' => $validated['associate_id'] ?? null,
                    'clinic_id' => $clinicId,
                    'patient_visit_id' => null,
                    'certificate_type' => 'Medical Certificate',
                    'certificate_details' => $request->remarks ?? '',
                    'issued_at' => now(),
                    'file_path' => $path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return back()->with('success', 'Progress note and billing added successfully.');
        });
    }
}
