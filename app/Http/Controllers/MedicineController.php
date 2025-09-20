<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Logs;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MedicineController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {
        $medicines = Medicine::with('clinics')->latest()
            ->paginate(8);

        return view('pages.medicines.index', compact('medicines'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'clinics' => 'required|array',
            'clinics.*.selected' => 'nullable|boolean',
            'clinics.*.price' => 'nullable|numeric|min:0|max:999999.99',
            'clinics.*.stock' => 'nullable|integer|min:0|max:100000',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // Step 1: Create the medicine
        $medicine = Medicine::create([
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'description' => $request->description,
        ]);

        // Step 2: Attach to selected clinics with their own pivot data
        foreach ($request->clinics as $clinicId => $data) {
            if (isset($data['selected'])) {
                $medicine->clinics()->attach($clinicId, [
                    'medicine_clinic_id' => Str::uuid(),
                    'price' => $data['price'] ?? 0,
                    'stock' => $data['stock'] ?? 0,
                ]);
            }
        }

        // Step 3: Log action for each attached clinic
        $authAccount = $this->guard->user();
        foreach ($request->clinics as $clinicId => $data) {
            if (isset($data['selected'])) {
                $clinic = Clinic::findOrFail($clinicId);

                Logs::record(
                    $authAccount,
                    $clinic,
                    null,
                    null,
                    'create',
                    'Medicine',
                    'User created a medicine',
                    'Medicine: '.$medicine->name.' (Stock: '.($data['stock'] ?? 0).', Price: '.($data['price'] ?? 0).')',
                    $request->ip(),
                    $request->userAgent()
                );
            }
        }

        return redirect()->route('medicines')->with('success', 'Medicine created successfully.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|uuid|exists:medicines,medicine_id',
            'name' => 'required|string|max:255',
            'clinics' => 'required|array',
            'clinics.*.selected' => 'nullable|boolean',
            'clinics.*.price' => 'nullable|numeric|min:0|max:999999.99',
            'clinics.*.stock' => 'nullable|integer|min:0|max:100000',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // Step 1: Find medicine by hidden input
        $medicine = Medicine::findOrFail($request->medicine_id);

        // Step 2: Update base fields
        $medicine->update([
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'description' => $request->description,
        ]);

        // Step 3: Build pivot data
        $pivotData = [];
        foreach ($request->clinics as $clinicId => $data) {
            if (isset($data['selected'])) {
                $pivotData[$clinicId] = [
                    'medicine_clinic_id' => Str::uuid(), // stays unique if new
                    'price' => $data['price'] ?? 0,
                    'stock' => $data['stock'] ?? 0,
                ];
            }
        }

        // Step 4: Sync clinics with pivot data
        $medicine->clinics()->sync($pivotData);

        // Step 5: Log updates
        $authAccount = $this->guard->user();
        foreach ($pivotData as $clinicId => $pivot) {
            $clinic = Clinic::findOrFail($clinicId);

            Logs::record(
                $authAccount,
                $clinic,
                null,
                null,
                'update',
                'Medicine',
                'User updated a medicine',
                'Medicine: '.$medicine->name.' (Stock: '.$pivot['stock'].', Price: '.$pivot['price'].')',
                $request->ip(),
                $request->userAgent()
            );
        }

        return redirect()->route('medicines')->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,medicine_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();

        // Step 1: Verify password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        // Step 2: Get the medicine
        $medicine = Medicine::findOrFail($request->medicine_id);

        // Step 3: Wrap in transaction
        return DB::transaction(function () use ($medicine, $deletor, $request) {
            // Delete pivot records first (if you want cascading handled manually)
            $medicine->clinics()->detach();

            // Delete the medicine itself
            $medicine->delete();

            // Step 4: Log deletion
            Logs::record(
                $deletor,
                null,
                null,
                null,
                'delete',
                'Medicine',
                'User deleted a medicine',
                'Medicine: '.$medicine->name,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('medicines')
                ->with('success', 'Medicine deleted successfully.');
        });
    }
}
