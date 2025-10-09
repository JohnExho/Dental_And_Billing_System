<?php

namespace App\Providers;

use App\Models\Patient;
use Illuminate\Support\Facades\View;
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
          View::composer(['pages.waitlist.modals.add'], function ($view) {
            $view->with('patients', Patient::all());
        });
    }
}
