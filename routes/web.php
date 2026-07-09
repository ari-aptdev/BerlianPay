<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HouseController;
use App\Http\Controllers\Admin\IplRateController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReminderLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ResidentAccountController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Resident\DashboardController as ResidentDashboardController;
use App\Http\Controllers\Resident\PaymentController as ResidentPaymentController;
use App\Http\Controllers\Resident\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

// --- Auth: satu form login, redirect otomatis sesuai role ---
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// --- Admin/Owner ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('houses', HouseController::class)->except(['show']);
    Route::resource('ipl-rates', IplRateController::class)->except(['show']);
    Route::resource('payments', AdminPaymentController::class)->except(['show']);

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

    Route::get('residents', [ResidentAccountController::class, 'index'])->name('residents.index');
    Route::get('residents/create', [ResidentAccountController::class, 'create'])->name('residents.create');
    Route::post('residents', [ResidentAccountController::class, 'store'])->name('residents.store');
    Route::patch('residents/{resident}/toggle-active', [ResidentAccountController::class, 'toggleActive'])->name('residents.toggle-active');
    Route::patch('residents/{resident}/reset-password', [ResidentAccountController::class, 'resetPassword'])->name('residents.reset-password');

    Route::get('settings/reminder', [SettingController::class, 'edit'])->name('settings.reminder');
    Route::put('settings/reminder', [SettingController::class, 'update'])->name('settings.reminder.update');

    Route::get('reminder-logs', [ReminderLogController::class, 'index'])->name('reminder-logs.index');
});

// --- Warga (read-only, scoped ke rumah sendiri) ---
Route::middleware(['auth', 'role:warga'])->prefix('resident')->name('resident.')->group(function () {
    Route::get('dashboard', [ResidentDashboardController::class, 'index'])->name('dashboard');

    Route::get('payments', [ResidentPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [ResidentPaymentController::class, 'show'])->name('payments.show');
    Route::get('payments/{payment}/download-proof', [ResidentPaymentController::class, 'downloadProof'])->name('payments.download-proof');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
});
