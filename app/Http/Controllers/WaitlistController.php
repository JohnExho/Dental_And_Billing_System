<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;

class WaitlistController extends Controller
{
    public function index()
    {
        $clinicId = session('clinic_id');

        if (!$clinicId) {
            return redirect(route('staff.dashboard'))->with('error', 'Select a clinic first.');
        }
        $query = Waitlist::with([
            'clinic',
            'account',
            'patient',
            'associate',
            'laboratory',
        ])->latest()->whereNotNull('clinic_id');

        if (session()->has('clinic_id') && $clinicId = session('clinic_id')) {
            $query->where('clinic_id', $clinicId);
        }

        $waitlist = $query->paginate(8);

        return view('pages.waitlist.index', compact('waitlist'));
    }
}
