<?php

namespace App\Http\Controllers;

use App\Models\PatientQrCode;
use Illuminate\Http\Request;

class PatientQrController extends Controller
{
    public function generateQr()
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
        $qr_id = \Str::uuid()->toString();
        $password = \Str::random(8);

        $url = route('qr.show', ['qr_id' => $qr_id]);

        $qrCode = \Endroid\QrCode\QrCode::create($url)
            ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->setSize(300)
            ->setMargin(10);

        $writer = new \Endroid\QrCode\Writer\PngWriter;
        $result = $writer->write($qrCode);

        // Save directly to public folder
        $fileName = 'qr_codes/'.$qr_id.'.png';
        $outputPath = 'C:/Users/Administrator/Documents/Dental_And_Billing_System/public/'.$fileName;

        if (! file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        $result->saveToFile($outputPath);

        // Save record in DB
        PatientQrCode::create([
            'clinic_id' => $clinicId,
            'qr_id' => $qr_id,
            'qr_code' => $fileName, // store relative to public
            'qr_password' => $password,
        ]);

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
