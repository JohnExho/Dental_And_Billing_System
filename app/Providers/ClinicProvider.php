<?php

namespace App\Providers;

use App\Models\Clinic;
use App\Models\Laboratories;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ClinicProvider extends ServiceProvider
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
        View::composer(['pages.associates.modals.add','pages.staffs.modals.add','pages.medicines.modals.add',
        'pages.medicines.modals.edit'], function ($view) {
            $view->with('clinics', Clinic::all());
        });

    }
}
