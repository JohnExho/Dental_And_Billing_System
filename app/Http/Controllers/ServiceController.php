<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Clinic;
use App\Models\Service;
use Illuminate\Support\Str;
use App\Models\Laboratories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'service_type' => 'required|string|max:255',

            // validate clinics + labs
            'clinics' => 'nullable|array',
            'clinics.*.selected' => 'nullable|boolean',
            'clinics.*.price' => 'nullable|numeric|min:0|max:999999.99',

            'laboratories' => 'nullable|array',
            'laboratories.*.selected' => 'nullable|boolean',
            'laboratories.*.price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // Step 1: Create the service
        $service = Service::create([
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'description' => $request->description,
            'service_type' => $request->service_type,
            'account_id' => $this->guard->id(),
        ]);

        $authAccount = $this->guard->user();

        // Step 2: Attach to clinics
        if ($request->has('clinics')) {
            foreach ($request->clinics as $clinicId => $data) {
                if (isset($data['selected'])) {
                    $service->clinics()->attach($clinicId, [
                        'clinic_service_id' => Str::uuid(),
                        'price' => $data['price'] ?? 0,
                    ]);

                    // log
                    $clinic = Clinic::find($clinicId);
                    Logs::record(
                        $authAccount,
                        $clinic,
                        null,
                        null,
                        'create',
                        'Service',
                        'User attached service to clinic',
                        'Service: '.$service->name.' → '.$clinic->name.' (₱'.($data['price'] ?? 0).')',
                        $request->ip(),
                        $request->userAgent()
                    );
                }
            }
        }

        // Step 3: Attach to laboratories
        if ($request->has('laboratories')) {
            foreach ($request->laboratories as $labId => $data) {
                if (isset($data['selected'])) {
                    $service->laboratories()->attach($labId, [
                        'laboratory_service_id' => Str::uuid(),
                        'price' => $data['price'] ?? 0,
                    ]);

                    // log
                    $lab = Laboratories::find($labId);
                    Logs::record(
                        $authAccount,
                        $lab,
                        null,
                        null,
                        'create',
                        'Service',
                        'User attached service to laboratory',
                        'Service: '.$service->name.' → '.$lab->name.' (₱'.($data['price'] ?? 0).')',
                        $request->ip(),
                        $request->userAgent()
                    );
                }
            }
        }

        // Step 4: Redirect
        return redirect()->route('services')->with('success', 'Service created successfully.');
    }
}
