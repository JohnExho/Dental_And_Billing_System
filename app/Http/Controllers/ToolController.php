<?php

namespace App\Http\Controllers;

use App\Models\PatientQrCode;

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
}
