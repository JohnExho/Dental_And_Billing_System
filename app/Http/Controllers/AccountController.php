<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Logs;
use App\Mail\SendEmail;
use App\Models\Account;
use App\Models\Address;
use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    public function loginPage()
    {
        return view('index');
    }

    public function staffIndex()
    {
        $staffs = Account::with('address.barangay', 'address.city', 'address.province')
            ->latest()
            ->where('role', 'staff')
            ->paginate(8);


        return view('pages.staffs.index', compact('staffs'));
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
            session(['active_role' => $account->role]);

            /** @var Account $account */
            $account = $this->guard->user();

            if ($account) {
                Logs::record(
                    $account,
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
            'delete',
            'auth',
            'User has deleted their account',
            'Account: ' . json_encode($accountId),
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
            null,
            null,
            'otp_request',
            'auth',
            'User has requested OTP for password reset',
            'Account: ' . json_encode($account->account_id),
            $request->ip(),
            $request->userAgent()
        );
        return redirect()->route('confirm-otp')->with('success', 'OTP sent to your email.');
    }

    public function resendOtp(Request $request)
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('login')->withErrors('Session expired. Please request a new OTP.');
        }

        $emailHash = hash('sha256', strtolower($email));
        $account = Account::where('email_hash', $emailHash)->firstOrFail();

        $otp = random_int(100000, 999999);

        $account->update([
            'otp_hash' => Hash::make((string)$otp),
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($account->email)->send(new SendEmail($otp));

        Logs::record(
            $account,
            null,
            null,
            'otp_resend',
            'auth',
            'User has requested OTP resend',
            'Account: ' . json_encode($account->account_id),
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
            null,
            null,
            'otp_verify',
            'auth',
            'User has verified OTP for password reset',
            'Account: ' . json_encode($account->account_id),
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
            null,
            null,
            'password_reset',
            'auth',
            'User has reset their password',
            'Account: ' . json_encode($account->account_id),
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('login')->with('success', 'Password reset successful.');
    }

    public function create(Request $request)
    {
        // Validation
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'specialty' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'contact_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|array',
            'address.house_no' => 'nullable|string|max:50',
            'address.street' => 'nullable|string|max:255',
            'address.barangay_id' => 'nullable|exists:barangays,id',
            'address.city_id' => 'nullable|exists:cities,id',
            'address.province_id' => 'nullable|exists:provinces,id',
        ]);

        // Prevent duplicate email
        $newEmailHash = $request->email ? hash('sha256', strtolower($request->email)) : null;

        if (
            $newEmailHash && Account::where('email_hash', $newEmailHash)
            ->whereNull('deleted_at')
            ->exists()
        ) {
            return redirect()->back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request) {
            $authAccount = $this->guard->user();

            // Step 1: Create Staff Account
            $staff = Account::create([
                'account_id'       => (string) Str::uuid(),
                'first_name'       => $request->first_name,
                'middle_name'      => $request->middle_name,
                'last_name'        => $request->last_name,
                'description'      => $request->description,
                'mobile_no'        => $request->mobile_no,
                'contact_no'       => $request->contact_no,
                'email'            => $request->email,
                'email_hash'       => $request->email ? hash('sha256', $request->email) : null,
                'role'             => 'staff', // mark as staff
                'password' => Hash::make($request->password), // important
            ]);

            // Step 2: Create Address (if provided)
            if ($request->filled('address')) {
                Address::create([
                    'account_id'  => $staff->account_id,
                    'house_no'    => $request->address['house_no'] ?? null,
                    'street'      => $request->address['street'] ?? null,
                    'barangay_id' => $request->address['barangay_id'] ?? null,
                    'city_id'     => $request->address['city_id'] ?? null,
                    'province_id' => $request->address['province_id'] ?? null,
                ]);
            }

            // Step 3: Logging
            $addressId = optional($staff->address)->address_id;

            Logs::record(
                $authAccount, // actor (logged-in user)
                null,
                null,
                'create',
                'staff',
                'User created a staff account',
                'staff: ' . $staff->account_id
                    . ', address: ' . $addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('staffs')->with('success', 'Staff created successfully.');
        });
    }
    public function update(Request $request, Account $staff)
    {
        // Validation
        $request->validate([
            'account_id'     => 'required|exists:accounts,account_id',
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'last_name'      => 'required|string|max:100',
            'mobile_no'      => 'nullable|string|max:20',
            'contact_no'     => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|array',
            'address.house_no'   => 'nullable|string|max:50',
            'address.street'     => 'nullable|string|max:255',
            'address.barangay_id' => 'nullable|exists:barangays,id',
            'address.city_id'    => 'nullable|exists:cities,id',
            'address.province_id' => 'nullable|exists:provinces,id',
        ]);

        $authAccount = $this->guard->user();
        $staff       = Account::findOrFail($request->account_id);

        // Normalize + hash email
        $normalizedEmail = $request->email ? strtolower($request->email) : null;
        $newEmailHash    = $normalizedEmail ? hash('sha256', $normalizedEmail) : null;

        // Prevent duplicate email
        if (
            $newEmailHash && Account::where('email_hash', $newEmailHash)
            ->where('account_id', '!=', $staff->account_id)
            ->whereNull('deleted_at')
            ->exists()
        ) {
            return redirect()->back()->with('error', 'The email has already been taken.');
        }

        return DB::transaction(function () use ($request, $staff, $authAccount, $normalizedEmail, $newEmailHash) {
            // Step 1: Update Staff
            $updateData = [
                'first_name'  => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name'   => $request->last_name,
                'mobile_no'   => $request->mobile_no,
                'contact_no'  => $request->contact_no,
                'email'       => $normalizedEmail,
                'email_hash'  => $newEmailHash,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $staff->update($updateData);

            // Step 2: Update Address (if provided)
            if ($request->filled('address')) {
                $staff->address()->updateOrCreate(
                    ['account_id' => $staff->account_id],
                    [
                        'house_no'    => $request->address['house_no'] ?? null,
                        'street'      => $request->address['street'] ?? null,
                        'barangay_id' => $request->address['barangay_id'] ?? null,
                        'city_id'     => $request->address['city_id'] ?? null,
                        'province_id' => $request->address['province_id'] ?? null,
                    ]
                );
            }

            // Step 3: Logging
            $addressId = optional($staff->address)->address_id;

            Logs::record(
                $authAccount, // actor
                null,       // subject
                null,
                'update',
                'staff',
                'User updated a staff account',
                'staff: ' . $staff->account_id
                    . ', address: ' . $addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('staffs')->with('success', 'Staff updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,account_id',
            'password' => 'required'
        ]);

        $account = Account::findOrFail($request->account_id);


        return DB::transaction(function () use ($account, $request) {

            $addressId = optional($account->address)->address_id;

            // Delete schedules & address
            $account->address()->delete();
            // Delete clinic
            $account->delete();
            $deletor =  Auth::guard('account')->user();
            // Logging
            Logs::record(
                $deletor,
                null,
                null,
                'delete',
                'clinic',
                'User deleted an account',
                'Account: ' . $account->account_id
                    . ', address: ' . $addressId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('staffs')->with('success', 'account deleted successfully.');
        });
    }
}
