<?php

namespace App\Http\Controllers;

use App\Models\PatientQrCode;
use App\Exports\PatientsExport;
use Maatwebsite\Excel\Facades\Excel;

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
        $fileName = 'patients_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PatientsExport, $fileName);
    }
}
