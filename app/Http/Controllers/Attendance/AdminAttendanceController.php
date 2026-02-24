<?php

namespace App\Http\Controllers\Attendance;

use App\Helpers\AttendanceStatus;
use App\Helpers\CheckInMethod;
use App\Http\Controllers\Controller;
use App\Models\attendance;
use App\Models\AttendanceLocation;
use App\Models\staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
            $query = attendance::with('staff')->orderBy('attendance_date', 'desc');


            if ($request->filled('staff_id')) {
                $query->where('staff_id', $request->staff_id);
            }

            if ($request->filled('from_date')) {
                $query->whereDate('attendance_date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('attendance_date', '<=', $request->to_date);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $attendances = $query->paginate(25);
            $staffList = staff::orderBy('id')->get();

            return view('attendance.admin.index', compact('attendances', 'staffList'));
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Show form to create manual attendance
     */
    public function create()
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        $attendance = new Attendance();
        $staffList = staff::get();

        $statuses = collect(AttendanceStatus::cases())->mapWithKeys(
            fn($status) => [$status->value => ucfirst(str_replace('_', ' ', $status->value))]
        );

        $methods = collect(CheckInMethod::cases())->mapWithKeys(
            fn($method) => [$method->value => ucfirst(str_replace('_', ' ', $method->value))]
        );

        return view('attendance.admin.edit', compact(
            'attendance',
            'staffList',
            'statuses',
            'methods'
        ));
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Store manual attendance
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasAnyRole('Admin|Super')) {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date|after_or_equal:check_in',
            'status' => 'required|string',
            'check_in_method' => 'required|string',
            'check_out_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $location = AttendanceLocation::where('is_default', true)->first();



        $attendanceDate = Carbon::parse($validated['attendance_date']);

        $attendance = attendance::create([
            'staff_id' => $validated['staff_id'],
            'attendance_date' => $attendanceDate,
            'check_in' => $validated['check_in'] ? Carbon::parse($validated['check_in']) : null,
            'check_out' => $validated['check_out'] ? Carbon::parse($validated['check_out']) : null,
            'check_in_method' => $validated['check_in_method'],
            'check_out_method' => $validated['check_out_method'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'is_holiday' => $this->isHoliday($attendanceDate),
            'is_weekend' => $attendanceDate->isWeekend(),
        ]);


        if ($attendance->check_in) {
            $attendance->calculateMetrics($location);
        }

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record created successfully.');
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Show form to edit attendance
     */
    public function edit(Attendance $attendance)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        $staffList = staff::orderBy('user_id')->get();

        $statuses = collect(AttendanceStatus::cases())->mapWithKeys(
            fn($status) => [$status->value => ucfirst(str_replace('_', ' ', $status->value))]
        );

        $methods = collect(CheckInMethod::cases())->mapWithKeys(
            fn($method) => [$method->value => ucfirst(str_replace('_', ' ', $method->value))]
        );

        return view('attendance.admin.edit', compact(
            'attendance',
            'staffList',
            'statuses',
            'methods'
        ));
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Update attendance record
     */
    public function update(Request $request, Attendance $attendance)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date|after_or_equal:check_in',
            'status' => 'required|string',
            'check_in_method' => 'required|string',
            'check_out_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $location = AttendanceLocation::where('is_default', true)->first();

        $attendance->update([
            'staff_id' => $validated['staff_id'],
            'attendance_date' => Carbon::parse($validated['attendance_date']),
            'check_in' => $validated['check_in'] ? Carbon::parse($validated['check_in']) : null,
            'check_out' => $validated['check_out'] ? Carbon::parse($validated['check_out']) : null,
            'check_in_method' => $validated['check_in_method'],
            'check_out_method' => $validated['check_out_method'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);


        if ($attendance->check_in) {
            $attendance->calculateMetrics($location);
        }

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record updated successfully.');
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Delete attendance record
     */
    public function destroy(Attendance $attendance)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record deleted successfully.');
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }

    }

    /**
     * Check if date is a holiday
     */
    private function isHoliday(Carbon $date): bool
    {
        return DB::table('holiday_calendars')
            ->whereDate('date', $date)
            ->exists();
    }
}
