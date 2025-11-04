<?php

namespace App\Http\Controllers;

use App\Models\Recall;
use Illuminate\Support\Str;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RecallController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function create(Request $request)
    {
        // 1. Validate the incoming form data
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,patient_id',
            'followup_date' => 'required|date|after_or_equal:today',
            'followup_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput();
        }

        // 2. Get the logged-in user
        $authAccount = $this->guard->user();

        // 3. Create the recall record
        $recall = Recall::create([
            'recall_id' => Str::uuid(),
            'account_id' => $authAccount->account_id,
            'patient_id' => $request->patient_id,
            'associate_id' => $authAccount->associate_id ?? null, // optional, if applicable
            'recall_date' => $request->followup_date,
            'recall_reason' => $request->followup_reason,
            'status' => 'Pending', // default status, you can modify this
        ]);


        LogService::record(
            $authAccount,
            $recall,
            'create',
            'Recalls',
            'User has created a new recall',
            "Recall Date: {$recall->recall_date->format('Y-m-d')} | Reason: {$recall->recall_reason}",
            $request->ip(),
            $request->userAgent()
        );

        // 5. Redirect with success message
        return redirect()->back()->with('success', 'Recall created successfully.');
    }

    public function update(Request $request)
    {
        // 1. Validate the incoming form data
        $validator = Validator::make($request->all(), [
            'recall_id' => 'required|uuid|exists:recalls,recall_id',
            'recall_reason' => 'required|string|max:500',
            'status' => 'required|in:Pending,Completed,Cancelled',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput();
        }

        // 2. Get the logged-in user
        $authAccount = $this->guard->user();

        // 3. Fetch the recall record
        $recall = Recall::findOrFail($request->recall_id);

        // 4. Update the recall record
        $recall->recall_reason = $request->recall_reason;
        $recall->status = $request->status;
        $recall->save();

        LogService::record(
            $authAccount,
            $recall,
            'update',
            'Recalls',
            'User has updated a recall',
            "Recall ID: {$recall->recall_id} | New Reason: {$recall->recall_reason} | New Status: {$recall->status}",
            $request->ip(),
            $request->userAgent()
        );

        // 5. Redirect with success message
        return redirect()->back()->with('success', 'Recall updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'recall_id' => 'required|exists:recalls,recall_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $recall = Recall::findOrFail($request->recall_id);

        // Check if the password matches the current user's password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($recall, $request, $deletor) {

            // Delete patient record
            $recall->delete();

            // Logging
            LogService::record(
                $deletor,
                $recall,
                'delete',
                'recall',
                'User deleted a progress recall',
                'recall ID: '.$recall->recall_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('specific-patient', ['id' => $recall->patient_id])->with('success', 'Patient recall deleted successfully.');
        });
    }
}
