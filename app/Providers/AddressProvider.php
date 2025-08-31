<?php

namespace App\Providers;

use Yajra\Address\Entities\City;
use Illuminate\Support\Facades\View;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\Province;
use Illuminate\Support\ServiceProvider;

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
    view()->composer('*', function ($view) {
        $view->with('provinces', Province::orderBy('name')->get());
    });
    }
}
