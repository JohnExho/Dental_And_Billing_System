<?php

// app/Http/Middleware/ValidateLoginToken.php
namespace App\Http\Middleware;

use App\Models\AccountLoginToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateLoginToken
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $account = Auth::user();
            $accountId = session('account_id_for_logout') ?? $account->account_id;
            
            // Check if a valid token exists
            $validToken = AccountLoginToken::where('account_id', $accountId)
                ->where('expires_at', '>', now())
                ->exists();
            
            if (!$validToken) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Your session has expired. Please login again.');
            }
        }
        
        return $next($request);
    }
}