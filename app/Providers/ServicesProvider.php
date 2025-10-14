<?php

namespace App\Providers;

use App\Models\Service;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ServicesProvider extends ServiceProvider
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
        View::composer(['pages.patients.progress-notes.modals.add'], function ($view) {
            $clinicId = Session::get('clinic_id');

            // Fetch services with clinic-specific price or fallback
            $services = Service::with(['clinicService' => function ($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            }])->get();

            // Add final_price dynamically
            $services->each(function ($service) {
                $service->final_price = $service->clinicService->first()->price ?? $service->default_price;
            });

            $view->with('services', $services);
        });
    }
}
