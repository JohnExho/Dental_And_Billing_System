<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ClinicController;

Route::middleware('web')->group(function () {
    // Login page
    Route::middleware('guest:account')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('login');
        Route::get('/forgot-password', function () {
            return view('auth.forgot-password');
        })->name('forgot-password');
        Route::get('/confirm-otp', function () {
            return view('auth.confirm-otp');
        })->name('confirm-otp');
        Route::get('/reset-password', [AccountController::class, 'showResetForm'])->name('reset-password');
    });

    Route::get('/locations/cities/{province}', [AddressController::class, 'cities']);
    Route::get('/locations/barangays/{city}', [AddressController::class, 'barangays']);
    Route::post('/process/login', [AccountController::class, 'login'])->name('process-login');
    Route::post('/process/logout', [AccountController::class, 'logout'])->name('process-logout');
    Route::put('/process/change/name', [AccountController::class, 'changeName'])->name('process-change-name');
    Route::put('/process/change/password', [AccountController::class, 'updatePassword'])->name('process-change-password');
    Route::post('/process/send/otp', [AccountController::class, 'sendOtp'])->name('process-send-otp');
    Route::post('/process/verify/otp', [AccountController::class, 'verifyOtp'])->name('process-verify-otp');
    Route::post('/process/reset/password', [AccountController::class, 'resetPassword'])->name('process-reset-password');
    Route::delete('/process/delete/account', [AccountController::class, 'deleteAccount'])->name('process-delete-account');
    Route::post('/process/create/clinic', [ClinicController::class, 'create'])->name('process-create-clinic');
    // Protected routes
    Route::middleware('auth:account')->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics');
        // Add Controller for Populating Clinics View
        Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
    });
});
