<?php

namespace App\Providers;

use App\Models\ToothList;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ToothProvider extends ServiceProvider
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
        View::composer(['pages.progress-notes.modals.add','pages.progress-notes.modals.add'], function ($view) {
            $clinicId = Session::get('clinic_id');

            $teeth = ToothList::with(['clinicPrices' => function ($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            }])
                ->orderBy('number', 'asc') // ✅ Sort here
                ->get();

            $teeth->each(function ($tooth) {
                $tooth->final_price = $tooth->clinicPrices->first()->price ?? $tooth->default_price;
            });

            $view->with('teeth', $teeth);
        });

    }
}
