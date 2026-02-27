<?php

use App\Http\Controllers\Attendance\AdminAttendanceController;
use App\Http\Controllers\Attendance\AdminAttendanceLocationController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\MagicLinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocViewerController;
use App\Http\Controllers\HolidayCalendarController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\LeaveApprovalController;
use App\Http\Controllers\LeaveReportController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('auth')->controller(AuthController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
});

Route::get('/attendance/verify-location/{token}', [MagicLinkController::class, 'showLocationVerification'])
    ->name('attendance.verify-location');

Route::post('/attendance/verify-location/{token}', [MagicLinkController::class, 'verifyLocation'])
    ->name('attendance.verify-location.post');


Route::middleware('auth', 'auth.session')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
    });
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route::get('/test-form', function () {
    //     return view('my-tests.formtest');
    // })->name('test-form');

    Route::controller(LeaveApplicationController::class)->group(function () {
        Route::get('/leave-applications', 'index')->name('leave.applications.index');
        Route::get('/leave-application/add', 'create')->name('leave.application.create');
        Route::post('/leave-application/save', 'store')->name('leave.application.store');
        Route::get('/leave-application/{leave_application}', 'show')->name('leave.application.show');
        Route::get('/leave-application/edit/{leave_application}', 'edit')->name('leave.application.edit');
        Route::put('/leave-application/update/{leave_application}', 'update')->name('leave.application.update');
        Route::delete('/leave-application/destroy/{leave_application}', 'destroy')->name('leave.application.destroy');
        Route::post('/leave-application/{application}/replacement-response', 'replacementRespond')
            ->name('leave.replacement.respond');
    });

    Route::prefix('leave-reports')->name('leave.reports.')->group(function () {
        Route::get('/',           [LeaveReportController::class, 'index'])->name('index');
        Route::get('/pending',    [LeaveReportController::class, 'pending'])->name('pending');
        Route::get('/approved',   [LeaveReportController::class, 'approved'])->name('approved');
        Route::get('/rejected',   [LeaveReportController::class, 'rejected'])->name('rejected');
    });

    Route::get('leave-approvals/{approval}/approve', [LeaveApprovalController::class, 'approveForm'])->name('leave.approvals.form');
    Route::get('leave-approvals/{application}/approve/new', [LeaveApprovalController::class, 'approveFormNew'])->name('leave.approvals.form.new');
    Route::post('leave-approvals/{approval}/approve', [LeaveApprovalController::class, 'approve'])->name('leave.approvals.submit');

    Route::post('/dismiss-replacement-toast/{application}', function ($applicationId) {
        session()->put('replacement_toast_dismissed_' . $applicationId, true);
        return response()->json(['success' => true]);
    })->name('dismiss.replacement.toast');

    //  Student Management Routes -->
    Route::controller(StudentsController::class)->group(function () {
        Route::get('/students', 'index')->name('students.index');
        Route::get('/students/create', 'create')->name('students.create');
        Route::post('/students', 'store')->name('students.store');
        Route::get('/students/{student}', 'show')->name('students.show');
        Route::get('/students/{student}/edit', 'edit')->name('students.edit');
        Route::put('/students/{student}', 'update')->name('students.update');
        Route::delete('/students/{student}', 'destroy')->name('students.destroy');
        // Route::get('/students/{student}/print', 'print')->name('students.print');
    });

    Route::resource('staff', StaffController::class);
    Route::controller(HolidayCalendarController::class)->group(function () {
        Route::get('/holiday-calendars/events', 'events')->name('holiday-calendars.events');
        Route::get('/holiday-calendars', 'index')->name('holiday-calendars.index');
        Route::get('/holiday-calendars/create', 'create')->name('holiday-calendars.create');
        Route::post('/holiday-calendars', 'store')->name('holiday-calendars.store');
        Route::get('/holiday-calendars/{holidayCalendar}', 'show')->name('holiday-calendars.show');
        Route::get('/holiday-calendars/{holidayCalendar}/edit', 'edit')->name('holiday-calendars.edit');
        Route::put('/holiday-calendars/{holidayCalendar}', 'update')->name('holiday-calendars.update');
        Route::delete('/holiday-calendars/{holidayCalendar}', 'destroy')->name('holiday-calendars.destroy');
    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::controller(AttendanceController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/checkin-pending', 'checkInPending')->name('checkin-pending');
            Route::post('/checkout', 'checkOut')->name('checkout');
        });

        Route::controller(MagicLinkController::class)->group(function () {
            Route::post('/request-checkin', 'sendCheckInLink')->name('request-checkin');
        });
    });
    Route::prefix('admin/attendance')->name('admin.attendance.')->group(function () {
        Route::get('/', [AdminAttendanceController::class, 'index'])
            ->name('index');

        Route::get('/create', [AdminAttendanceController::class, 'create'])
            ->name('create');

        Route::post('/', [AdminAttendanceController::class, 'store'])
            ->name('store');

        Route::get('/{attendance}/edit', [AdminAttendanceController::class, 'edit'])
            ->name('edit');

        Route::put('/{attendance}', [AdminAttendanceController::class, 'update'])
            ->name('update');

        Route::delete('/{attendance}', [AdminAttendanceController::class, 'destroy'])
            ->name('destroy');

        // Location Settings Management
        Route::prefix('location')->name('location.')->group(function () {

            Route::get('/', [AdminAttendanceLocationController::class, 'index'])
                ->name('index');

            Route::get('/create', [AdminAttendanceLocationController::class, 'create'])
                ->name('create');

            Route::post('/', [AdminAttendanceLocationController::class, 'store'])
                ->name('store');

            Route::get('/{location}/edit', [AdminAttendanceLocationController::class, 'edit'])
                ->name('edit');

            Route::put('/{location}', [AdminAttendanceLocationController::class, 'update'])
                ->name('update');

            Route::delete('/{location}', [AdminAttendanceLocationController::class, 'destroy'])
                ->name('destroy');

            Route::patch('/{location}/set-default', [AdminAttendanceLocationController::class, 'setDefault'])
                ->name('set-default');
        });
    });
});
Route::middleware(['auth'])->get('docs/{student}/{type}/{file}', [DocViewerController::class, 'show'])
    ->name('doc.viewer');
