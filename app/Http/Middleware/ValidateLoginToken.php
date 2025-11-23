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
    // 1800 seconds = 30 minutes
    protected $idleTimeoutSeconds = 1800;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $account = Auth::user();
            $accountId = $account->account_id;

            // Check if login token is still valid
            $token = AccountLoginToken::where('account_id', $accountId)
                ->where('expires_at', '>', now())
                ->first();

            if (!$token) {
                return $this->logoutUser($request, 'Your session has expired. Please login again.');
            }

            // Extend token expiration on each request (sliding window)
            // Token stays valid for 30 more minutes from now (production: 30 min)
            if (!$request->is('ping', 'force-logout')) {
                $token->update([
                    'expires_at' => now()->addMinutes(30)
                ]);
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