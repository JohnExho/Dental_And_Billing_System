<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\Laboratories;
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
        View::composer(['pages.associates.modals.add','pages.staffs.modals.add'], function ($view) {
            $view->with('laboratories', Laboratories::all());
        });
    }
}
