<?php

namespace App\Providers;

use App\Models\Associate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AssociateProvider extends ServiceProvider
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
        View::composer(['pages.waitlist.modals.add', 'pages.patients.modals.waitlist-add', 'pages.recalls.modals.add', 'pages.recalls.modals.edit'], function ($view) {
            $view->with('associates', Associate::where('is_active', true)->get());
        });

        View::composer(['pages.appointments.index'], function ($view) {
            $activeClinicId = session('clinic_id');
            $view->with('associates', Associate::where('is_active', true)
                ->where('clinic_id', $activeClinicId)
                ->get());
        });
    }
}
