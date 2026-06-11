<?php

use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Nurse\MedicineController;
use App\Http\Controllers\Nurse\PatientController;
use App\Http\Controllers\Patient\MedicineLogController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/account/profile', [ProfileController::class, 'show'])->name('account.profile');
    Route::put('/account/timezone', [ProfileController::class, 'updateTimezone'])->name('account.timezone');

    Route::middleware('role:'.User::ROLE_NURSE)->prefix('perawat')->name('nurse.')->group(function () {
        Route::resource('patients', PatientController::class);
        Route::post('patients/{patient}/reset-password', [PatientController::class, 'resetPassword'])
            ->name('patients.reset-password');

        Route::get('patients/{patient}/medicines/create', [MedicineController::class, 'create'])
            ->name('patients.medicines.create');
        Route::post('patients/{patient}/medicines', [MedicineController::class, 'store'])
            ->name('patients.medicines.store');
        Route::get('patients/{patient}/medicines/{medicine}/edit', [MedicineController::class, 'edit'])
            ->name('patients.medicines.edit');
        Route::put('patients/{patient}/medicines/{medicine}', [MedicineController::class, 'update'])
            ->name('patients.medicines.update');
        Route::delete('patients/{patient}/medicines/{medicine}', [MedicineController::class, 'destroy'])
            ->name('patients.medicines.destroy');
    });

    Route::middleware('role:'.User::ROLE_PATIENT)->prefix('pasien')->name('patient.')->group(function () {
        Route::get('/', [MedicineLogController::class, 'dashboard'])->name('dashboard');
        Route::get('medicines/{medicine}/history', [MedicineLogController::class, 'history'])
            ->name('medicines.history');
        Route::post('medicines/{medicine}/mark-taken', [MedicineLogController::class, 'markTaken'])
            ->name('medicines.mark-taken');
    });
});

require __DIR__.'/auth.php';
