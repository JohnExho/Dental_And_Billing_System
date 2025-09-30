<?php

namespace App\Providers;

use App\Models\Logs;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class LogViewServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer(['auth.admin-dashboard', 'pages.tools.index'], function ($view) {

            // Check which view is being rendered
            $viewName = $view->getName();

            if ($viewName === 'auth.admin-dashboard') {
                // ✅ For admin dashboard → EXCLUDE auth logs
                $logs = Logs::where('log_type', '!=', 'auth')
                    ->latest()
                    ->paginate(20);
            } else {
                // ✅ For pages.tools.index → LOAD ALL logs
                $logs = Logs::latest()
                    ->get();
            }

            $processed = collect();

            foreach ($logs as $i => $log) {
                $processed->push($log);

                if ($log->description === 'User has logged in') {
                    $next = $logs[$i + 1] ?? null;

                    if (
                        $next &&
                        $next->account_id === $log->account_id &&
                        $next->description !== 'User has logged out'
                    ) {
                        $synthetic = $log->replicate();
                        $synthetic->log_id = (string) Str::uuid();
                        $synthetic->description = 'Unexpected logout';
                        $synthetic->created_at = $next->created_at;

                        $processed->push($synthetic);
                    }
                }
            }

            $view->with('logs', $processed);
        });
    }
}
