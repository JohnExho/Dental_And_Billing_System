<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Logs;
use App\Models\Medicine;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $clinicId = session('clinic_id');
        $medicines = Medicine::with([
            'medicineClinics' => function ($q) use ($clinicId) {
                if ($clinicId) {
                    $q->where('clinic_id', $clinicId);
                }
            },
        ])->paginate(8);

        return view('pages.medicines.index', compact('medicines', 'clinicId'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'stock' => session()->has('clinic_id')
                ? 'required|integer|min:0'
                : 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // 1. Create the medicine catalog entry
        $medicine = Medicine::create([
            'medicine_id' => Str::uuid(),
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'description' => $request->description,
            'default_price' => session()->has('clinic_id') ? null : $request->price,
        ]);

        // 2. If a clinic is in session, store clinic-specific price & stock
        if (session()->has('clinic_id')) {
            $clinicId = session('clinic_id');

            $medicine->clinics()->attach($clinicId, [
                'medicine_clinic_id' => Str::uuid(),
                'price' => $request->price,
                'stock' => $request->stock,
            ]);
        }

        // 3. Logging
        $authAccount = $this->guard->user();
        $priceSource = session()->has('clinic_id') ? 'Clinic Price' : 'Default Price';

        LogService::record(
            $authAccount,
            $medicine,
            'create',
            'Medicine Catalog',
            'User has created a medicine',
            "Medicine: {$medicine->name} | {$priceSource}: {$request->price}, Stock: {$request->stock}",
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('medicines')->with('success', 'Medicine created successfully.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|exists:medicines,medicine_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // 1. Find medicine
        $medicine = Medicine::findOrFail($request->medicine_id);

        // 2. Update core attributes
        $medicine->update([
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'description' => $request->description,
        ]);

        // 3. Handle pricing/stock depending on clinic session
        $priceSource = 'Default Price';

        if (session()->has('clinic_id')) {
            $priceSource = 'Clinic Price';
            $clinicId = session('clinic_id');

            // Update or create pivot record
            $medicine->medicineClinics()->updateOrCreate(
                [
                    'clinic_id' => $clinicId,
                ],
                [
                    'medicine_clinic_id' => Str::uuid(), // still unique
                    'price' => $request->price,
                    'stock' => $request->stock ?? 0,
                ]
            );
        } else {
            $medicine->update([
                'default_price' => $request->price,
            ]);
        }

        // 4. Logging
        $authAccount = $this->guard->user();

        LogService::record(
            $authAccount,
            $medicine,
            'update',
            'Medicine Catalog',
            'User has updated a medicine',
            "Medicine: {$medicine->name} | {$priceSource}: {$request->price}, Stock: ".($request->stock ?? 'N/A'),
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('medicines')
            ->with('success', 'Medicine updated successfully.');
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
