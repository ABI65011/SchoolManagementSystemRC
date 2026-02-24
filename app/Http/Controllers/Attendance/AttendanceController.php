<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\attendance;
use App\Models\AttendanceSetting;
use App\Helpers\CheckInMethod;
use App\Models\AttendanceLocation;
use App\Models\staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Show staff attendance dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();


        $staff = staff::with('user')->where('user_id', $user->id)->first();

        if (!$staff) {
            return view('attendance.staff.dashboard', [
                'todayAttendance' => null,
                'recentAttendance' => collect(),
                'error' => 'Your account is not linked to a staff profile.'
            ]);
        }

        $todayAttendance = attendance::where('staff_id', $staff->id)
            ->whereDate('attendance_date', today())
            ->first();

        $recentAttendance = attendance::where('staff_id', $staff->id)
            ->whereDate('attendance_date', '!=', today())
            ->orderBy('attendance_date', 'desc')
            ->take(10)
            ->get();

        return view('attendance.staff.dashboard', compact(
            'todayAttendance',
            'recentAttendance'
        ));
    }

    /**
     * Show check-in pending page
     */
    public function checkInPending()
    {
        return view('attendance.staff.checkin-pending');
    }

    /**
     * Manual check-out
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();

        $staff = staff::where('user_id', $user->id)->firstOrFail();

        $location = AttendanceLocation::where('is_default', true)->first();


        $attendance = attendance::where('staff_id', $staff->id)

            ->whereDate('attendance_date', today())
            ->whereNull('check_out')
            ->firstOrFail();


        $attendance->update([
            'check_out' => now(),
            'check_out_method' => CheckInMethod::Manual->value,
            'check_out_location' => json_encode([
                'lat' => $request->latitude,
                'lng' => $request->longitude,
            ]),
        ]);

        
        $attendance->calculateMetrics($location);

        return redirect()->route('attendance.dashboard')
            ->with('success', 'Check-out successful! Working hours: ' .
                number_format($attendance->working_hours, 2) . ' hours');
    }
}
