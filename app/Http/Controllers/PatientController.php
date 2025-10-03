<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Patient;
use Illuminate\Support\Str;
use App\Services\LogService;
use Illuminate\Http\Request;
use Yajra\Address\Entities\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\Province;
use Illuminate\Support\Facades\Validator; // if you use logs

class PatientController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {

        $clinicId = session('clinic_id');
        $query = Patient::with([
            'clinic',
            'account',
            // 'patientQR',
            'address.barangay',
            'address.city',
            'address.province',
        ])->latest();

        if (session()->has('clinic_id') && $clinicId = session('clinic_id')) {
            $query->where('clinic_id', $clinicId);
        }

        $patients = $query->paginate(8);

        return view('pages.patients.index', compact('patients', 'clinicId'));
    }

    public function create(Request $request)
    {
        // ✅ Step 1: Manual validation using Validator
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'mobile_no' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'sex' => 'required|string|max:20',
            'civil_status' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date',
            'referral' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'school' => 'nullable|string|max:255',
            'clinic_id' => 'nullable|exists:clinics,clinic_id',
            'address' => 'nullable|array',
            'address.house_no' => 'nullable|string|max:50',
            'address.street' => 'nullable|string|max:255',
            'address.barangay_id' => 'nullable|exists:barangays,id',
            'address.city_id' => 'nullable|exists:cities,id',
            'address.province_id' => 'nullable|exists:provinces,id',
        ]);

        // ✅ Return errors if validation fails
        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // ✅ Extract validated values
        $validated = $validator->validated();

        // ✅ Step 2: Normalize + Hash email
        $normalizedEmail = $validated['email'] ? strtolower($validated['email']) : null;
        $newEmailHash = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;

        // ✅ Step 3: Prevent duplicate email
        if (
            $newEmailHash &&
            Patient::where('email_hash', $newEmailHash)
                ->withoutTrashed()
                ->exists()
        ) {
            return back()->with('error', 'The email has already been taken.');
        }

        // ✅ Step 4: Create inside transaction
        return DB::transaction(function () use ($validated, $normalizedEmail, $newEmailHash, $request) {

            $authAccount = $this->guard->user();

            // Auto-assign clinic_id from auth account if present
            $clinicId = $authAccount->clinic_id ?? ($validated['clinic_id'] ?? null);

            // ✅ Create patient
            $patient = Patient::create([
                'patient_id' => (string) Str::uuid(),
                'account_id' => $authAccount->account_id,
                'clinic_id' => $clinicId,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'last_name_hash' => hash('sha256', strtolower($validated['last_name'])),
                'mobile_no' => $validated['mobile_no'] ?? null,
                'contact_no' => $validated['contact_no'] ?? null,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
                'sex' => $validated['sex'],
                'civil_status' => $validated['civil_status'] ?? null,
                'date_of_birth' => $validated['date_of_birth'],
                'referral' => $validated['referral'] ?? null,
                'occupation' => $validated['occupation'] ?? null,
                'company' => $validated['company'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'height' => $validated['height'] ?? null,
                'school' => $validated['school'] ?? null,
            ]);

            // Step 2: Create Address (if provided)
            if ($request->filled('address')) {
                Address::create([
                    'patient_id' => $patient->patient_id,
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

            // Step 3: Logging
            $addressId = optional($patient->address)->address_id;

            // ✅ Log
            LogService::record(
                $authAccount,
                $patient,
                'create',
                'patient',
                'User created a patient record',
                'Patient: '.$patient->patient_id . 'Address: ' . $addressId ,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Patient created successfully.');
        });
    }
}
