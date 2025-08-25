<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Logs;
use App\Mail\SendEmail;
use App\Models\Account;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class AccountController extends Controller
{
    /**
     * The authentication guard instance.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    /**
     * Display the main index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Show the settings page for the authenticated account.
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        $account = $this->guard->user(); // Use the 'account' guard for consistency
        return view('settings.index', compact('account'));
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $emailHash = hash('sha256', strtolower($request->email));
        $account = Account::where('email_hash', $emailHash)->first();

        if ($account && Hash::check($request->password, $account->password)) {
            $this->guard->login($account);
            $request->session()->regenerate();
            /** @var Account $account */
            $account = $this->guard->user();
            if ($account) {
                Logs::record(
                    $account,
                    'login',
                    'auth',
                    'User has logged in',
                    $request->ip(),
                    $request->userAgent(),
                );
            }

            return redirect()->route('dashboard')->with('success', 'Welcome back!'. ' ' . $account->full_name);
        }

        return back()->with('error', 'Invalid credentials.');
    }


    public function logout(Request $request)
    {
        $account = $this->guard->user();
        // Log out the user using the 'account' guard first
        $this->guard->logout();

        // Invalidate the session to prevent session fixation
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        /** @var Account $account */

        if ($account) {
            Logs::record(
                $account,
                'logout',
                'auth',
                'User has logged out',
                $request->ip(),
                $request->userAgent()
            );
        }

        // Redirect to login page with a message
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    /**
     * Change the authenticated account's name.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeName(Request $request)
    {
        $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
        ]);

        /** @var Account $account */
        $account = $this->guard->user(); // Use the 'account' guard for consistency
        $account->last_name = $request->last_name;
        $account->first_name = $request->first_name;
        $account->middle_name = $request->middle_name;
        $account->save();

        /** @var Account $account */
        $account = $this->guard->user();
        if ($account) {
            Logs::record(
                $account,
                'update',
                'auth',
                'User has changed name',
                $request->ip(),
                $request->userAgent()
            );
        }

        return redirect()->back()->with('success', 'Name updated successfully.');
    }

    /**
     * Update the authenticated account's password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $account = $this->guard->user();
        $throttleKey = 'password-update:' . $account->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->with('error', 'Too many attempts. Please try again later.');
        }

        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|string|min:8|confirmed',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            RateLimiter::hit($throttleKey);
            return redirect()->back()
                ->with('error', $e->validator->errors()->first())
                ->withInput();
        }



        if (!Hash::check($request->current_password, $account->password)) {
            RateLimiter::hit($throttleKey);
            return redirect()->back()->with(
                'error',
                'The current password is incorrect.'
            )->withInput();
        }


        RateLimiter::clear($throttleKey);

        /** @var Account $account */
        $account->password = Hash::make($request->password);
        $account->save();

        /** @var Account $account */
        $account = $this->guard->user();
        if ($account) {
            Logs::record(
                $account,
                'update',
                'auth',
                'User has changed password',
                $request->ip(),
                $request->userAgent()
            );
        }

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Delete the authenticated account.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        /** @var \App\Models\Account|null $account */
        $account = $this->guard->user();

        $request->validate([
            'deletion_password' => 'required|string',
        ]);

        // Get the master password from env config
        $masterDeletionPassword = env('ACCOUNT_DELETION_PASSWORD');

        if (!$account) {
            return redirect()->back()->with('error', 'No account found to delete.');
        }

        if ($request->input('deletion_password') !== $masterDeletionPassword) {
            return redirect()->back()->with('error', 'Invalid deletion password.');
        }

        // Delete the account
        $account->delete();

        // Log out the account using the 'account' guard
        $this->guard->logout();

        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        /** @var Account $account */
        Logs::record(
            $account,
            'delete',
            'auth',
            'User has deleted their account',
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('login')->with('success', 'Account deleted successfully.');
    }


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
            'otp_hash' => Hash::make((string)$otp),
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($account->email)->send(new SendEmail($otp));

        /** @var Account $account */
        Logs::record(
            $account,
            'otp_request',
            'auth',
            'User has requested OTP for password reset',
            $request->ip(),
            $request->userAgent()
        );
        return redirect()->route('confirm-otp')->with('success', 'OTP sent to your email.');
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

    if (!$account || !$account->otp_hash || now()->gt($account->otp_expires_at)) {
        return back()->withErrors(['otp' => 'Invalid or expired OTP']);
    }

    if (!Hash::check($otpInput, $account->otp_hash)) {
        return back()->withErrors(['otp' => 'Invalid or expired OTP']);
    }

    $account->update([
        'otp_hash' => null,
        'otp_expires_at' => null,
    ]);

    Logs::record(
        $account,
        'otp_verify',
        'auth',
        'User has verified OTP for password reset',
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
        if (!session('otp_verified')) {
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
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $emailHash = hash('sha256', strtolower(($encryptedEmail)));
        $account = Account::where('email_hash', $emailHash)->firstOrFail();

        $account->update(['password' => Hash::make($request->password)]);
        /** @var Account $account */
        Logs::record(
            $account,
            'password_reset',
            'auth',
            'User has reset their password',
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('login')->with('success', 'Password reset successful.');
    }
}
