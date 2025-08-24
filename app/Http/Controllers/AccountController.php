<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\SendEmail;
use App\Models\Account;
use App\Models\Logs;
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // use the 'account' guard so auth matches the route middleware (auth:account)
        if ($this->guard->attempt($credentials)) {
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
                    $request->userAgent()
                );
            }

            return redirect()->route('dashboard')->with('success', 'Welcome back!');
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
            $account->logAction('logout', 'auth', 'User has logged out', request()->ip(), request()->userAgent());
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
            'name' => 'required|string|max:255',
        ]);

        /** @var Account $account */
        $account = $this->guard->user(); // Use the 'account' guard for consistency
        $account->name = $request->name;
        $account->save();

        /** @var Account $account */
        $account = $this->guard->user();
        if ($account) {
            $account->logAction('update', 'auth', 'User has changed name', request()->ip(), request()->userAgent());
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
            $account->logAction('update', 'auth', 'User has changed password', request()->ip(), request()->userAgent());
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

        $account->logAction('delete', 'auth', 'User has deleted their account', request()->ip(), request()->userAgent());

        return redirect()->route('login')->with('success', 'Account deleted successfully.');
    }


    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:accounts,email',
        ]);

        $account = Account::where('email', $request->email)->first();

        $otp = random_int(100000, 999999);

        $account->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($account->email)->send(new SendEmail($otp));

        $account->logAction('otp_send', 'auth', 'User requested OTP for password reset', request()->ip(), request()->userAgent());
        return redirect()->route('login')->with('success', 'OTP sent to your email.');
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:accounts,email',
            'otp'   => 'required|numeric',
        ]);

        $account = Account::where('email', $request->email)->first();

        if (!$account->otp || !$account->otp_expires_at) {
            return response()->json(['message' => 'No OTP request found'], 400);
        }

        if ($account->otp != $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (now()->gt($account->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        // Clear OTP after successful use
        $account->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json(['message' => 'OTP verified, proceed to reset password.']);
    }
}
