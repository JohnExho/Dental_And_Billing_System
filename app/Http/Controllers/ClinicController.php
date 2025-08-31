<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Clinic;
use App\Models\Address;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ClinicSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            ->take(8)
            ->get();


        return view('pages.clinics.index', compact('clinics'));
    }

    public function create(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specialty' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'schedules' => 'nullable|array',
            'schedule_summary' => 'nullable|string|max:255',
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
        if ($request->email && Clinic::where('email_hash', hash('sha256', $request->email))->exists()) {
            return redirect()->route('clinics')->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request) {
            $account = $this->guard->user();

            // Step 1: Create Clinic
            $clinic = Clinic::create([
                'clinic_id' => Str::uuid(),
                'account_id' => $account->account_id,
                'name' => $request->name,
                'description' => $request->description,
                'specialty' => $request->specialty,
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'email_hash' => $request->email ? hash('sha256', $request->email) : null,
            ]);

            // Step 2: Create Schedules (if any)
            $schedules = [];
            foreach ($request->schedule ?? [] as $day => $data) {
                if (!empty($data['active'])) {
                    $schedules[] = [
                        'clinic_schedule_id' => Str::uuid(),
                        'schedule_summary' => $request->schedule_summary ?? null,
                        'day_of_week' => $day,
                        'start_time' => $data['start'] ?? null,
                        'end_time' => $data['end'] ?? null,
                    ];
                }
            }

            if (!empty($schedules)) {
                $clinic->clinicSchedules()->createMany($schedules);
            }


            if (!empty($schedules)) {
                $clinic->clinicSchedules()->createMany($schedules);
            }

            // Step 3: Create Address (if provided)
            if ($request->filled('address')) {
                Address::create([
                    'account_id' => $account->account_id,
                    'clinic_id' => $clinic->clinic_id,
                    'house_no' => $request->address['house_no'] ?? null,
                    'street' => $request->address['street'] ?? null,
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
                'create',
                'clinic',
                'User created a clinic',
                'clinic: ' . $clinic->clinic_id
                    . ', address: ' . $addressId
                    . ', schedules: ' . json_encode($scheduleIds),
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('clinics')->with('success', 'Clinic created successfully.');
        });
    }
}
