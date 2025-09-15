<?php

namespace App\Http\Controllers;


use App\Models\Logs;
use App\Models\Account;
use App\Models\Address;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;

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
    public function loginPage()
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

        if ($account && $account->is_active && Hash::check($request->password, $account->password)) {
            $this->guard->login($account);
            $request->session()->regenerate();
            session(['active_role' => $account->role]);

            /** @var Account $account */
            $account = $this->guard->user();

            if ($account) {
                Logs::record(
                    $account,
                    null,
                    null,
                    null,
                    'login',
                    'auth',
                    'User has logged in',
                    'Account: ' . json_encode($account->account_id),
                    $request->ip(),
                    $request->userAgent()
                );
            }

            // redirect logic based on role
            if ($account->role === 'staff') {
                return redirect()->route('staff.dashboard')
                    ->with('success', 'Welcome back ' . $account->full_name);
            }

            if ($account->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welcome back ' . $account->full_name);
            }

            // fallback (if role isn’t matched)
            return redirect()->route('dashboard')
                ->with('success', 'Welcome back ' . $account->full_name);
        }

        // give clearer error message for inactive accounts
        if ($account && !$account->is_active) {
            return back()->with('error', 'Your account is inactive. Please contact support.');
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
                null,
                null,
                null,
                'logout',
                'auth',
                'User has logged out',
                'Account: ' . json_encode($account->account_id),
                $request->ip(),
                $request->userAgent()
            );
        }

        // Redirect to login page with a message
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    public function switchRole(Request $request)
    {
        $account = $this->guard->user();

        $request->validate([
            'role' => 'required|in:admin,staff',
        ]);

        // Only allow switching if admin and can_act_as_staff
        if ($account->role === 'admin' && $account->can_act_as_staff) {
            session(['active_role' => $request->role]);
        } else {
            // Fallback: staff or admin without permission stay in their main role
            session(['active_role' => $account->role]);
        }

        $activeRole = session('active_role');
        if ($account) {
            Logs::record(
                $account,
                null,
                null,
                null,
                'update',
                'auth',
                'User has changed roles',
                'Account: ' . json_encode($account->account_id),
                $request->ip(),
                $request->userAgent()
            );
        }

        return match ($activeRole) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            default => redirect()->route('dashboard'), // fallback
        };
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
        $account->last_name_hash = hash('sha256', strtolower($request->last_name));
        $account->first_name = $request->first_name;
        $account->middle_name = $request->middle_name;
        $account->save();

        /** @var Account $account */
        $account = $this->guard->user();
        if ($account) {
            Logs::record(
                $account,
                null,
                null,
                null,
                'update',
                'auth',
                'User has changed name',
                'Account: ' . json_encode($account->account_id),
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
                null,
                null,
                null,
                'update',
                'auth',
                'User has changed password',
                'Account: ' . json_encode($account->account_id),
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
        // $masterDeletionPassword = env('ACCOUNT_DELETION_PASSWORD');
        $masterDeletionPassword = config('app.account_deletion_password');
        // $masterDeletionPassword = "secret";

        if (!$account) {
            return redirect()->back()->with('error', 'No account found to delete.');
        }

        if ($request->input('deletion_password') !== $masterDeletionPassword) {
            return redirect()->back()->with('error', 'Invalid deletion password.');
        }


        $accountId = $account->account_id;

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
            null,
            null,
            null,
            'delete',
            'auth',
            'User has deleted their account',
            'Account: ' . json_encode($accountId),
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('login')->with('success', 'Account deleted successfully.');
    }
}
