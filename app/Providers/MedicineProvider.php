<?php

namespace App\Providers;

use App\Models\Medicine;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MedicineProvider extends ServiceProvider
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
        View::composer(['pages.prescriptions.modals.add'], function ($view) {
            $clinicId = Session::get('clinic_id');

            // Fetch medicines with clinic-specific price or fallback
            $medicines = Medicine::with(['medicineClinics' => function ($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            }])->orderBy('name', 'asc')->get();

            // Add final_price dynamically
            $medicines->each(function ($medicine) {
                $medicine->final_price = $medicine->medicineClinics->first()->price ?? $medicine->default_price;
            });

            $view->with('medicines', $medicines);
        });
    }
}
