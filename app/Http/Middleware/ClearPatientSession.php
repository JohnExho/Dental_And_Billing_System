<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearPatientSession
{
    public function handle(Request $request, Closure $next)
    {

        // Only keep the session for this one route
        if ($request->route()?->getName() !== 'specific-patient') {
            session()->forget('current_patient_id');
        }

        return $next($request);
    }
}
