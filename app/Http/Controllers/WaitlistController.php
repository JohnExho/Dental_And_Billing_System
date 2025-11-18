<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Waitlist;
use App\Services\LogService;
use App\Traits\ValidationMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WaitlistController extends Controller
{
    use ValidationMessages;

    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {
        $clinicId = session('clinic_id');

        if (! $clinicId) {
            return redirect(route('staff.dashboard'))->with('error', 'Select a clinic first.');
        }
        $query = Waitlist::with([
            'clinic',
            'account',
            'patient',
            'associate',
        ])->latest()->whereNotNull('clinic_id');

        if (session()->has('clinic_id') && $clinicId = session('clinic_id')) {
            $query->where('clinic_id', $clinicId);
        }

        $waitlist = $query->paginate(8);
        $patientCount = $query->count();

        return view('pages.waitlist.index', compact('waitlist', 'patientCount'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'patient_id' => 'required|uuid|exists:patients,patient_id',
                'associate_id' => 'nullable|uuid',
                'queue_position' => 'nullable|integer',
                'clinic_id' => 'required|uuid',
            ],
            self::messages()
        );

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $request) {

            $authAccount = $this->guard->user();

            $clinicId = session('clinic_id')
                ?? $authAccount->clinic_id
                ?? ($validated['clinic_id'] ?? null);

            if (! $clinicId || ! Clinic::find($clinicId)) {
                return back()->with('error', 'Select a Clinic First.');
            }

            // ✅ Get the latest record of this patient in this clinic
            $latest = Waitlist::where('patient_id', $validated['patient_id'])
                ->where('clinic_id', $clinicId)
                ->latest('created_at')
                ->first();

            // ✅ If there's a record and it's NOT finished, block
            if ($latest && $latest->status !== 'completed') {
                return back()->with('error', 'This patient is already in the waitlist.');
            }

            // ✅ Get today's start and end (PHT)
            $todayStart = Carbon::now('Asia/Manila')->startOfDay();
            $todayEnd = Carbon::now('Asia/Manila')->endOfDay();

            // ✅ Get queue position based on previous status
            $queuePosition = 0;
            if (! $latest || $latest->status === 'completed') {
                // Get last queue_position number for today
                $lastPosition = Waitlist::whereBetween('created_at', [$todayStart, $todayEnd])
                    ->max('queue_position');
                $queuePosition = $lastPosition ? $lastPosition + 1 : 1;
            } else {
                $queuePosition = $latest->queue_position;
            }

            // ✅ Create waitlist record
            $waitlist = Waitlist::create([
                'waitlist_id' => (string) Str::uuid(),
                'account_id' => $authAccount->account_id,
                'patient_id' => $validated['patient_id'],
                'clinic_id' => $clinicId,
                'associate_id' => $validated['associate_id'] ?? null,
                'requested_at_date' => now(),
                'requested_at_time' => now(),
                'queue_position' => $queuePosition,
                'queue_snapshot' => $queuePosition,
                'status' => 'waiting',
            ]);

            // ✅ Log
            LogService::record(
                $authAccount,
                $waitlist,
                'create',
                'waitlist',
                'User created a waitlist record',
                'Waitlist: '.$waitlist->waitlist_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('waitlist')
                ->with('success', 'Waitlist created successfully.');
        });
    }

    public function update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'waitlist_id' => 'required|uuid|exists:waitlist,waitlist_id',
                'associate_id' => 'nullable|uuid|exists:associates,associate_id',
                'status' => 'required|in:waiting,in_consultation,completed,finished',
            ],
            self::messages()
        );

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $request) {

            $authAccount = $this->guard->user();

            // ✅ Fetch waitlist by hidden field
            $waitlist = Waitlist::where('waitlist_id', $validated['waitlist_id'])->first();

            if ($validated['status'] === 'completed' || $validated['status'] === 'in_consultation') {
                // Get all waiting patients in this clinic today, ordered by queue_position
                $waitingPatients = Waitlist::where('clinic_id', $waitlist->clinic_id)
                    ->where('status', 'waiting')
                    ->whereDate('created_at', Carbon::today('Asia/Manila'))
                    ->orderBy('queue_position')
                    ->get();

                // Reassign queue positions sequentially
                $position = 0;
                foreach ($waitingPatients as $patient) {
                    $patient->queue_position = $position++;
                    $patient->save();
                }
            }

            if (! $waitlist) {
                return back()->with('error', 'Waitlist record not found.');
            }

            // ✅ Only update allowed fields
            $waitlist->associate_id = $validated['associate_id'] ?? null;
            $waitlist->status = $validated['status'] ?? $waitlist->status;
            $waitlist->save();

            // ✅ Log the update
            LogService::record(
                $authAccount,
                $waitlist,
                'update',
                'waitlist',
                'User updated a waitlist record',
                'Waitlist: '.$waitlist->waitlist_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('waitlist')
                ->with('success', 'Waitlist updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'waitlist_id' => 'required|exists:waitlist,waitlist_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $waitlist = Waitlist::findOrFail($request->waitlist_id);

        // Check if the password matches the current user's password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($waitlist, $request, $deletor) {

            $addressId = optional($waitlist->address)->address_id;

            // Delete patient record
            $waitlist->delete();

            // Logging
            LogService::record(
                $deletor,
                $waitlist,
                'delete',
                'patient',
                'User deleted a patient record',
                'Waitlist ID: '.$waitlist->waitlist_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('waitlist')->with('success', 'Waitlist record deleted successfully.');
        });
    }
}
