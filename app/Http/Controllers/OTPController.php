<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\Account;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OTPController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $emailHash = hash('sha256', strtolower($request->email));
        $account = Account::where('email_hash', $emailHash)->firstOrFail();

        session(['otp_email' => $account->email]);
        $otp = random_int(100000, 999999);

        $account->update([
            'otp_hash' => Hash::make((string) $otp),
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($account->email)->send(new SendEmail($otp));

        /** @var Account $account */
        LogService::record(
            $account,            // who did it
            $account,            // what was acted on (loggable model, here the Account itself)
            'otp_request',             // action
            'auth',              // log_type
            'User has requested OTP for password reset',
            'Account: '.$account->account_id,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('confirm-otp')->with('success', 'OTP sent to your email.');
    }

    public function resendOtp(Request $request)
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('login')->withErrors('Session expired. Please request a new OTP.');
        }

        $emailHash = hash('sha256', strtolower($email));
        $account = Account::where('email_hash', $emailHash)->firstOrFail();

        $otp = random_int(100000, 999999);

        $account->update([
            'otp_hash' => Hash::make((string) $otp),
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($account->email)->send(new SendEmail($otp));

        /** @var Account $account */
        LogService::record(
            $account,            // who did it
            $account,            // what was acted on (loggable model, here the Account itself)
            'otp_resend',             // action
            'auth',              // log_type
            'User has requested OTP resend',
            'Account: '.$account->account_id,
            $request->ip(),
            $request->userAgent()
        );

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    public function verifyOtp(Request $request)
    {
        $encryptedEmail = session('otp_email');

        $request->validate([
            'otp' => 'required',
        ]);

        $otpInput = is_array($request->otp)
            ? implode('', $request->otp)
            : $request->otp;

        $emailHash = hash('sha256', strtolower($encryptedEmail));
        $account = Account::where('email_hash', $emailHash)->firstOrFail();

        if (! $account || ! $account->otp_hash || now()->gt($account->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        if (! Hash::check($otpInput, $account->otp_hash)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        $account->update([
            'otp_hash' => null,
            'otp_expires_at' => null,
        ]);

        LogService::record(
            $account,            // who did it
            $account,            // what was acted on (loggable model, here the Account itself)
            'otp_verify',             // action
            'auth',              // log_type
            'User has verified OTP for password reset',
            'Account: '.$account->account_id,
            $request->ip(),
            $request->userAgent()
        );

        session([
            'otp_verified' => true,
        ]);

        return redirect()->route('reset-password')->with('success', 'OTP verified. You can now reset your password.');
    }

    public function showResetForm(Request $request)
    {
        if (! session('otp_verified')) {
            return redirect()->route('forgot-password')
                ->withErrors(['otp' => 'You must verify your OTP first.']);
        }

        $encryptedEmail = session('otp_email');

        return view('auth.reset-password', [
            'email' => $encryptedEmail, // pass email to the Blade view
        ]);
    }

    public function resetPassword(Request $request)
    {
        $encryptedEmail = session('otp_email');
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $emailHash = hash('sha256', strtolower(($encryptedEmail)));
        $account = Account::where('email_hash', $emailHash)->firstOrFail();

        $account->update(['password' => Hash::make($request->password)]);
        /** @var Account $account */
        LogService::record(
            $account,            // who did it
            $account,            // what was acted on (loggable model, here the Account itself)
            'password_reset',             // action
            'auth',              // log_type
            'User has reset their password',
            'Account: '.$account->account_id,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('login')->with('success', 'Password reset successful.');
    }
}
