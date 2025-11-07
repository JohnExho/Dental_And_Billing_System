<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Bill;
use App\Models\Clinic;
use App\Models\Note;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Recall;
use App\Models\Treatment;
use App\Services\LogService;
use App\Traits\RegexPatterns;
use App\Traits\ValidationMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province; // if you use logs

class PatientController extends Controller
{
    use RegexPatterns;
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

        $query = Patient::query();

        if ($clinicId) {
            $query->where('clinic_id', $clinicId);
        }

        $patientCount = $query->count(); // ✅ Count after filter

        $patients = $query->with([
            'clinic',
            'account',
            'address.barangay',
            'address.city',
            'address.province',
        ])->latest()->paginate(8);

        return view('pages.patients.index', compact('patients', 'clinicId', 'patientCount'));
    }

    public function create(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:'.self::namePattern(),
                ],
                'middle_name' => [
                    'nullable',
                    'string',
                    'max:100',
                    'regex:'.self::namePattern(),
                ],
                'last_name' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:'.self::namePattern(),
                ],
                'mobile_no' => 'nullable|string|max:20',
                'contact_no' => 'nullable|string|max:20',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            // Split local part from domain
                            $local = explode('@', $value)[0];

                            // Check if local part is only digits
                            if (preg_match('/^\d+$/', $local)) {
                                $fail('Email cannot contain only numbers before the @ symbol.');
                            }
                        }
                    },
                ],
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
            ],
            self::messages()
        );

        // ✅ Return errors if validation fails
        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // ✅ Extract validated values
        $validated = $validator->validated();

        // ✅ Step 2: Normalize + Hash email
        $normalizedEmail = $validated['email'] ? strtolower($validated['email']) : null;
        $newEmailHash = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('profile_pictures', 'public'); // stored in storage/app/public/profile_pictures
        } else {
            $path = null;
        }

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
        return DB::transaction(function () use ($validated, $normalizedEmail, $newEmailHash, $request, $path) {

            $authAccount = $this->guard->user();

            // Auto-assign clinic_id from auth account if present
            $clinicId = session('clinic_id') ?? $authAccount->clinic_id ?? ($validated['clinic_id'] ?? null);

            // Validate again to be safe
            if (! $clinicId || ! Clinic::find($clinicId)) {
                return back()->with('error', 'Select a Clinic First.');
            }

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
                'profile_picture' => $path,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
                'sex' => $validated['sex'],
                'civil_status' => $validated['civil_status'] ?? 'Single',
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
                'Patient: '.$patient->patient_id.'Address: '.$addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect(route('patients'))->with('success', 'Patient created successfully.');
        });
    }

    public function update(Request $request)
    {

        // Step 1: Validate
        $validator = Validator::make(
            $request->all(),
            [
                'patient_id' => 'required|exists:patients,patient_id',
                'first_name' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:'.self::namePattern(),
                ],
                'middle_name' => [
                    'nullable',
                    'string',
                    'max:100',
                    'regex:'.self::namePattern(),
                ],
                'last_name' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:'.self::namePattern(),
                ],
                'mobile_no' => 'nullable|string|max:20',
                'contact_no' => 'nullable|string|max:20',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            // Split local part from domain
                            $local = explode('@', $value)[0];

                            // Check if local part is only digits
                            if (preg_match('/^\d+$/', $local)) {
                                $fail('Email cannot contain only numbers before the @ symbol.');
                            }
                        }
                    },
                ],
                'sex' => 'required|string|max:20',
                'civil_status' => 'nullable|string|max:50',
                'date_of_birth' => 'required|date',
                'referral' => 'nullable|string|max:255',
                'occupation' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric',
                'height' => 'nullable|numeric',
                'school' => 'nullable|string|max:255',
                'address' => 'nullable|array',
                'address.house_no' => 'nullable|string|max:50',
                'address.street' => 'nullable|string|max:255',
                'address.barangay_id' => 'nullable|exists:barangays,id',
                'address.city_id' => 'nullable|exists:cities,id',
                'address.province_id' => 'nullable|exists:provinces,id',
            ],
            self::messages()
        );

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();
        $patient = Patient::findOrFail($validated['patient_id']);
        $normalizedEmail = $validated['email'] ? strtolower($validated['email']) : null;
        $newEmailHash = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;

        // Step 2: Check for duplicate email excluding this patient
        if ($newEmailHash && Patient::where('email_hash', $newEmailHash)
            ->where('patient_id', '!=', $patient->patient_id)
            ->withoutTrashed()
            ->exists()
        ) {
            return back()->with('error', 'The email has already been taken.');
        }

        // Step 3: Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('profile_pictures', 'public');
            // Optional: delete old picture
            if ($patient->profile_picture) {
                Storage::disk('public')->delete($patient->profile_picture);
            }
            $patient->profile_picture = $path;
        }

        return DB::transaction(function () use ($patient, $validated, $normalizedEmail, $newEmailHash, $request) {

            $authAccount = $this->guard->user();

            // Update patient fields
            $patient->update([
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

            // Update or create address
            if ($request->filled('address')) {
                $addressData = [
                    'house_no' => $request->address['house_no'] ?? null,
                    'street' => $request->address['street'] ?? null,
                    'barangay_name' => optional(Barangay::find($request->address['barangay_id']))->name,
                    'city_name' => optional(City::find($request->address['city_id']))->name,
                    'province_name' => optional(Province::find($request->address['province_id']))->name,
                    'barangay_id' => $request->address['barangay_id'] ?? null,
                    'city_id' => $request->address['city_id'] ?? null,
                    'province_id' => $request->address['province_id'] ?? null,
                ];
                if ($patient->address) {
                    $patient->address->update($addressData);
                } else {
                    $addressData['patient_id'] = $patient->patient_id;
                    Address::create($addressData);
                }
            }

            if ($request->boolean('remove_profile_picture')) {
                // Delete old file if exists
                if ($patient->profile_picture && Storage::disk('public')->exists($patient->profile_picture)) {
                    Storage::disk('public')->delete($patient->profile_picture);
                }

                // Nullify column
                $patient->update(['profile_picture' => null]);
            }

            // Logging
            LogService::record(
                $authAccount,
                $patient,
                'update',
                'patient',
                'User updated a patient record',
                'Patient: '.$patient->patient_id.' Address: '.optional($patient->address)->address_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Patient updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $patient = Patient::findOrFail($request->patient_id);

        // Check if the password matches the current user's password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($patient, $request, $deletor) {

            $addressId = optional($patient->address)->address_id;

            // Delete related address
            $patient->address()->delete();
            $patient->waitlist()->delete();

            // Delete patient record
            $patient->delete();

            // Logging
            LogService::record(
                $deletor,
                $patient,
                'delete',
                'patient',
                'User deleted a patient record',
                'Patient ID: '.$patient->patient_id
                    .', address ID: '.$addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('patients')->with('success', 'Patient record deleted successfully.');
        });
    }

    public function specific(Request $request)
    {
        $clinicId = session('clinic_id');

        // If patient_id is provided in the URL (first time)
        if ($request->has('patient_id')) {
            session(['current_patient_id' => $request->patient_id]);

            return redirect()->route('specific-patient');
        }

        // Retrieve from session
        $patientId = session('current_patient_id');

        if (! $patientId) {
            abort(404, 'No patient selected.');
        }

        // Fetch patient with relationships
        $patient = Patient::with(['address.barangay', 'address.city', 'address.province', 'clinic'])
            ->findOrFail($patientId);

        // Redirect back if patient clinic_id is null or different
        if (! $patient->clinic_id || $patient->clinic_id != $clinicId) {
            return redirect()->route('patients')->with('error', 'Patient does not belong to your clinic.');
        }
        // Fetch progress notes for this patient
        $progressNotes = Note::with(['account', 'clinic'])
            ->where('patient_id', $patientId)
            ->where('note_type', 'progress')
            ->latest()
            ->paginate(8);

        $bills = Bill::with([
            'billItems.service',
            'billItems.teeth', // Load the many-to-many teeth relationship
            'account',
            'patient',
        ])->paginate(8);

        $recalls = Recall::with(['account'])
            ->where('patient_id', $patientId)
            ->latest()
            ->paginate(8);

        $treatments = Treatment::with([
            'account',
            'clinic',
            'visit',
            'bill.billItemTooths' => fn ($q) => $q->whereNull('deleted_at'), // 🚀 ignore deleted pivot rows
            'bill.billItemTooths.tooth',
        ])
            ->where('patient_id', $patientId)
            ->where('clinic_id', $clinicId)
            ->latest()
            ->paginate(8);

        $prescriptions = Prescription::with(['account', 'clinic', 'visit'])
            ->where('patient_id', $patientId)
            ->where('clinic_id', $clinicId)
            ->latest()
            ->paginate(8);

        return view('pages.patients.specific', compact('patient', 'progressNotes', 'bills', 'recalls', 'treatments', 'prescriptions'));
    }
}
