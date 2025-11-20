<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffOnly
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $account = $this->guard->user();

        // Determine the currently active role, fallback to user's main role
        $activeRole = session('active_role', $account?->role);

        // Deny access if not logged in or active role is not staff
        if (! $account || $activeRole !== 'staff') {
            // Optional: redirect to some "no access" page or dashboard
            return redirect()->route('404'); // or route('dashboard')
        }

        return $next($request);
    }
}
