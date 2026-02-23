<?php

use App\Http\Controllers\DormitoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\PermissionVerifyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPermissionController;
use App\Http\Controllers\StudentPermissionApprovalController;
use App\Http\Controllers\StudentPermissionCheckinController;
use App\Http\Controllers\StudentPermissionLetterController;
use App\Http\Controllers\StudentTrackingController;
use App\Http\Controllers\StudentViolationController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;


Route::get('/tracking', [StudentTrackingController::class, 'index'])->name('tracking.index');
Route::get('/student/tracking', [StudentTrackingController::class, 'tracking']);


Route::get('/verify/permission', [PermissionVerifyController::class, 'permission'])->name('verify.permission');
Route::get('/verify/walas', [PermissionVerifyController::class, 'walas'])->name('verify.walas');

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

        Route::prefix('users')->controller(UserController::class)->group(function () {
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

        Route::prefix('dormitories')->controller(DormitoryController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::get('/permissions/pdf', [StudentPermissionController::class, 'pdf'])->name('permissions.pdf');
    Route::prefix('permissions')->group(function () {

        Route::controller(StudentPermissionController::class)->group(function () {
            Route::get('/check-violation/{student}', 'checkViolation');
            Route::post('/massal', 'storeMassal')->name('permissions.massal');
            Route::post('/upload-terlambat/{id}', 'uploadTerlambat')->name('permissions.upload-terlambat');
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
        Route::get('/checkin',  [StudentPermissionCheckinController::class, 'checkinView']);
        Route::post('/checkin', [StudentPermissionCheckinController::class, 'checkin']);

        Route::get('/checkout',  [StudentPermissionCheckinController::class, 'checkoutView']);
        Route::post('/checkout', [StudentPermissionCheckinController::class, 'checkout']);
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

    Route::get('/permissions/{id}/surat', [StudentPermissionLetterController::class, 'show'])->name('permissions.surat');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});


Auth::routes();
