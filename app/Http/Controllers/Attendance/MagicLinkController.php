<?php

namespace App\Http\Controllers\Attendance;

use App\Helpers\AttendanceStatus;
use App\Helpers\CheckInMethod;
use App\Http\Controllers\Controller;
use App\Mail\MagicLinkMail;
use App\Models\attendance;
use App\Models\AttendanceLocation;
use App\Models\MagicLink;
use App\Models\staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MagicLinkController extends Controller
{
    /**
     * Send magic link to staff email for check-in
     */
    public function sendCheckInLink(Request $request)
    {
        $user = Auth::user();

        $staff = staff::with('user')->where('user_id', $user->id)->first();

        if (!$staff) {
            return redirect()->route('attendance.dashboard')
                ->with('error', 'Your account is not linked to a staff profile. Please contact administrator.');
        }


        $staffName = $staff->user->name;

        $existingAttendance = attendance::where('staff_id', $staff->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($existingAttendance) {
            return redirect()->route('attendance.dashboard')
                ->with('info', 'You have already checked in today.');
        }


        MagicLink::where('staff_id', $staff->id)
            ->whereDate('created_at', today())
            ->where('used', false)
            ->update(['used' => true]);


        $magicLink = MagicLink::create([
            'staff_id' => $staff->id,
            'token' => Str::random(32),
            'expires_at' => now()->addMinutes(15),
            'used' => false,
        ]);

        $location = AttendanceLocation::where('is_default', true)->first();


        Mail::to($staff->user->email ??  $user->email)->send(new MagicLinkMail(
            staffName: $staffName,
            magicLinkUrl: route('attendance.verify-location', $magicLink->token),
            expiresAt: $magicLink->expires_at->format('h:i A'),
            checkInDate: now()->format('l, F j, Y'),
            requestedAt: now()->format('h:i A'),
            locationName: $location->location_name,
            geofenceRadius: $location->geofence_radius
        ));

        return redirect()->route('attendance.checkin-pending');
    }

    /**
     * Show location verification page
     */
    public function showLocationVerification($token)
    {
        $magicLink = MagicLink::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $location = AttendanceLocation::where('is_default', true)->first();

        return view('attendance.staff.verify-location', [
            'token' => $token,
            'location' => $location,
        ]);
    }

    /**
     * Verify location and complete check-in
     */
    public function verifyLocation(Request $request, $token)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $magicLink = MagicLink::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $location = AttendanceLocation::where('is_default', true)->first();


        if (!$location->isWithinGeofence($request->latitude, $request->longitude)) {
            $distance = $location->distanceFrom($request->latitude, $request->longitude);

            return back()->with(
                'error',
                "You are " . round($distance) . " meters away from the school. " .
                    "Please move within {$location->geofence_radius} meters to check in."
            );
        }


        $existingAttendance = attendance::where('staff_id', $magicLink->staff_id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($existingAttendance) {
            $magicLink->update(['used' => true]);
            return redirect()->route('attendance.dashboard')
                ->with('info', 'You have already checked in today.');
        }


        $attendance = attendance::create([
            'staff_id' => $magicLink->staff_id,
            'attendance_date' => today(),
            'check_in' => now(),
            'check_in_method' => CheckInMethod::Magic_Link->value,
            'check_in_location' => json_encode([
                'lat' => $request->latitude,
                'lng' => $request->longitude,
            ]),
            'status' => AttendanceStatus::Present->value,
            'late_minutes' => 0,
            'early_departure_minutes' => 0,
            'working_hours' => 0,
            'is_holiday' => $this->isHoliday(today()),
            'is_weekend' => today()->isWeekend(),
            'overtime_hours' => 0,
        ]);


        $attendance->calculateMetrics($location);

        
        $magicLink->update(['used' => true]);

        return redirect()->route('attendance.dashboard')
            ->with('success', 'Check-in successful! You are now checked in.');
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
