<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

Route::middleware('web')->group(function () {
    // Login page
    Route::middleware('guest:account')->group(function(){
         Route::get('/', [AccountController::class, 'index'])->name('login');
    });

    // Handle login
    Route::post('/process/login', [AccountController::class, 'login'])->name('process-login');
    Route::post('/process/logout', [AccountController::class, 'logout'])->name('process-logout');
    Route::put('/process/change/name',[AccountController::class, 'changeName'])->name('process-change-name');
    Route::put('/process/change/password',[AccountController::class, 'updatePassword'])->name('process-change-password');
    Route::delete('/process/delete/account',[AccountController::class, 'deleteAccount'])->name('process-delete-account');

    // Protected routes
    Route::middleware('auth:account')->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
    });
});
