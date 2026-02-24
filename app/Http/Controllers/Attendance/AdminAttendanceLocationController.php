<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAttendanceLocationController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        $locations = AttendanceLocation::orderBy('is_default', 'desc')

            ->orderBy('location_name')
            ->get();

        return view('attendance.admin.settings.index', compact('locations'));
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Show form to create new location
     */
    public function create()
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
            return view('attendance.admin.settings.create');
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        return view('attendance.admin.settings.create');
    }

    /**
     * Store new location
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        $validated = $request->validate([
            'location_name' => 'required|string|max:255',
            'school_latitude' => 'required|numeric|between:-90,90',
            'school_longitude' => 'required|numeric|between:-180,180',
            'geofence_radius' => 'required|integer|min:10|max:5000',
            'late_threshold' => 'required|date_format:H:i',
            'full_day_hours' => 'required|numeric|min:1|max:24',
            'working_day_start' => 'required|date_format:H:i',
            'working_day_end' => 'required|date_format:H:i',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);


        if (!empty($validated['is_default'])) {
            AttendanceLocation::where('is_default', true)->update(['is_default' => false]);
        }

        AttendanceLocation::create([
            'location_name' => $validated['location_name'],
            'school_latitude' => $validated['school_latitude'],
            'school_longitude' => $validated['school_longitude'],
            'geofence_radius' => $validated['geofence_radius'],
            'late_threshold' => $validated['late_threshold'],
            'full_day_hours' => $validated['full_day_hours'],
            'working_day_start' => $validated['working_day_start'],
            'working_day_end' => $validated['working_day_end'],
            'is_default' => $validated['is_default'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.attendance.location.index')
            ->with('success', 'Location added successfully.');
            } else {
                return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
            }
    }

    /**
     * Show form to edit location
     */
    public function edit(AttendanceLocation $location)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        return view('attendance.admin.settings.edit', compact('location'));
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Update location
     */
    public function update(Request $request, AttendanceLocation $location)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        $validated = $request->validate([
            'location_name' => 'required|string|max:255',
            'school_latitude' => 'required|numeric|between:-90,90',
            'school_longitude' => 'required|numeric|between:-180,180',
            'geofence_radius' => 'required|integer|min:10|max:5000',
            'late_threshold' => 'required|date_format:H:i',
            'full_day_hours' => 'required|numeric|min:1|max:24',
            'working_day_start' => 'required|date_format:H:i',
            'working_day_end' => 'required|date_format:H:i',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['is_default']) && !$location->is_default) {
            AttendanceLocation::where('is_default', true)->update(['is_default' => false]);
        }


        if (empty($validated['is_default']) && $location->is_default) {
            $count = AttendanceLocation::count();
            if ($count === 1) {
                return back()->with('error', 'You must have at least one default location.');
            }
        }

        $location->update([
            'location_name' => $validated['location_name'],
            'school_latitude' => $validated['school_latitude'],
            'school_longitude' => $validated['school_longitude'],
            'geofence_radius' => $validated['geofence_radius'],
            'late_threshold' => $validated['late_threshold'],
            'full_day_hours' => $validated['full_day_hours'],
            'working_day_start' => $validated['working_day_start'],
            'working_day_end' => $validated['working_day_end'],
            'is_default' => $validated['is_default'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.attendance.location.index')
            ->with('success', 'Location updated successfully.');
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Delete location
     */
    public function destroy(AttendanceLocation $location)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {
        if ($location->is_default) {
            return back()->with('error', 'Cannot delete the default location. Set another location as default first.');
        }

        $location->delete();

        return redirect()->route('admin.attendance.location.index')
            ->with('success', 'Location deleted successfully.');
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Set location as default
     */
    public function setDefault(AttendanceLocation $location)
    {
        if (Auth::user()->hasAnyRole('Admin|Super')) {

        AttendanceLocation::where('is_default', true)->update(['is_default' => false]);

        $location->update(['is_default' => true, 'is_active' => true]);

        return redirect()->route('admin.attendance.location.index')
            ->with('success', "{$location->location_name} is now the default location.");
        } else {
            return redirect()->route('attendance.dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }
}
