<?php

namespace App\Http\Controllers;

use App\Models\PatientQrCode;
use App\Exports\PatientsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;


class ToolController extends Controller
{
    public function index()
    {
        $clinicId = session('clinic_id');

        // Fetch the latest QR for this clinic
        $qr = null;
        if ($clinicId) {
            $qr = PatientQrCode::where('clinic_id', $clinicId)->latest()->first();
        }

        return view('pages.tools.index', compact('qr'));
    }


public function extract()
{
    $userId = auth()->id() ?? session()->getId(); 
    $cooldownKey = 'patients_export_cooldown_' . $userId;

    // If user is still cooling down:
    if (Cache::has($cooldownKey)) {
        return back()->with('error', 'Please wait a bit before exporting again.');
    }

    // Set cooldown (30 seconds)
    Cache::put($cooldownKey, true, now()->addSeconds(30));

    $fileName = 'patients_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new PatientsExport, $fileName);
}}
