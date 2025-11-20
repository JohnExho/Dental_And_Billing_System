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

    $unpaidBills = Bill::with('patient')
        ->where('status', 'unpaid')
        ->when($clinicId, function ($query) use ($clinicId) {
            $query->where('clinic_id', $clinicId);
        })
        ->get();

    $view->with('unpaidBills', $unpaidBills);
});


    }
}
