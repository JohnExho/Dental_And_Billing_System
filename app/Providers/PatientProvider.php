<?php

namespace App\Providers;

use App\Models\Patient;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class PatientProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer(['pages.waitlist.modals.add', 'pages.appointments.modals.add'], function ($view) {
            $clinicId = Session::get('clinic_id');

            $patients = Patient::where('clinic_id', $clinicId)->get();

            $view->with('patients', $patients);
        });
    }
}
