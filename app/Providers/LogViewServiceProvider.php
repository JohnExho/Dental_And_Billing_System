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
            $viewName = $view->getName();

            if ($viewName === 'auth.admin-dashboard') {
                $logsQuery = Logs::where('log_type', '!=', 'auth')
                    ->whereNotIn('action', ['select', 'deselect'])
                    ->latest();
            } else {
                // For tools.index → include all logs
                $logsQuery = Logs::latest();
            }

            // Paginate first
            $logs = $logsQuery->paginate(20);

            // Process only the current page items
            $processed = collect();
            foreach ($logs as $i => $log) {
                $processed->push($log);

                if ($log->description === 'User has logged in') {
                    $next = $logs[$i + 1] ?? null;

                    if ($next && $next->account_id === $log->account_id && $next->description !== 'User has logged out') {
                        $synthetic = $log->replicate();
                        $synthetic->log_id = (string) Str::uuid();
                        $synthetic->description = 'Unexpected logout';
                        $synthetic->created_at = $next->created_at;

                        $processed->push($synthetic);
                    }
                }
            }
            // Replace the items of the paginator with processed ones
            $logs->setCollection($processed);

            $view->with('logs', $logs);
        });

    }
}
