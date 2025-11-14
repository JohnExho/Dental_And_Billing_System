<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\PatientQrCode;
use App\Services\LogService;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PatientQrController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function generateQr(Request $request)
    {
        $clinicId = session('clinic_id');

        // Delete previous QR for this clinic
        $existingQr = PatientQrCode::where('clinic_id', $clinicId)->first();
        if ($existingQr) {
            $filePath = public_path($existingQr->qr_code);
            if (file_exists($filePath)) {
                unlink($filePath); // delete the old QR file
            }
            $existingQr->delete();
        }

        // Generate new QR
        $qr_id = Str::uuid()->toString();
        $password = Str::random(8);

        $url = route('qr.show', ['qr_id' => $qr_id]);

        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter;
        $result = $writer->write($qrCode);

        // Save directly to public folder
        $fileName = 'qr_codes/'.$qr_id.'.png';
        $outputPath = storage_path('app/public/'.$fileName);

        if (! file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        $result->saveToFile($outputPath);

        // Save record in DB
        $patientQr = PatientQrCode::create([
            'clinic_id' => $clinicId,
            'qr_id' => $qr_id,
            'qr_code' => $fileName, // store relative to public
            'qr_password' => $password,
        ]);

        $authAccount = $this->guard->user();

        LogService::record(
            $authAccount,
            $patientQr,
            'create',
            'Patient QR Code',
            'User has generated a new patient QR code',
            "QR ID: {$qr_id}".' '."Clinic ID: {$clinicId}",
            $request->ip(),
            $request->userAgent()
        );

        // ✅ Redirect to the same page with updated data
        return redirect()->route('tools')->with('success', 'QR regenerated!');
    }

    // Step 1: Show the password input page
    public function showPasswordForm($qr_id)
    {
        $qr = PatientQrCode::findOrFail($qr_id);

        return view('pages.tools.modals.password', compact('qr'));
    }

    // Step 2: Handle password submission
    public function verifyPassword(Request $request, $qr_id)
    {
        $request->validate([
            'qr_password' => 'required|string',
        ]);

        $qr = PatientQrCode::findOrFail($qr_id);

        // Compare entered password
        if ($qr->qr_password !== $request->qr_password) {
            return back()->withErrors(['qr_password' => 'Incorrect password.']);
        }

        // Store session flag and clinic_id
        session([
            'qr_access' => true,
            'clinic_id' => $qr->clinic_id, // store clinic_id
        ]);

        $authAccount = $this->guard->user();
        $guestAccount = Account::where('role', 'guest')->first();

        LogService::record(
            $authAccount ?? $guestAccount,
            $qr,
            'access',
            'Patient QR Code',
            'User has accessed the patient QR code',
            "QR ID: {$qr_id}".' '."Clinic ID: {$qr->clinic_id}",
            $request->ip(),
            $request->userAgent()
        );

        // Redirect to the protected view
        return redirect()->route('qr.view', $qr_id)->with('success', 'Password verified!');
    }

    // Step 3: Show the protected view
    public function showProtectedView($qr_id)
    {
        // Check if user has verified password
        if (! session('qr_access')) {
            abort(403, 'Unauthorized access');
        }

        // Optionally store additional session data or retrieve data related to $qr_id
        session(['successes' => true]);

        // Use the $qr_id as needed
        $qr = PatientQrCode::findOrFail($qr_id); // Retrieve additional QR code details if necessary

        return view('pages.patients.modals.self-add', compact('qr_id', 'qr'));
    }
}
