<?php
// not checked
namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Laboratories;
use App\Models\Logs;
use Illuminate\Support\Str;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class LaboratoryController extends Controller
{
    protected $guard;
    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }
    public function index()
    {
        $laboratories = Laboratories::with('address.barangay', 'address.city', 'address.province')
            ->latest()
            ->paginate(4);


        return view('pages.laboratories.index', compact('laboratories'));
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
            'address' => 'nullable|array',
            'address.house_no' => 'nullable|string|max:50',
            'address.street' => 'nullable|string|max:255',
            'address.barangay_id' => 'nullable|exists:barangays,id',
            'address.city_id' => 'nullable|exists:cities,id',
            'address.province_id' => 'nullable|exists:provinces,id',
        ]);

        // Prevent duplicate email
        $newEmailHash = $request->email ? hash('sha256', strtolower($request->email)) : null;

        if (
            $newEmailHash && Laboratories::where('email_hash', $newEmailHash)
            ->whereNull('deleted_at') // ignore soft-deleted
            ->exists()
        ) {
            return redirect()->back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request) {
            $account = $this->guard->user();
            Log::info($request->all());

            // Step 1: Create Clinic
            $laboratory = Laboratories::create([
                'laboratory_id' => Str::uuid(),
                'account_id' => $account->account_id,
                'name' => $request->name,
                'description' => $request->description,
                'speciality' => $request->speciality,
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'email_hash' => $request->email ? hash('sha256', $request->email) : null,
            ]);


            // Step 2: Create Address (if provided)
            if ($request->filled('address')) {
                Address::create([
                    'account_id' => $account->account_id,
                    'laboratory_id' => $laboratory->laboratory_id,
                    'house_no' => $request->address['house_no'] ?? null,
                    'street' => $request->address['street'] ?? null,
                    'barangay_name'   => optional(Barangay::find($request->address['barangay_id']))->name,
                    'city_name'       => optional(City::find($request->address['city_id']))->name,
                    'province_name'   => optional(Province::find($request->address['province_id']))->name,
                    'barangay_id' => $request->address['barangay_id'] ?? null,
                    'city_id'     => $request->address['city_id'] ?? null,
                    'province_id' => $request->address['province_id'] ?? null,
                ]);
            }

            // Step 4: Logging
            $addressId = optional($laboratory->address)->address_id;

            Logs::record(
                $account,
                null,
                $laboratory,
                'create',
                'clinic',
                'User created a laboratory',
                'clinic: ' . $laboratory->laboratory_id
                    . ', address: ' . $addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('laboratories')->with('success', 'Laboratory created successfully.');
        });
    }

    public function update(Request $request, Account $authAccount)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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
        ]);

        $authAccount = $this->guard->user();
        $laboratory  = Laboratories::findOrFail($request->laboratory_id);

        // Normalize + hash email
        $normalizedEmail = $request->email ? strtolower($request->email) : null;
        $newEmailHash    = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;

        // Prevent duplicate email
        if (
            $newEmailHash && Laboratories::where('email_hash', $newEmailHash)
            ->where('laboratory_id', '!=', $laboratory->laboratory_id)
            ->whereNull('deleted_at')
            ->exists()
        ) {
            return redirect()->back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request, $laboratory, $authAccount, $normalizedEmail, $newEmailHash) {
            // Step 1: Update laboratory
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'speciality' => $request->speciality,
                'mobile_no' => $request->mobile_no,
                'contact_no' => $request->contact_no,
                'email' => $normalizedEmail,
                'email_hash' => $newEmailHash,
            ];

            $laboratory->update($updateData);

            // Step 2: Update Address (if provided)
            if ($request->filled('address')) {
                $laboratory->address()->updateOrCreate(
                    ['laboratory_id' => $laboratory->laboratory_id],
                    [
                        'house_no'    => $request->address['house_no'] ?? null,
                        'street'      => $request->address['street'] ?? null,
                        'barangay_name'   => optional(Barangay::find($request->address['barangay_id']))->name,
                        'city_name'       => optional(City::find($request->address['city_id']))->name,
                        'province_name'   => optional(Province::find($request->address['province_id']))->name,
                        'barangay_id' => $request->address['barangay_id'] ?? null,
                        'city_id'     => $request->address['city_id'] ?? null,
                        'province_id' => $request->address['province_id'] ?? null,
                    ]
                );
            }

            // Step 3: Logging
            $addressId = optional($laboratory->address)->address_id;

            Logs::record(
                $authAccount, // actor
                null,       // subject
                $laboratory,
                'update',
                'laboratory',
                'User updated a laboratory',
                'laboratory: ' . $laboratory->laboratory_id
                    . ', address: ' . $addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('laboratories')->with('success', 'laboratory updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'laboratory_id' => 'required|exists:laboratories,laboratory_id',
            'password' => 'required'
        ]);
    
        $account =  Auth::guard('account')->user();
        $laboratory = Laboratories::findOrFail($request->laboratory_id);


        return DB::transaction(function () use ($laboratory, $account, $request) {

            $addressId = optional($laboratory->address)->address_id;

            // Delete schedules & address
            $laboratory->address()->delete();
            // Delete clinic
            $laboratory->delete();

            // Logging
            Logs::record(
                $account,
                null,
                null,
                'delete',
                'clinic',
                'User deleted a Laboratory',
                'Account: ' . $laboratory->laboratory_id
                    . ', address: ' . $addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('laboratories')->with('success', 'Laboratory deleted successfully.');
        });
    }
}
