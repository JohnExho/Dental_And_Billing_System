<?php

namespace App\Http\Controllers;

use App\Models\ClinicToothPrice;
use App\Models\ToothList;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ToothListController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {
        $clinicId = session('clinic_id'); // null if not chosen

        $teeth = ToothList::with([
            'clinicPrices' => function ($q) use ($clinicId) {
                if ($clinicId) {
                    $q->where('clinic_id', $clinicId);
                }
            },
        ])
            ->orderByRaw('CAST(number AS UNSIGNED) ASC')
            ->paginate(8);

        return view('pages.teeth.index', compact('teeth', 'clinicId'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1|max:32|unique:tooth_list,number',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // 1. Create tooth catalog entry
        $tooth = ToothList::create([
            'tooth_list_id' => \Str::uuid(),
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'number' => $request->number,
            'default_price' => session()->has('clinic_id') ? null : $request->price, // only store if no clinic
        ]);

        // 2. If session clinic exists → save price for that clinic
        if (session()->has('clinic_id')) {
            ClinicToothPrice::create([
                'clinic_tooth_price_id' => \Str::uuid(),
                'clinic_id' => session('clinic_id'),
                'tooth_list_id' => $tooth->tooth_list_id,
                'price' => $request->price,
            ]);
        }

        // 3. Logging
        $authAccount = $this->guard->user();
        $priceSource = session()->has('clinic_id') ? 'Clinic Price' : 'Default Price';

        LogService::record(
            $authAccount,
            $tooth,
            'create',
            'Tooth Catalog',
            'User has created a tooth',
            "Tooth: {$tooth->name} (#{$tooth->number}) {$priceSource}: {$request->price}",
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('teeth')->with('success', 'Tooth created successfully.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tooth_list_id' => 'required|exists:tooth_list,tooth_list_id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'number' => [
                'required',
                'integer',
                'min:1',
                'max:32',
                Rule::unique('tooth_list', 'number')
                    ->ignore($request->tooth_list_id, 'tooth_list_id'),
            ],
        ], [
            'number.unique' => 'This tooth number is already assigned.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // 1. Find the base tooth
        $tooth = ToothList::findOrFail($request->tooth_list_id);

        // 2. Update core attributes
        $tooth->update([
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'number' => $request->number,
        ]);

        // 3. Handle pricing depending on clinic session
        $priceSource = 'Default Price';

        if (session()->has('clinic_id')) {
            $priceSource = 'Clinic Price';

            ClinicToothPrice::updateOrCreate(
                [
                    'clinic_id' => session('clinic_id'),
                    'tooth_list_id' => $tooth->tooth_list_id,
                ],
                [
                    'clinic_tooth_price_id' => Str::uuid(),
                    'price' => $request->price,
                ]
            );
        } else {
            $tooth->update([
                'default_price' => $request->price,
            ]);
        }

        // 4. Logging
        $authAccount = $this->guard->user();
        LogService::record(
            $authAccount,
            $tooth,
            'update',
            'Tooth Catalog',
            'User has updated a tooth',
            "Tooth: {$tooth->name} (#{$tooth->number}) {$priceSource}: {$request->price}",
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('teeth')
            ->with('success', 'Tooth updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'tooth_list_id' => 'required|exists:tooth_list,tooth_list_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();

        // Password check
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        $toothList = ToothList::findOrFail($request->tooth_list_id);

        return DB::transaction(function () use ($toothList, $deletor, $request) {
            // If a clinic session exists, remove only the clinic price mapping
            if (session()->has('clinic_id')) {
                $clinicId = session('clinic_id');
                ClinicToothPrice::where('clinic_id', $clinicId)
                    ->where('tooth_list_id', $toothList->tooth_list_id)
                    ->get()
                    ->each
                    ->delete(); // uses the model ->delete() = soft delete

                $priceSource = "Clinic Price (Clinic ID: {$clinicId})";
            } else {
                // Otherwise delete the tooth from catalog entirely
                $toothList->delete();
                $priceSource = 'Default Price / Global Tooth';
            }

            // Logging
            LogService::record(
                $deletor,
                $toothList,
                'delete',
                'Teeth',
                'User deleted a tooth or clinic-specific price',
                "Tooth: {$toothList->name} (#{$toothList->number}) Source: {$priceSource}",
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('teeth')
                ->with('success', 'Tooth deleted successfully.');
        });
    }
}
