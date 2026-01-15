<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentPermissionController;
use App\Http\Controllers\StudentPermissionApprovalController;
use App\Http\Controllers\StudentPermissionCheckinController;

Route::middleware(['auth'])->group(function () {

    Route::prefix('master')->group(function () {

        Route::prefix('academic-years')->controller(AcademicYearController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('classes')->controller(SchoolClassController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('students')->controller(StudentController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('permissions')->group(function () {

        Route::controller(StudentPermissionController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/create', 'create');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
        });

        Route::controller(StudentPermissionApprovalController::class)->group(function () {
            Route::post('/{id}/approve', 'approve');
            Route::post('/{id}/reject', 'reject');
        });
    });

    Route::controller(StudentPermissionCheckinController::class)->group(function () {
        Route::get('/checkin/{qr_token}', 'scan');
        Route::post('/checkin/{qr_token}', 'store');
    });
});
