<?php

namespace App\Providers;

use App\Models\Laboratories;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class LaboratoryProvider extends ServiceProvider
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
        View::composer(['pages.waitlist.modals.add', 'pages.patients.modals.waitlist-add'], function ($view) {            $view->with('laboratories', Laboratories::all());
        });
    }
}
