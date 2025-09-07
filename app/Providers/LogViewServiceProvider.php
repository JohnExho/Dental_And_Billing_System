<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Logs;
use Illuminate\Support\Str;

class LogViewServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('auth.admin-dashboard', function ($view) {
            // Grab the latest logs
            $logs = Logs::latest()->take(20)->get();

            $processed = collect();

            foreach ($logs as $i => $log) {
                $processed->push($log);

                if ($log->description === 'User has logged in') {
                    $next = $logs[$i + 1] ?? null;

                    // Only insert unexpected logout if:
                    // - there *is* a next record
                    // - it's for the same account
                    // - and it's not a proper "User has logged out"
                    if ($next && $next->account_id === $log->account_id && $next->description !== 'User has logged out') {
                        $synthetic = $log->replicate();
                        $synthetic->log_id = (string) Str::uuid();
                        $synthetic->description = 'Unexpected logout';
                        // Timestamp aligned with the next record, so it appears as a bridge
                        $synthetic->created_at = $next->created_at;

                        $processed->push($synthetic);
                    }
                }
            }

            $view->with('logs', $processed);
        });
    }
}
