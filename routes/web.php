<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\EmployeePortalController;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Employee Portal (public)
Route::get('/', [EmployeePortalController::class, 'index'])->name('employee.portal');
Route::post('/verify', [EmployeePortalController::class, 'verify'])->name('employee.verify');
Route::get('/slip/{id}', [EmployeePortalController::class, 'showSlip'])->name('employee.slip');
Route::get('/slip/{id}/download', [EmployeePortalController::class, 'downloadSlip'])->name('employee.slip.download');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Salary management
    Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
    Route::get('/salary/upload', [SalaryController::class, 'showUploadForm'])->name('salary.upload.form');
    Route::post('/salary/upload', [SalaryController::class, 'upload'])->name('salary.upload');
    Route::get('/salary/template', [SalaryController::class, 'exportTemplate'])->name('salary.template');
    Route::get('/salary/period/{id}', [SalaryController::class, 'showPeriod'])->name('salary.period');
    Route::post('/salary/period/{id}/publish', [SalaryController::class, 'publishPeriod'])->name('salary.period.publish');
    Route::delete('/salary/period/{id}', [SalaryController::class, 'destroyPeriod'])->name('salary.period.destroy');

    // Record CRUD
    Route::get('/salary/record/{id}/edit', [SalaryController::class, 'edit'])->name('salary.record.edit');
    Route::put('/salary/record/{id}', [SalaryController::class, 'update'])->name('salary.record.update');
    Route::delete('/salary/record/{id}', [SalaryController::class, 'destroy'])->name('salary.record.destroy');
});
