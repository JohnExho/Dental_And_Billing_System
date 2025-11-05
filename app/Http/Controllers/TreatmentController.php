<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\BillItem;
use App\Models\BillItemTooth;
use App\Models\Note;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            'tooth_ids' => 'nullable|array',
            'tooth_ids.*' => 'nullable|uuid|exists:tooth_list,tooth_list_id',
            'note' => 'required|string',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        try {
            return DB::transaction(function () use ($request) {
                $authAccount = $this->guard->user();
                $clinicId = session('clinic_id') ?? $authAccount->clinic_id;

                // Create a new bill item for the treatment
                $billItem = BillItem::create([
                    'bill_item_id' => Str::uuid(),
                    'account_id' => $authAccount->account_id,
                    'item_type' => 'service',
                    'service_id' => $request->procedure_id,
                    'amount' => 0, // Will be updated based on clinic prices
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create bill item tooth entries if teeth were selected
                if (!empty($request->tooth_ids)) {
                    foreach ($request->tooth_ids as $toothId) {
                        BillItemTooth::create([
                            'bill_item_tooth_id' => Str::uuid(),
                            'bill_item_id' => $billItem->bill_item_id,
                            'tooth_list_id' => $toothId,
                        ]);
                    }
                }

                // Create a note for the treatment
                $note = Note::create([
                    'note_id' => Str::uuid(),
                    'account_id' => $authAccount->account_id,
                    'patient_id' => $request->patient_id,
                    'summary' => Str::limit($request->note, 100, '...'),
                    'note' => $request->note,
                    'note_type' => 'treatment'
                ]);

                // Create the treatment record
                $treatment = Treatment::create([
                    'treatment_id' => Str::uuid(),
                    'patient_id' => $request->patient_id,
                    'account_id' => $authAccount->account_id,
                    'bill_item_id' => $billItem->bill_item_id,
                    'clinic_id' => $clinicId,
                    'status' => $request->status,
                    'treatment_date' => now(),
                ]);

                // Log the activities
                LogService::record(
                    $authAccount,
                    $treatment,
                    'create',
                    'Treatment',
                    'User created a new treatment',
                    "Patient ID: {$request->patient_id} | Treatment ID: {$treatment->treatment_id}",
                    $request->ip(),
                    $request->userAgent()
                );

                LogService::record(
                    $authAccount,
                    $note,
                    'create',
                    'Treatment Note',
                    'User created a treatment note',
                    "Patient ID: {$request->patient_id} | Note ID: {$note->note_id}",
                    $request->ip(),
                    $request->userAgent()
                );

                return back()->with('success', 'Treatment added successfully.');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add treatment. ' . $e->getMessage());
        }
    }
}
