<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Clinic;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;

class ClinicController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {
        $clinics = Clinic::with('address.barangay', 'address.city', 'address.province')
            ->latest()
            ->paginate(4);

        return view('pages.clinics.index', compact('clinics'));
    }

    public function create(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'speciality' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'schedules' => 'nullable|array',
            'schedule_summary' => 'required|string|max:255',
            'schedules.*.day_of_week' => 'required|string',
            'schedules.*.start_time' => 'required|date_format:H:i:s',
            'schedules.*.end_time' => 'required|date_format:H:i:s',
            'address' => 'nullable|array',
            'address.house_no' => 'nullable|string|max:50',
            'address.street' => 'nullable|string|max:255',
            'address.barangay_id' => 'nullable|exists:barangays,id',
            'address.city_id' => 'nullable|exists:cities,id',
            'address.province_id' => 'nullable|exists:provinces,id',
        ]);

        // Prevent duplicate email
        $normalizedEmail = $request->email ? strtolower($request->email) : null;
        $newEmailHash = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;

        if (
            $newEmailHash && Clinic::where('email_hash', $newEmailHash)
                ->whereNull('deleted_at') // ignore soft-deleted
                ->exists()
        ) {
            return redirect()->back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request, $normalizedEmail, $newEmailHash) {
            $account = $this->guard->user();
            Log::info($request->all());

            // Step 1: Create Clinic
            $clinic = Clinic::create([
                'clinic_id' => Str::uuid(),
                'account_id' => $account->account_id,
                'name' => $request->name,
                'name_hash' => $request->name_hash = hash('sha256', strtolower($request->name)),
                'description' => $request->description,
                'schedule_summary' => $request->schedule_summary ?: 'No schedule yet',
                'speciality' => $request->speciality,
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
            ]);

            // Step 2: Create Schedules (if any)
            $schedules = [];
            foreach ($request->schedule ?? [] as $day => $data) {
                if (! empty($data['active'])) {
                    $schedules[] = [
                        'clinic_schedule_id' => Str::uuid(),
                        'day_of_week' => $day,
                        'start_time' => $data['start'] ?? null,
                        'end_time' => $data['end'] ?? null,
                    ];
                }
            }

            if (! empty($schedules)) {
                $clinic->clinicSchedules()->createMany($schedules);
            }

            // Step 3: Create Address (if provided)
            if ($request->filled('address')) {
                Address::create([
                    'account_id' => $account->account_id,
                    'clinic_id' => $clinic->clinic_id,
                    'house_no' => $request->address['house_no'] ?? null,
                    'street' => $request->address['street'] ?? null,
                    'barangay_name' => optional(Barangay::find($request->address['barangay_id']))->name,
                    'city_name' => optional(City::find($request->address['city_id']))->name,
                    'province_name' => optional(Province::find($request->address['province_id']))->name,
                    'barangay_id' => $request->address['barangay_id'] ?? null,
                    'city_id' => $request->address['city_id'] ?? null,
                    'province_id' => $request->address['province_id'] ?? null,
                ]);
            }

            // Step 4: Logging
            $scheduleIds = $clinic->clinicSchedules->pluck('clinic_schedule_id')->all();
            $addressId = optional($clinic->address)->address_id;

            Logs::record(
                $account,
                $clinic,
                null,
                null,
                'create',
                'clinic',
                'User created a clinic',
                'clinic: '.$clinic->clinic_id
                    .', address: '.$addressId
                    .', schedules: '.json_encode($scheduleIds),
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('clinics')->with('success', 'Clinic created successfully.');
        });
    }

    public function update(Request $request, Clinic $clinic)
    {
        // Validation
        $request->validate([
            'clinic_id' => 'required|exists:clinics,clinic_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'speciality' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|regex:/^[A-Za-z][A-Za-z0-9._%+-]*@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
            'schedule_summary' => 'nullable|string|max:255',
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|string',
            'schedules.*.start_time' => 'required|date_format:H:i:s',
            'schedules.*.end_time' => 'required|date_format:H:i:s',
            'address' => 'nullable|array',
            'address.house_no' => 'nullable|string|max:50',
            'address.street' => 'nullable|string|max:255',
            'address.barangay_id' => 'nullable',
            'address.city_id' => 'nullable',
            'address.province_id' => 'nullable',
        ]);

        $account = Auth::guard('account')->user();
        $clinic = Clinic::findOrFail($request->clinic_id);

        $normalizedEmail = $request->email ? strtolower($request->email) : null;
        $newEmailHash = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;

        // Prevent duplicate email
        if (
            $normalizedEmail && Clinic::where('email_hash', $newEmailHash)
                ->where('clinic_id', '!=', $clinic->clinic_id)
                ->exists()
        ) {
            return redirect()->route('clinics')->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request, $clinic, $account, $normalizedEmail, $newEmailHash) {

            // --- Update clinic basic info ---
            $clinic->update([
                'name' => $request->name,
                'name_hash' => $request->name_hash = hash('sha256', strtolower($request->name)),
                'description' => $request->description,
                'speciality' => $request->speciality,
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
                'schedule_summary' => $request->schedule_summary,
            ]);

            // --- Handle schedules ---
            $incomingSchedules = $request->schedule ?? [];

            // Delete schedules not in the request or inactive
            $activeDays = array_keys(array_filter($incomingSchedules, fn ($d) => ! empty($d['active'])));
            $clinic->clinicSchedules()
                ->whereNotIn('day_of_week', $activeDays)
                ->delete();

            // Update or create active schedules
            foreach ($incomingSchedules as $day => $data) {
                if (! empty($data['active'])) {
                    $clinic->clinicSchedules()->updateOrCreate(
                        ['clinic_id' => $clinic->clinic_id, 'day_of_week' => $day],
                        ['start_time' => $data['start'], 'end_time' => $data['end']]
                    );
                }
            }

            // --- Update address ---
            if ($request->filled('address')) {
                $clinic->address()->updateOrCreate(
                    ['clinic_id' => $clinic->clinic_id],
                    [
                        'account_id' => $account->account_id,
                        'house_no' => $request->address['house_no'] ?? null,
                        'street' => $request->address['street'] ?? null,
                        'barangay_name' => optional(Barangay::find($request->address['barangay_id']))->name,
                        'city_name' => optional(City::find($request->address['city_id']))->name,
                        'province_name' => optional(Province::find($request->address['province_id']))->name,
                        'barangay_id' => $request->address['barangay_id'] ?? null,
                        'city_id' => $request->address['city_id'] ?? null,
                        'province_id' => $request->address['province_id'] ?? null,
                    ]
                );
            }

            $scheduleIds = $clinic->clinicSchedules()->pluck('clinic_schedule_id')->all();
            $addressId = optional($clinic->address)->address_id;

            Logs::record(
                $account,
                $clinic,
                null,
                null,
                'update',
                'clinic',
                'User updated a clinic',
                'clinic: '.$clinic->clinic_id
                    .', address: '.$addressId
                    .', schedules: '.json_encode($scheduleIds),
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('clinics')->with('success', 'Clinic updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'clinic_id' => 'required|exists:clinics,clinic_id',
            'password' => 'required',
        ]);

        $account = Auth::guard('account')->user();
        $clinic = Clinic::findOrFail($request->clinic_id);

        if (! Hash::check($request->password, $account->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($clinic, $account, $request) {

            $scheduleIds = $clinic->clinicSchedules()->pluck('clinic_schedule_id')->all();
            $addressId = optional($clinic->address)->address_id;

            // Delete schedules & address
            $clinic->clinicSchedules()->delete();
            $clinic->address()->delete();
            // Delete clinic
            $clinic->delete();

            // Logging
            Logs::record(
                $account,
                $clinic,
                null,
                null,
                'delete',
                'clinic',
                'User deleted a clinic',
                'clinic: '.$clinic->clinic_id
                    .', address: '.$addressId
                    .', schedules: '.json_encode($scheduleIds),
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('clinics')->with('success', 'Clinic deleted successfully.');
        });
    }

    public function select(Request $request)
    {
        $clinicId = $request->input('clinic_id');

        // Optionally validate it exists
        if (! \App\Models\Clinic::where('clinic_id', $clinicId)->exists()) {
            return redirect()->back()->with('error', 'Invalid clinic selection.');
        }

        session(['clinic_id' => $clinicId]);

        return redirect()->route('admin.dashboard')->with('success', 'Clinic selected successfully.');
    }
}
