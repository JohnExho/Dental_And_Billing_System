<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Address;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;

class StaffController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {
        $clinicId = session('clinic_id');

        $staffs = Account::with([
            'clinic',
            'address.barangay',
            'address.city',
            'address.province',
        ])
            ->where('role', 'staff')
            ->whereNotNull('clinic_id')
            ->when($clinicId, fn ($query) => $query->where('clinic_id', $clinicId)) // session-based clinic filter
            ->latest()
            ->paginate(8);

        return view('pages.staffs.index', compact('staffs'));
    }

    public function create(Request $request)
    {
        // Validation
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'specialty' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'email' => 'nullable|email|max:255',
            'clinic_id' => 'nullable|exists:clinics,clinic_id',
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
            $newEmailHash && Account::where('email_hash', $newEmailHash)
                ->whereNull('deleted_at')
                ->exists()
        ) {
            return redirect()->back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request, $normalizedEmail, $newEmailHash) {
            $authAccount = $this->guard->user();

            // Step 1: Create Staff Account
            $staff = Account::create([
                'account_id' => (string) Str::uuid(),
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'last_name_hash' => hash('sha256', strtolower($request->last_name)),
                'description' => $request->description,
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
                'clinic_id' => $request->clinic_id,
                'role' => 'staff', // mark as staff
                'password' => Hash::make($request->password), // important
            ]);

            // Step 2: Create Address (if provided)
            if ($request->filled('address')) {
                Address::create([
                    'account_id' => $staff->account_id,
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
            $addressId = optional($staff->address)->address_id;

            LogService::record(
                $authAccount,
                $staff,
                'create',
                'staff',
                'User created a staff account',
                'Staff: '.$staff->account_id
                .', address: '.$addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('staffs')->with('success', 'Staff created successfully.');
        });
    }

    public function update(Request $request, Account $staff)
    {
        // Validation
        $request->validate([
            'account_id' => 'required|exists:accounts,account_id',
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
        $staff = Account::findOrFail($request->account_id);

        $normalizedEmail = $validated['email'] ?? null;
        $newEmailHash = $normalizedEmail ? hash('sha256', strtolower($normalizedEmail)) : null;

        if ($newEmailHash && Account::where('email_hash', $newEmailHash)
            ->whereNull('deleted_at')
            ->where('is_active', 1) // <-- only consider active associates
            ->exists()
        ) {
            return back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request, $staff, $authAccount, $normalizedEmail, $newEmailHash) {
            // Step 1: Update Staff
            $updateData = [
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'last_name_hash' => hash('sha256', strtolower($request->last_name)),
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            $staff->update($updateData);

            // Step 2: Update Address (if provided)
            if ($request->filled('address')) {
                $staff->address()->updateOrCreate(
                    ['account_id' => $staff->account_id],
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
            $addressId = optional($staff->address)->address_id;

            LogService::record(
                $authAccount,
                $staff,
                'update',
                'staff',
                'User updated a staff account',
                'Staff: '.$staff->account_id
                .', address: '.$addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('staffs')->with('success', 'Staff updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,account_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $staff = Account::findOrFail($request->account_id);

        // Check if the password matches the current user's password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($staff, $request) {

            $addressId = optional($staff->address)->address_id;

            // Delete schedules & address
            $staff->address()->delete();
            // Delete clinic
            $staff->delete();
            $deletor = Auth::guard('account')->user();
            // Logging

            LogService::record(
                $deletor,
                $staff,
                'delete',
                'staff',
                'User deleted a staff account',
                'Staff: '.$staff->account_id
                .', address: '.$addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('staffs')->with('success', 'account deleted successfully.');
        });
    }
}
