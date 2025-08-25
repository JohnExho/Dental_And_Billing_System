<?php
// app/Providers/LogViewServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Log;
use App\Models\Logs;

class LogViewServiceProvider extends ServiceProvider
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
        // Bind logs to all views (or you can limit to specific views)
        View::composer('dashboard', function ($view) {
            $logs = Logs::latest()->take(8)->get(); // adjust limit if needed
            $view->with('logs', $logs);
        });
    }
}
