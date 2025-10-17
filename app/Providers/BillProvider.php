<?php

namespace App\Providers;

use App\Models\Bill;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class BillProvider extends ServiceProvider
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
        View::composer(['auth.staff-dashboard', 'auth.admin-dashboard'], function ($view) {
            $clinicId = session('clinic_id');

            // Always define it first
            $unpaidBills = collect();

            if ($clinicId) {
                $unpaidBills = Bill::with('patient')
                    ->where('status', 'unpaid')
                    ->where('clinic_id', $clinicId)
                    ->get();
            }

            $view->with('unpaidBills', $unpaidBills);
        });

    }
}
