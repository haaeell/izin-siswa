<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentPermissionController;
use App\Http\Controllers\StudentPermissionApprovalController;
use App\Http\Controllers\StudentPermissionCheckinController;
use App\Http\Controllers\StudentViolationController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
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
            Route::get('/template', 'template');
            Route::post('/import', 'import');
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('teachers')->controller(TeacherController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('permissions')->group(function () {

        Route::controller(StudentPermissionController::class)->group(function () {
            Route::get('/check-violation/{student}', 'checkViolation');
            Route::get('/', 'index');
            Route::post('/', 'store')->name('permissions.store');
            Route::get('/{id}', 'show');
        });

        Route::controller(StudentPermissionApprovalController::class)->group(function () {
            Route::post('/{id}/approve', 'approve');
            Route::post('/{id}/reject', 'reject');
        });
    });

    Route::controller(StudentPermissionCheckinController::class)->group(function () {
        Route::get('/checkin', 'index');
        Route::post('/checkin', 'store');
        Route::post('/checkin-manual', 'manual');
    });

    Route::prefix('violations')->controller(StudentViolationController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('reports')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/pdf', 'exportPdf');
        Route::get('/excel', 'exportExcel');
    });
});

Route::get('/dev/reset-system', function () {
    Artisan::call('optimize:clear');
    Artisan::call('migrate:fresh', [
        '--seed' => true,
        '--force' => true,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Cache cleared & database migrated fresh with seed'
    ]);
});

Auth::routes();
