<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Clinic;
use App\Models\Medicine;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\AccountLoginToken;

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
public function index(Request $request)
{
    $userAgent = $request->header('User-Agent');

    if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $userAgent)) {
        return redirect()->route('404'); // or abort(404)
    }

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
    // 1️⃣ Validate email
    $request->validate(['email' => ['required', 'email']]);
    $emailHash = hash('sha256', strtolower($request->email));
    $account = Account::where('email_hash', $emailHash)->first();
    
    if (! $account) {
        return back()->with('error', 'Invalid credentials.');
    }
    
    if (! $account->is_active) {
        return back()->with('error', 'Your account is inactive. Please contact support.');
    }
    
    $role = strtolower(trim($account->role));
    
    if ($role === 'guest') {
        return back()->with('error', 'Guest accounts cannot log in.');
    }
    
    if ($role !== 'guest' && ! Hash::check($request->password, $account->password)) {
        return back()->with('error', 'Invalid credentials.');
    }
    
    if ($role === 'staff' && empty($account->clinic_id)) {
        return back()->with('error', 'You are not assigned to any clinic.');
    }
    
// Before creating a new token
$existingToken = AccountLoginToken::where('account_id', $account->account_id)
    ->where('expires_at', '>', now()) // only consider still valid tokens
    ->first();

// Expire old tokens automatically
AccountLoginToken::where('account_id', $account->account_id)
    ->where('expires_at', '<=', now()) // expired tokens
    ->delete();

if ($existingToken) {
    return back()->with('error', 'This account is already logged in from another device.');
}

    // 5️⃣ NOW login after passing all checks
    $this->guard->login($account);
    $request->session()->regenerate();
    
    session([
        'active_role' => $role,
        'clinic_id' => $role === 'staff' ? $account->clinic_id : null,
    ]);
    
    /** @var Account $account */
    $account = $this->guard->user();
    
    // 6️⃣ Log login action
    LogService::record(
        $account,
        $account,
        'login',
        'auth',
        'User has logged in',
        'Account: ' . $account->account_id,
        $request->ip(),
        $request->userAgent()
    );
    $expiresAt = now()->addMinutes(30);

    // 7️⃣ Create a new login token
    $rawToken = Str::uuid();
    AccountLoginToken::create([
        'account_id' => $account->account_id,
        'token_id' => $rawToken,
        'token' => hash('sha256', $rawToken),
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'expires_at' => $expiresAt,
    ]);
    
    // Store account_id in session (more reliable than token_id)
    session(['account_id_for_logout' => $account->account_id]);
    
    // 8️⃣ Redirect based on role
    $redirectRoute = match ($role) {
        'staff' => 'staff.dashboard',
        'admin' => 'admin.dashboard',
        default => 'dashboard',
    };
    
    return redirect()->route($redirectRoute);
}

public function logout(Request $request)
{
    $account = $this->guard->user();
    $accountId = $account?->account_id ?? session('account_id_for_logout');
    
    // 🔥 DELETE ALL TOKENS for this account (more reliable)
    if ($accountId) {
        AccountLoginToken::where('account_id', $accountId)->delete();
    }
    
    // Log the logout
    if ($account) {
        LogService::record(
            $account,
            $account,
            'logout',
            'auth',
            'User has logged out',
            'Account: '.$account->account_id,
            $request->ip(),
            $request->userAgent()
        );
    }
    
    // THEN log out and invalidate session
    $this->guard->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect()->route('login')->with('success', 'You have been logged out.');
}


    public function switchRole(Request $request)
    {
        $account = $this->guard->user();

        $request->validate([
            'role' => 'required|in:admin,staff',
            'clinic_id' => 'nullable|exists:clinics,clinic_id',
        ]);

        // Only allow switching if admin and can_act_as_staff
        if ($account->role === 'admin' && $account->can_act_as_staff) {
            session(['active_role' => $request->role]);
        } else {
            // Fallback: staff or admin without permission stay in their main role
            session(['active_role' => $account->role]);
        }

        if ($request->clinic_id) {
            session(['clinic_id' => $request->clinic_id]);
        }

        $activeRole = session('active_role');
        if ($account) {

            LogService::record(
                $account,            // who did it
                $account,            // what was acted on (loggable model, here the Account itself)
                'update',             // action
                'auth',              // log_type
                'User has changed roles',
                'Account: '.$account->account_id,
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
            LogService::record(
                $account,            // who did it
                $account,            // what was acted on (loggable model, here the Account itself)
                'update',             // action
                'auth',              // log_type
                'User has changed name',
                'Account: '.$account->account_id,
                $request->ip(),
                $request->userAgent()
            );
        }

        return redirect()->back()->with('success', 'Name updated successfully.');
    }

    /**
     * Update the authenticated account's password.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $account = $this->guard->user();
        $throttleKey = 'password-update:'.$account->id;

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

        if (! Hash::check($request->current_password, $account->password)) {
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
            LogService::record(
                $account,            // who did it
                $account,            // what was acted on (loggable model, here the Account itself)
                'update',             // action
                'auth',              // log_type
                'User has changed password',
                'Account: '.$account->account_id,
                $request->ip(),
                $request->userAgent()
            );
        }

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Delete the authenticated account.
     *
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
        $masterDeletionPassword = config('app.account_deletion_password');

        if (! $account) {
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
        LogService::record(
            $account,            // who did it
            $account,            // what was acted on (loggable model, here the Account itself)
            'delete',             // action
            'auth',              // log_type
            'User has deleted their account',
            'Account: '.$account->account_id,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('login')->with('success', 'Account deleted successfully.');
    }
}
