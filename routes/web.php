<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\Attendance\AdminAttendanceController;
use App\Http\Controllers\Attendance\AdminAttendanceLocationController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\MagicLinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContinuousAssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocViewerController;
use App\Http\Controllers\ExamCategoryController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\GradingScaleController;
use App\Http\Controllers\GradingScaleItemController;
use App\Http\Controllers\HolidayCalendarController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\LeaveApprovalController;
use App\Http\Controllers\LeaveReportController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentCheckInController;
use App\Http\Controllers\StudentLeaveController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SubjectController;
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

    Route::controller(StudentAttendanceController::class)->prefix('student-attendance')->name('student-attendance.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/bulk', 'bulkCreate')->name('bulk');
        Route::post('/bulk', 'bulkStore')->name('bulk-store');
        Route::get('/class/{class}/report', 'classReport')->name('class-report');
        Route::get('/moes-report', 'moesReport')->name('moes-report');
        Route::get('/{attendance}/edit', 'edit')->name('edit');
        Route::put('/{attendance}', 'update')->name('update');
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
        Route::post('/students/update-sponsorship', 'updateSponsorship')->name('students.update-sponsorship');
        Route::post('/students/bulk-sponsorship', 'bulkSponsorship')->name('students.bulk-sponsorship');
        Route::get('/students/government-sponsored', 'governmentSponsored')->name('students.government-sponsored');
        Route::get('/students/{student}', 'show')->name('students.show');
        Route::get('/students/{student}/edit', 'edit')->name('students.edit');
        Route::put('/students/{student}', 'update')->name('students.update');
        Route::delete('/students/{student}', 'destroy')->name('students.destroy');
        // Route::get('/students/{student}/print', 'print')->name('students.print');
    });

    Route::controller(StudentCheckInController::class)->group(function () {
        Route::get('/student-checkins', 'index')->name('student-checkins.index');
        Route::post('/student-checkins/{student}/check-in', 'checkin')->name('student-checkins.check-in');
        Route::get('student-checkins/summary', 'summary')->name('student-checkins.summary');
    });

    Route::controller(StudentLeaveController::class)->prefix('student-leaves')->name('student-leaves.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{studentLeave}', 'show')->name('show');
        Route::post('/{studentLeave}/approve', 'approve')->name('approve');
        Route::post('/{studentLeave}/deny', 'deny')->name('deny');
        Route::post('/{studentLeave}/sign-out', 'signOut')->name('sign-out');
        Route::post('/{studentLeave}/sign-in', 'signIn')->name('sign-in');
        Route::get('/active/list', 'activeLeaves')->name('active-list');
        Route::get('/overdue/list', 'overdueLeaves')->name('overdue-list');
    });

    Route::resource('staff', StaffController::class);
    Route::resource('subjects', SubjectController::class);
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

    Route::controller(ExamCategoryController::class)->group(function () {
        Route::get('/exam-categories', 'index')->name('exam-categories.index');
        Route::get('/exam-categories/create', 'create')->name('exam-categories.create');
        Route::post('/exam-categories', 'store')->name('exam-categories.store');
        Route::get('/exam-categories/{examCategory}', 'show')->name('exam-categories.show');
        Route::get('/exam-categories/{examCategory}/edit', 'edit')->name('exam-categories.edit');
        Route::put('/exam-categories/{examCategory}', 'update')->name('exam-categories.update');
        Route::delete('/exam-categories/{examCategory}', 'destroy')->name('exam-categories.destroy');
    });

    Route::controller(AdmissionController::class)->group(function () {
        Route::get('/admissions', 'index')->name('admissions.index');
        Route::get('/admissions/create', 'create')->name('admissions.create');
        Route::post('/admissions', 'store')->name('admissions.store');
        Route::get('/admissions/{admission}', 'show')->name('admissions.show');
        Route::get('/admissions/{admission}/edit', 'edit')->name('admissions.edit');
        Route::put('/admissions/{admission}', 'update')->name('admissions.update');
        Route::delete('/admissions/{admission}', 'destroy')->name('admissions.destroy');
        Route::post('/admissions/{admission}/update-status', 'updateStatus')->name('admissions.update-status');
        Route::post('/bulk-admit', 'bulkAdmit')->name('admissions.bulk-admit');
    });


    Route::controller(ExamController::class)->group(function () {
        Route::get('/exams', 'index')->name('exams.index');
        Route::get('/exams/create', 'create')->name('exams.create');
        Route::post('/exams', 'store')->name('exams.store');
        Route::get('/exams/{exam}', 'show')->name('exams.show');
        Route::get('/exams/{exam}/edit', 'edit')->name('exams.edit');
        Route::put('/exams/{exam}', 'update')->name('exams.update');
        Route::delete('/exams/{exam}', 'destroy')->name('exams.destroy');
        Route::post('/exams/{exam}/update-status', 'updateStatus')->name('exams.update-status');
        Route::get('/exams/{exam}/results-data', 'getResultsData')->name('exams.results-data');
    });

    Route::controller(ExamResultController::class)->group(function () {
        Route::get('/exam-results', 'index')->name('exam-results.index');
        Route::get('/exams/{exam}/results/student/{student}', 'studentExamResults')->name('exam-results.student-exam-results');
        Route::get('/exam-results/{examResult}/edit', 'edit')->name('exam-results.edit');
        Route::put('/exam-results/{examResult}', 'update')->name('exam-results.update');
        Route::delete('/exam-results/{examResult}', 'destroy')->name('exam-results.destroy');
        Route::get('/exams/{exam}/results/create', 'create')->name('exam-results.create');
        Route::post('/exams/{exam}/results', 'store')->name('exam-results.store');
        Route::get('/exams/{exam}/results/upload', 'uploadForm')->name('exam-results.upload');
        Route::post('/exams/{exam}/results/upload', 'upload')->name('exam-results.upload.process');
        Route::get('/exams/{exam}/results/template', 'downloadTemplate')->name('exam-results.template');
        Route::post('/exams/{exam}/results/release', 'releaseResults')->name('exam-results.release');
        Route::get('/exams/{exam}/results/print/{student}', 'printResult')->name('exam-results.print');
    });

    Route::controller(ReportCardController::class)->group(function () {
        Route::get('/report-cards', 'index')->name('report-cards.index');
        Route::get('/report-cards/create', 'create')->name('report-cards.create');
        Route::post('/report-cards', 'store')->name('report-cards.store');
        Route::get('/report-cards/{reportCard}', 'show')->name('report-cards.show');
        Route::get('/report-cards/{reportCard}/edit', 'edit')->name('report-cards.edit');
        Route::put('/report-cards/{reportCard}', 'update')->name('report-cards.update');
        Route::delete('/report-cards/{reportCard}', 'destroy')->name('report-cards.destroy');
        Route::post('/report-cards/{reportCard}/publish', 'publish')->name('report-cards.publish');
        Route::get('/report-cards/{reportCard}/download', 'download')->name('report-cards.download');
        Route::post('/report-cards/bulk-generate', 'bulkGenerate')->name('report-cards.bulk-generate');
    });

    Route::controller(ContinuousAssessmentController::class)->group(function () {
        Route::get('/continuous-assessments', 'index')->name('continuous-assessments.index');
        Route::get('/continuous-assessments/create', 'create')->name('continuous-assessments.create');
        Route::post('/continuous-assessments', 'store')->name('continuous-assessments.store');
        Route::get('/continuous-assessments/{continuousAssessment}', 'show')->name('continuous-assessments.show');
        Route::get('/continuous-assessments/{continuousAssessment}/edit', 'edit')->name('continuous-assessments.edit');
        Route::put('/continuous-assessments/{continuousAssessment}', 'update')->name('continuous-assessments.update');
        Route::delete('/continuous-assessments/{continuousAssessment}', 'destroy')->name('continuous-assessments.destroy');
        Route::get('/students/{student}/continuous-assessments', 'studentAssessments')->name('students.continuous-assessments');
        Route::get('/continuous-assessments/bulk/{class}', 'bulkCreate')->name('continuous-assessments.bulk-create');
        Route::post('/continuous-assessments/bulk/{class}', 'bulkStore')->name('continuous-assessments.bulk-store');
    });

    Route::controller(GradingScaleController::class)->group(function () {
        Route::get('/grading-scales', 'index')->name('grading-scales.index');
        Route::get('/grading-scales/create', 'create')->name('grading-scales.create');
        Route::post('/grading-scales', 'store')->name('grading-scales.store');
        Route::get('/grading-scales/{gradingScale}', 'show')->name('grading-scales.show');
        Route::get('/grading-scales/{gradingScale}/edit', 'edit')->name('grading-scales.edit');
        Route::put('/grading-scales/{gradingScale}', 'update')->name('grading-scales.update');
        Route::delete('/grading-scales/{gradingScale}', 'destroy')->name('grading-scales.destroy');
        Route::post('/grading-scales/{gradingScale}/set-default', 'setDefault')->name('grading-scales.set-default');
    });

    Route::controller(GradingScaleItemController::class)->group(function () {
        Route::get('/grading-scales/{gradingScale}/items', 'index')->name('grading-scale-items.index');
        Route::get('/grading-scales/{gradingScale}/items/create', 'create')->name('grading-scale-items.create');
        Route::post('/grading-scales/{gradingScale}/items', 'store')->name('grading-scale-items.store');
        Route::post('/grading-scale-items/{gradingScaleItem}/reorder', 'reorder')->name('grading-scale-items.reorder');
        Route::get('/grading-scale-items/{gradingScaleItem}/edit', 'edit')->name('grading-scale-items.edit');
        Route::put('/grading-scale-items/{gradingScaleItem}', 'update')->name('grading-scale-items.update');
        Route::delete('/grading-scale-items/{gradingScaleItem}', 'destroy')->name('grading-scale-items.destroy');
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
