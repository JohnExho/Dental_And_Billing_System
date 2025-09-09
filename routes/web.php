<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\LaboratoryController;
use App\Models\Laboratories;

Route::middleware('web')->group(function () {
    // Login page
    Route::middleware('guest:account')->group(function () {
        Route::get('/', [AccountController::class, 'loginPage'])->name('login');
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
    Route::post('/process/chang/role', [AccountController::class, 'switchRole'])->name('process-switch-role');
    Route::put('/process/change/name', [AccountController::class, 'changeName'])->name('process-change-name');
    Route::put('/process/change/password', [AccountController::class, 'updatePassword'])->name('process-change-password');
    Route::post('/process/send/otp', [AccountController::class, 'sendOtp'])->name('process-send-otp');
    Route::post('/process/resend/otp', [AccountController::class, 'resendOtp'])->name('process-resend-otp');
    Route::post('/process/verify/otp', [AccountController::class, 'verifyOtp'])->name('process-verify-otp');
    Route::post('/process/reset/password', [AccountController::class, 'resetPassword'])->name('process-reset-password');
    Route::delete('/process/delete/account', [AccountController::class, 'deleteAccount'])->name('process-delete-account');

    Route::post('/process/create/clinic', [ClinicController::class, 'create'])->name('process-create-clinic');
    Route::put('/process/update/clinic', [ClinicController::class, 'update'])->name('process-update-clinic');
    Route::delete('process/delete/clnic', [ClinicController::class, 'destroy'])->name('process-delete-clinic');

    Route::post('/process/create/staff', [AccountController::class, 'create'])->name('process-create-staff');
    Route::put('/process/update/staff', [AccountController::class, 'update'])->name('process-update-staff');
    Route::delete('/process/delete/staff', [AccountController::class, 'destroy'])->name('process-delete-staff');

     Route::post('/process/create/laboratory', [LaboratoryController::class, 'create'])->name('process-create-laboratory');

    // Protected routes
    Route::middleware('auth:account')->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::view('/admin/dashboard', 'auth.admin-dashboard')->name('admin.dashboard');
        Route::view('/staff/dashboard', 'auth.staff-dashboard')->name('staff.dashboard');
        Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
     Route::middleware('admin.only')->group(function(){
        Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics');
        Route::view('/associates', 'pages.associates.index')->name('associates');
        Route::get('/staffs', [AccountController::class,'staffIndex'])->name('staffs');
        Route::get('/laboratories', [LaboratoryController::class,'index'])->name('laboratories');
           });
    });
});
