<?php

namespace App\Providers;

use App\Models\Bill;
use Illuminate\Support\Facades\Auth;
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
            $account = Auth::guard('account')->user();
            $clinicId = session('clinic_id');

            $unpaidBillsQuery = Bill::with('patient')
                ->where('status', 'unpaid');

            // If user is staff, only show bills for their current clinic
            if ($account && $account->role === 'admin') {
                if ($clinicId) {
                    $unpaidBillsQuery->where('clinic_id', $clinicId);
                } else {
                    // Staff without a selected clinic sees nothing
                    $unpaidBillsQuery;
                }
            }

            if ($account && $account->role === 'staff') {
                if ($clinicId) {
                    $unpaidBillsQuery->where('clinic_id', $clinicId);
                } else {
                    // Staff without a selected clinic sees nothing
                    $unpaidBillsQuery->whereNull('clinic_id');
                }
            }

            $unpaidBills = $unpaidBillsQuery->limit(15)->get();

            $view->with('unpaidBills', $unpaidBills);
        });

    }
}
