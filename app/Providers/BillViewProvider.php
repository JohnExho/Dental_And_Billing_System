<?php

namespace App\Providers;

use App\Models\Medicine;
use App\Models\Service;
use App\Models\ToothList;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class BillViewProvider extends ServiceProvider
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
        View::composer(['pages.billing.modals.process'], function ($view) {
            $clinicId = Session::get('clinic_id');

            // Fetch medicines with clinic-specific prices
            $medicines = Medicine::with(['medicineClinics' => function ($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            }])->orderBy('name', 'asc')->get();

            // Add final_price dynamically
            $medicines->each(function ($medicine) {
                $medicine->final_price = $medicine->medicineClinics->first()->price ?? $medicine->default_price;
            });

            // Get services and teeth (assuming you have these)
            $services = Service::orderBy('name', 'asc')->get();
            $teeth = ToothList::orderBy('number', 'asc')->get();

            $view->with([
                'medicines' => $medicines,
                'services' => $services,
                'teeth' => $teeth
            ]);
        });
    }
}