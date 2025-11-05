<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AssociateController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProgressNoteController;
use App\Http\Controllers\RecallController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ToothListController;
use App\Http\Controllers\WaitlistController;
use Illuminate\Support\Facades\Route;

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
        Route::get('/reset-password', [OTPController::class, 'showResetForm'])->name('reset-password');
    });

    Route::get('/locations/cities/{province}', [AddressController::class, 'cities']);
    Route::get('/locations/barangays/{city}', [AddressController::class, 'barangays']);
    Route::post('/process/login', [AccountController::class, 'login'])->name('process-login');
    Route::post('/process/logout', [AccountController::class, 'logout'])->name('process-logout');
    Route::post('/process/chang/role', [AccountController::class, 'switchRole'])->name('process-switch-role');
    Route::put('/process/change/name', [AccountController::class, 'changeName'])->name('process-change-name');
    Route::put('/process/change/password', [AccountController::class, 'updatePassword'])->name('process-change-password');
    Route::delete('/process/delete/account', [AccountController::class, 'deleteAccount'])->name('process-delete-account');

    Route::post('/process/send/otp', [OTPController::class, 'sendOtp'])->name('process-send-otp');
    Route::post('/process/resend/otp', [OTPController::class, 'resendOtp'])->name('process-resend-otp');
    Route::post('/process/verify/otp', [OTPController::class, 'verifyOtp'])->name('process-verify-otp');
    Route::post('/process/reset/password', [OTPController::class, 'resetPassword'])->name('process-reset-password');

    Route::post('/process/create/clinic', [ClinicController::class, 'create'])->name('process-create-clinic');
    Route::put('/process/update/clinic', [ClinicController::class, 'update'])->name('process-update-clinic');
    Route::delete('process/delete/clinic', [ClinicController::class, 'destroy'])->name('process-delete-clinic');
    Route::post('process/clinics/select', [ClinicController::class, 'select'])->name('process-select-clinic');

    Route::post('/process/create/staff', [StaffController::class, 'create'])->name('process-create-staff');
    Route::put('/process/update/staff', [StaffController::class, 'update'])->name('process-update-staff');
    Route::delete('/process/delete/staff', [StaffController::class, 'destroy'])->name('process-delete-staff');

    Route::post('/process/create/associate', [AssociateController::class, 'create'])->name('process-create-associate');
    Route::put('/process/update/associate', [AssociateController::class, 'update'])->name('process-update-associate');
    Route::delete('/process/delete/associate', [AssociateController::class, 'destroy'])->name('process-delete-associate');

    Route::post('/process/create/tooth', [ToothListController::class, 'create'])->name('process-create-tooth');
    Route::put('/process/update/tooth', [ToothListController::class, 'update'])->name('process-update-tooth');
    Route::delete('/process/delete/tooth', [ToothListController::class, 'destroy'])->name('process-delete-tooth');

    Route::post('/process/create/medicine', [MedicineController::class, 'create'])->name('process-create-medicine');
    Route::put('/process/update/medicine', [MedicineController::class, 'update'])->name('process-update-medicine');
    Route::delete('/process/delete/medicine', [MedicineController::class, 'destroy'])->name('process-delete-medicine');

    Route::post('/process/create/service', [ServiceController::class, 'create'])->name('process-create-service');
    Route::put('/process/update/service', [ServiceController::class, 'update'])->name('process-update-service');
    Route::delete('/process/delete/service', [ServiceController::class, 'destroy'])->name('process-delete-service');

    Route::post('/process/create/patient', [PatientController::class, 'create'])->name('process-create-patient');
    Route::put('/process/update/patient', [PatientController::class, 'update'])->name('process-update-patient');
    Route::delete('/process/delete/patient', [PatientController::class, 'destroy'])->name('process-delete-patient');

    Route::post('/process/create/waitlist', [WaitlistController::class, 'create'])->name('process-create-waitlist');
    Route::put('/process/update/waitlist', [WaitlistController::class, 'update'])->name('process-update-waitlist');
    Route::delete('/process/delete/waitlist', [WaitlistController::class, 'destroy'])->name('process-delete-waitlist');

    Route::post('/process/create/process-note', [ProgressNoteController::class, 'create'])->name('process-create-progress-note');
    Route::put('/process/update/process-note', [ProgressNoteController::class, 'update'])->name('process-update-progress-note');
    Route::delete('/process/delete/process-note', [ProgressNoteController::class, 'destroy'])->name('process-delete-progress-note');

    Route::post('/process/create/recall', [RecallController::class, 'create'])->name('process-create-recall');
    Route::put('/process/update/recall', [RecallController::class, 'update'])->name('process-update-recall');
    Route::delete('/process/delete/recall', [RecallController::class, 'destroy'])->name('process-delete-recall');

    Route::post('/process/create/prescription', [PrescriptionController::class, 'create'])->name('process-create-prescription');
    Route::put('/process/update/prescription', [PrescriptionController::class, 'update'])->name('process-update-prescription');
    Route::delete('/process/delete/prescription', [PrescriptionController::class, 'destroy'])->name('process-delete-prescription');

    Route::post('/process/create/treatment', [TreatmentController::class, 'create'])->name('process-create-treatment');

    // Protected routes
    Route::middleware(['auth:account', 'patient.profile'])->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::view('/admin/dashboard', 'auth.admin-dashboard')->name('admin.dashboard');
        Route::view('/staff/dashboard', 'auth.staff-dashboard')->name('staff.dashboard');
        Route::get('/waitlist', [WaitlistController::class, 'index'])->name('waitlist');
        Route::get('/patients', [PatientController::class, 'index'])->name('patients');
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
        Route::get('/patient/profile', [PatientController::class, 'specific'])->name('specific-patient');
        Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
        Route::view('/dump', 'dd')->name('dump');
        Route::middleware('admin.only')->group(function () {
            Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics');
            Route::get('/associates', [AssociateController::class, 'index'])->name('associates');
            Route::get('/staffs', [StaffController::class, 'index'])->name('staffs');
            Route::get('/teeth', [ToothListController::class, 'index'])->name('teeth');
            Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines');
            Route::get('/services', [ServiceController::class, 'index'])->name('services');
            Route::view('/tools', 'pages.tools.index')->name('tools');
        });
    });
});
