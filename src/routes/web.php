<?php

use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\StampCorrectionApproveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\StampCorrectionRequestController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified', 'not_admin'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');

    Route::get('/attendance/list', [AttendanceListController::class, 'index'])->name('attendance.list');
    Route::get('/attendance/detail/{attendance}', [AttendanceDetailController::class, 'show'])->name('attendance.detail');
    Route::post('/attendance/detail/{attendance}', [AttendanceDetailController::class, 'submitCorrection'])->name('attendance.detail.submit');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])->name('stamp_correction_request.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'daily'])->name('admin.attendance.daily');
    Route::get('/admin/attendance/{attendance}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');
    Route::put('/admin/attendance/{attendance}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
    Route::get('/admin/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/admin/attendance/staff/{user}', [AdminStaffController::class, 'monthlyAttendance'])->name('admin.staff.attendance');

    Route::get('/stamp_correction_request/approve/{stamp_correction_request}', [StampCorrectionApproveController::class, 'show'])->name('stamp_correction_request.approve');
    Route::post('/stamp_correction_request/approve/{stamp_correction_request}', [StampCorrectionApproveController::class, 'approve'])->name('stamp_correction_request.approve.store');
});
