<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Yajra\Address\Entities\Province;

class AddressProvider extends ServiceProvider
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
        view()->composer(['pages.patients.modals.add', 'pages.patients.modals.edit',
            'pages.clinics.modals.add', 'pages.clinics.modals.edit',
            'pages.associates.modals.add', 'pages.associates.modals.edit',
            'pages.staffs.modals.add', 'pages.staffs.modals.edit',
            'pages.laboratories.modals.add', 'pages.laboratories.modals.edit',
            'pages.patients.modals.self-add','pages.reports.partials.location-partial'
        ], function ($view) {
            $view->with('provinces', Province::orderBy('name')->get());
        });
    }
}
