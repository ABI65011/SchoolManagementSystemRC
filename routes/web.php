<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocViewerController;
use App\Http\Controllers\HolidayCalendarController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('auth')->controller(AuthController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
});

Route::middleware('auth', 'auth.session')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
    });

    Route::get('/test-form', function () {
        return view('my-tests.formtest');
    })->name('test-form');

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

    // Route::resource('staff', StaffController::class);
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
});
Route::middleware(['auth'])->get('docs/{student}/{type}/{file}', [DocViewerController::class, 'show'])
    ->name('doc.viewer');
