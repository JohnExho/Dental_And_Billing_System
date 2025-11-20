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
        // 1️⃣ Validate request
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2️⃣ Find account by email hash
        $emailHash = hash('sha256', strtolower($request->email));
        $account = Account::where('email_hash', $emailHash)->first();

        // 3️⃣ Check credentials
        if (! $account || ! $account->is_active || ! Hash::check($request->password, $account->password)) {
            $errorMessage = $account && ! $account->is_active
                ? 'Your account is inactive. Please contact support.'
                : 'Invalid credentials.';

            return back()->with('error', $errorMessage);
        }
        $role = strtolower(trim($account->role));

        if ($role === 'staff') {
            // Staff: check clinic assignment
            if (empty($account->clinic_id)) {
                return back()->with('error', 'You are not assigned to any clinic. Please contact your administrator.');
            }

            $clinic = Clinic::find($account->clinic_id);
            if (! $clinic) {
                return back()->with('error', 'Assigned clinic not found. Please contact support.');
            }
        }
        // 4️⃣ Login and regenerate session
        $this->guard->login($account);
        $request->session()->regenerate();
        session([
            'active_role' => $role,
            'clinic_id' => $role === 'staff' ? $account->clinic_id : null,
        ]);

        /** @var Account $account */
        $account = $this->guard->user(); // ensures we have the guard-loaded user
        // 5️⃣ Log the login
        LogService::record(
            $account,
            $account,
            'login',
            'auth',
            'User has logged in',
            'Account: '.$account->account_id,
            $request->ip(),
            $request->userAgent()
        );

        // 6️⃣ Initialize stock message
        $stockErrorMessage = null;
        Log::info('Login role:', ['role' => $account->role]);
        // 7️⃣ Role-specific logic
        if ($role === 'admin') {
            // Admin: show low-stock medicines
            $lowStockMedicines = Medicine::leftJoin('medicine_clinics', 'medicines.medicine_id', '=', 'medicine_clinics.medicine_id')
                ->select('medicines.medicine_id', 'medicines.name', DB::raw('COALESCE(SUM(medicine_clinics.stock), 0) as total_stock'))
                ->groupBy('medicines.medicine_id', 'medicines.name')
                ->having('total_stock', '<', 50)
                ->get();

            if ($lowStockMedicines->isNotEmpty()) {
                $medicineNames = $lowStockMedicines->pluck('name')->join(', ');
                $stockErrorMessage = "Low stock for: {$medicineNames}";
            }
        }

        // 8️⃣ Determine redirect based on role
        $redirectRoute = match ($role) {
            'staff' => 'staff.dashboard',
            'admin' => 'admin.dashboard',
            default => 'dashboard',
        };

        // 9️⃣ Redirect with messages
        return redirect()->route($redirectRoute)
            ->with('success', 'Welcome back '.$account->full_name)
            ->with('stock_error', $stockErrorMessage);
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
            LogService::record(
                $account,            // who did it
                $account,            // what was acted on (loggable model, here the Account itself)
                'logout',             // action
                'auth',              // log_type
                'User has logged out',
                'Account: '.$account->account_id,
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
