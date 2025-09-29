<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Str;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {

        $clinicId = session('clinic_id'); // null if not chosen
        $services = Service::with([
            'clinicService' => function ($q) use ($clinicId) {
                if ($clinicId) {
                    $q->where('clinic_id', $clinicId);
                }
            },
        ])
            ->paginate(8);

        return view('pages.services.index', compact('services', 'clinicId'));
    }

    public function create(Request $request)
    {
        // Validate form data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'service_type' => 'required|string|max:255',
        ]);

        // Return errors if validation fails
        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // 1. Create the service catalog entry
        $clinicId = session('clinic_id');
        $service = Service::create([
            'service_id' => Str::uuid(),
            'account_id' => $this->guard->user()->account_id,
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'description' => $request->description,
            'service_type' => $request->service_type,
            'default_price' => session()->has('clinic_id') ? null : $request->price,  // If no clinic, set default price
        ]);

        // 2. If a clinic is in session, store clinic-specific price & stock
        if (session()->has('clinic_id')) {
            $clinicId = session('clinic_id');

            // Save clinic-specific price
            $service->clinicService()->updateOrCreate(
                ['clinic_id' => $clinicId],  // Assuming `clinic_id` is the column name
                [
                    'clinic_service_id' => Str::uuid(),
                    'price' => $request->price,  // Clinic price
                ]
            );
        }

        // 3. Logging
        $authAccount = $this->guard->user();
        $priceSource = session()->has('clinic_id') ? 'Clinic Price' : 'Default Price';

        LogService::record(
            $authAccount,
            $service,
            'create',
            'Service Catalog',
            'User has created a Service',
            "Service: {$service->name} | {$priceSource}: {$request->price}",
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('services')->with('success', 'Service created successfully.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,service_id',
            'name' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // 1. Find the base service
        $service = Service::findOrFail($request->service_id);

        // 2. Update core attributes
        $service->update([
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'service_type' => $request->service_type,
            'description' => $request->description,
        ]);

        // 3. Handle pricing depending on clinic session
        $priceSource = 'Default Price';

        if (session()->has('clinic_id')) {
            $priceSource = 'Clinic Price';

            $clinicId = session('clinic_id');

            $service->clinicService()->updateOrCreate(
                [
                    'clinic_id' => $clinicId,
                ],
                [
                    'clinic_service_id' => Str::uuid(),
                    'price' => $request->price,
                ]
            );
        } else {
            $service->update([
                'default_price' => $request->price,
            ]);
        }

        // 4. Logging
        $authAccount = $this->guard->user();
        LogService::record(
            $authAccount,
            $service,
            'update',
            'Service Catalog',
            'User has updated a service',
            "Service: {$service->name} ({$priceSource}: {$request->price})",
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('services')
            ->with('success', 'Service updated successfully.');
    }


    public function destroy(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'password' => 'required',
        ]);

        $deletor = $this->guard->user();

        // Step 1: Verify password
        if (!Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        // Step 2: Get the service
        $service = Service::where('service_id', $request->service_id)->firstOrFail();

        // Step 3: Wrap in transaction
        return DB::transaction(function () use ($service, $deletor, $request) {
            // Delete related clinic services if any
            $service->clinicService()->delete();

            // Delete the service itself
            $service->delete();

            // Step 4: Log deletion
            LogService::record(
                $deletor,
                $service,
                'delete',
                'Service Catalog',
                'User has deleted a service',
                "Service: {$service->name}",
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('services')
                ->with('success', 'Service deleted successfully.');
        });
    }
}
