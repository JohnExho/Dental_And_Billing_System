<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    /**
     * Handle an incoming request.
     */

    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function handle(Request $request, Closure $next)
    {
        $account = $this->guard->user();
        $activeRole = session('active_role', $account?->role);

        // Check if logged in, role is admin, and active role is admin
        if (!$account || $account->role !== 'admin' || $activeRole !== 'admin') {
            // Option 1: redirect to staff dashboard
            return redirect()->route('staff.dashboard')
                ->with('error', 'You do not have permission to access this page.');

            // Option 2 (if you prefer a 403 page):
            // abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
