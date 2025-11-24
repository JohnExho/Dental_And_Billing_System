<?php

namespace App\Providers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppointmentProvider extends ServiceProvider
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
        // Composer for dashboard
        View::composer('auth.staff-dashboard', function ($view) {
            $query = Appointment::with(['patient', 'associate', 'account']);

            // Filter by clinic_id if one is selected in session
            $selectedClinicId = session('selected_clinic_id');
            if ($selectedClinicId) {
                $query->where('clinic_id', $selectedClinicId);
            }

            // Get all appointments, ordered by date (most recent first)
            $appointments = $query->orderBy('appointment_date', 'desc')
                ->paginate(10); // Add pagination to handle large datasets

            $view->with('appointments', $appointments);
        });
    }
}