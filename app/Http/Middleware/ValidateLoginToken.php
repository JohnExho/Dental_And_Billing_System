<?php

namespace App\Http\Middleware;

use App\Models\AccountLoginToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ValidateLoginToken
{
    // Idle timeout in seconds (must match JavaScript IDLE_TIMEOUT)
    // 60 seconds = 1 minute
    protected $idleTimeoutSeconds = 60;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $account = Auth::user();
            $accountId = session('account_id_for_logout') ?? $account->account_id;

            // Check if login token is still valid
            $token = AccountLoginToken::where('account_id', $accountId)
                ->where('expires_at', '>', now())
                ->first();

            if (!$token) {
                return $this->logoutUser($request, 'Your session has expired. Please login again.');
            }

            // Initialize last activity if not set
            if (!session()->has('last_activity_at')) {
                session(['last_activity_at' => now()]);
                return $next($request);
            }

            // Get last activity timestamp
            $lastActivity = session('last_activity_at');
            
            // Ensure we have a Carbon instance
            if (!($lastActivity instanceof Carbon)) {
                try {
                    $lastActivity = Carbon::parse($lastActivity);
                } catch (\Exception $e) {
                    session(['last_activity_at' => now()]);
                    return $next($request);
                }
            }

            $secondsSinceLastActivity = now()->diffInSeconds($lastActivity);

            // Check if user has been inactive too long
            if ($secondsSinceLastActivity >= $this->idleTimeoutSeconds) {
                return $this->logoutUser($request, 'You have been inactive for too long. Please login again.');
            }

            // Update last activity timestamp on all requests except logout
            if (!$request->is('force-logout')) {
                session(['last_activity_at' => now()]);
            }
        }

        return $next($request);
    }

    /**
     * Logout user and redirect to login
     */
    protected function logoutUser(Request $request, string $message)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Handle AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $message,
                'redirect' => route('login')
            ], 401);
        }
        
        return redirect()->route('login')->with('error', $message);
    }
}