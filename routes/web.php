<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\RecallController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\AssociateController;
use App\Http\Controllers\PatientQrController;
use App\Http\Controllers\ToothListController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProgressNoteController;

Route::middleware('web')->group(function () {
    Route::view('/404', '404')->name('404');
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
        Route::get('/success', function () {
            // Check if a specific session variable is set
            if (session()->has('successes')) {
                // If the session key exists, show the success page
                return view('pages.tools.modals.success');
            } else {
                return redirect()->route('404');
            }
        })->name('success');
    });
Route::post('/ping', function (Request $request) {
    // Update last activity timestamp
    $request->session()->put('last_activity_at', now());
    
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String()
    ]);
})->middleware('auth:account');

Route::post('/force-logout', function (Request $request) {
    if (Auth::check()) {
        $accountId = Auth::user()->account_id;
        
        // Delete only the current device's token
        if ($accountId) {
            \App\Models\AccountLoginToken::where('account_id', $accountId)
                ->where('ip_address', $request->ip())
                ->where('user_agent', $request->userAgent())
                ->delete();
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
    
    return response()->json([
        'error' => 'You have been logged out due to inactivity.',
        'redirect' => route('login')
    ]);
})->middleware('auth:account');

        Route::get('/locations/cities/{province}', [AddressController::class, 'cities']);
    Route::get('/locations/barangays/{city}', [AddressController::class, 'barangays']);
    Route::post('/process/login', [AccountController::class, 'login'])->name('process-login');

    Route::middleware('validate.login.token')->group(function () {
    Route::post('/process/logout', [AccountController::class, 'logout'])->name('process-logout');
    Route::post('/process/change/role', [AccountController::class, 'switchRole'])->name('process-switch-role');
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
    Route::get('/patients/all', [PatientController::class, 'getAllPatients'])->name('patients.all');
    Route::put('/process/archive/patient', [PatientController::class, 'archive'])->name('process-archive-patient');
    Route::put('/process/unarchive/patient', [PatientController::class, 'unarchive'])->name('process-unarchive-patient');

    Route::post('/process/create/waitlist', [WaitlistController::class, 'create'])->name('process-create-waitlist');
    Route::put('/process/update/waitlist', [WaitlistController::class, 'update'])->name('process-update-waitlist');
    Route::delete('/process/delete/waitlist', [WaitlistController::class, 'destroy'])->name('process-delete-waitlist');
    Route::get('/waitlist/all', [WaitlistController::class, 'getAllWaitlist'])->name('waitlist.all');
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
    Route::put('/process/update/treatment', [TreatmentController::class, 'update'])->name('process-update-treatment');
    Route::delete('/process/delete/treatment', [TreatmentController::class, 'destroy'])->name('process-delete-treatment');

    Route::post('/process/process/bill', [BillController::class, 'create'])->name('process-process-bill');
    Route::delete('/process/delete/bill', [BillController::class, 'destroy'])->name('process-delete-bill');

    Route::post('/process/generate/qr', [PatientQrController::class, 'generateQr'])->name('process-generate-qr');
    Route::get('/qr/{qr_id}', [PatientQrController::class, 'showPasswordForm'])->name('qr.show');
    Route::post('/qr/{qr_id}/verify', [PatientQrController::class, 'verifyPassword'])->name('qr.verify');
    Route::get('/qr/{qr_id}/view', action: [PatientQrController::class, 'showProtectedView'])->name('qr.view');

    Route::post('/process/create/appointment', [AppointmentController::class, 'create'])->name('process-create-appointment');
    Route::put('/process/update/appointment', [AppointmentController::class, 'update'])->name('process-update-appointment');
    Route::delete('/process/delete/appointment', [AppointmentController::class, 'destroy'])->name('process-delete-appointment');

    Route::get('/process/export-patients', [ToolController::class, 'extract'])->name('process-export-patients');
    });

    // Protected routes
    Route::middleware(['auth:account', 'patient.profile', 'validate.login.token'])->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/settings', [AccountController::class, 'settings'])->name('settings');

        Route::middleware('staff.only')->group(function () {
            Route::view('/staff/dashboard', 'auth.staff-dashboard')->name('staff.dashboard');
            Route::get('/waitlist', [WaitlistController::class, 'index'])->name('waitlist');
            Route::get('/patients', [PatientController::class, 'index'])->name('patients');
            Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
            Route::get('/patient/profile', [PatientController::class, 'specific'])->name('specific-patient');
        });
        Route::middleware('admin.only')->group(function () {
            Route::view('/admin/dashboard', 'auth.admin-dashboard')->name('admin.dashboard');
            Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics');
            Route::get('/associates', [AssociateController::class, 'index'])->name('associates');
            Route::get('/staffs', [StaffController::class, 'index'])->name('staffs');
            Route::get('/teeth', [ToothListController::class, 'index'])->name('teeth');
            Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines');
            Route::get('/services', [ServiceController::class, 'index'])->name('services');
            Route::get('/tools', [ToolController::class, 'index'])->name('tools');
            Route::get('/reports', [ReportController::class, 'index'])->name('reports');
            Route::view('/dump', 'dd')->name('dump');

        });
    });
    
    Route::fallback(function () {
        return redirect()->route('404');
    });
});
