<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Clinic;
use App\Models\Account;
use App\Models\Address;
use App\Models\Associate;
use Illuminate\Support\Str;
use App\Services\LogService;
use Illuminate\Http\Request;
use Yajra\Address\Entities\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\Province;

class AssociateController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {
        $query = Associate::with([
            'clinic',
            'address.barangay',
            'address.city',
            'address.province',
        ])->latest();

        // If a clinic is selected → filter
        if (session()->has('clinic_id') && $clinicId = session('clinic_id')) {
            $query->where('clinic_id', $clinicId);
        }

        $associates = $query->paginate(8);

        return view('pages.associates.index', compact('associates'));
    }


    public function create(Request $request)
    {
        // Validation
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'speciality' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|array',
            'address.house_no' => 'nullable|string|max:50',
            'address.street' => 'nullable|string|max:255',
            'address.barangay_id' => 'nullable|exists:barangays,id',
            'address.city_id' => 'nullable|exists:cities,id',
            'address.province_id' => 'nullable|exists:provinces,id',
            'clinic_id' => 'nullable|exists:clinics,clinic_id',
        ]);

        // Prevent duplicate email

        $normalizedEmail = $request->email ? strtolower($request->email) : null;
        $newEmailHash = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;

        if (
            $newEmailHash && Associate::where('email_hash', $newEmailHash)
                ->whereNull('deleted_at')
                ->exists()
        ) {
            return redirect()->back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request, $newEmailHash, $normalizedEmail) {
            $authAccount = $this->guard->user();

            // Step 1: Create associate Account
            $associate = Associate::create([
                'associate_id' => (string) Str::uuid(),
                'account_id' => $authAccount->account_id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'last_name_hash' => hash('sha256', strtolower($request->last_name)),
                'speciality' => $request->speciality,
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
                'clinic_id' => $request->clinic_id,
            ]);

            // Step 2: Create Address (if provided)
            if ($request->filled('address')) {
                Address::create([
                    'associate_id' => $associate->associate_id,
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
            $addressId = optional($associate->address)->address_id;
            LogService::record(
                $authAccount,            // who did it
                $associate,            // what was acted on (loggable model, here the Account itself)
                'create',             // action
                'associate',              // log_type
                'User created an associate',
                'Associate: ' . $associate->associate_id
                . ', Address: ' . $addressId,
                $request->ip(),
                $request->userAgent()
            );
            return redirect()->route('associates')->with('success', 'associate created successfully.');
        });
    }

    public function update(Request $request, Associate $associate)
    {
        // Validation
        $request->validate([
            'associate_id' => 'required|exists:associates,associate_id',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'mobile_no' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|array',
            'address.house_no' => 'nullable|string|max:50',
            'address.street' => 'nullable|string|max:255',
            'address.barangay_id' => 'nullable|exists:barangays,id',
            'address.city_id' => 'nullable|exists:cities,id',
            'address.province_id' => 'nullable|exists:provinces,id',
            'is_active' => 'nullable|boolean',
        ]);

        $authAccount = $this->guard->user();
        $associate = Associate::findOrFail($request->associate_id);

        // Normalize + hash email
        $normalizedEmail = $request->email ? strtolower($request->email) : null;
        $newEmailHash = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;

        // Prevent duplicate email
        if (
            $newEmailHash && Associate::where('email_hash', $newEmailHash)
                ->where('associate_id', '!=', $associate->associate_id)
                ->whereNull('deleted_at')
                ->exists()
        ) {
            return redirect()->back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request, $associate, $authAccount, $normalizedEmail, $newEmailHash) {
            // Step 1: Update Associate
            $updateData = [
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'last_name_hash' => hash('sha256', strtolower($request->last_name)),
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
                'is_active' => $request->has('is_active') ? 1 : 0
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            $associate->update($updateData);

            // Step 2: Update Address (if provided)
            if ($request->filled('address')) {
                $associate->address()->updateOrCreate(
                    ['associate_id' => $associate->associate_id],
                    [
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
            // Step 3: Logging
            $addressId = optional($associate->address)->address_id;

            LogService::record(
                $authAccount,            // who did it
                $associate,            // what was acted on (loggable model, here the Account itself)
                'update',             // action
                'associate',              // log_type
                'User updated an associate',
                'Associate: ' . $associate->associate_id
                . ', Address: ' . $addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('associates')->with('success', 'associate updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'associate_id' => 'required|exists:associates,associate_id',
            'password' => 'required',
        ]);
        $deletor = Auth::guard('account')->user();
        $associate = Associate::findOrFail($request->associate_id);

        if (!Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }


        return DB::transaction(function () use ($associate, $request) {

            $addressId = optional($associate->address)->address_id;

            // Delete schedules & address
            $associate->address()->delete();
            // Delete clinic
            $associate->delete();
            $deletor = Auth::guard('account')->user();
            // Logging

            LogService::record(
                $deletor,            // who did it
                $associate,            // what was acted on (loggable model, here the Account itself)
                'delete',             // action
                'associate',              // log_type
                'User deleted an associate',
                'Associate: ' . $associate->associate_id,
                $request->ip(),
                $request->userAgent()
            );
            return redirect()->route('associates')->with('success', 'associate deleted successfully.');
        });
    }
}
