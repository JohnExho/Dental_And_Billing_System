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
            $today = Carbon::today();

            $query = Appointment::with(['patient', 'associate'])
                ->whereDate('appointment_date', $today)
                ->where('status', 'scheduled');

            // Filter by clinic_id if one is selected in session
            $selectedClinicId = session('selected_clinic_id');
            if ($selectedClinicId) {
                $query->where('clinic_id', $selectedClinicId);
            }

            $todayAppointments = $query->orderBy('appointment_date', 'asc')->get();

            $view->with('todayAppointments', $todayAppointments);
        });
    }
}
