<?php

namespace App\Providers;

use App\Models\Prescription;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class PrescriptionProvider extends ServiceProvider
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
        View::composer([
            'pages.billing.modals.process'
        ], function ($view) {

            $clinicId = Session::get('clinic_id');

            $prescriptions = Prescription::with([
                'medicine.medicineClinics' => function ($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                }
            ])
                ->orderBy('created_at', 'desc')
                ->get();

            // add final_price + medicine_name computed values
            $prescriptions->each(function ($prescription) {
                $prescription->medicine_name = $prescription->medicine->name ?? 'Unknown Medicine';

                // Same logic as MedicineProvider: use clinic price or fallback to default price
                $prescription->final_price = $prescription->medicine?->medicineClinics->first()->price
                    ?? $prescription->medicine?->default_price
                    ?? 0;
            });

            $view->with('prescriptions', $prescriptions);
        });
    }
}
