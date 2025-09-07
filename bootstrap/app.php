<?php

use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\Authenticated;
use Illuminate\Foundation\Application;
// use App\Http\Middleware\Action;
use App\Http\Middleware\Unauthenticated;
// use App\Http\Middleware\AuthenticatedAdmin;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.only' => AdminOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
