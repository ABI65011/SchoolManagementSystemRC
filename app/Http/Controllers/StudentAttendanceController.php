<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDiscrepancy;
use App\Models\Classes;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentAttendance::with(['student', 'class', 'recordedBy']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('date')) {
            $query->where('attendance_date', $request->date);
        }

        if ($request->filled('sponsorship')) {
            $query->where('sponsorship', $request->sponsorship);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        $classes = Classes::orderBy('name')->get();

        return view('student-attendance.index', compact('attendances', 'classes'));
    }

    /**
     * Show bulk attendance entry form.
     */
    public function bulkCreate(Request $request)
    {
        $classId = $request->class_id;
        $date = $request->date ?? now()->toDateString();

        if (!$classId) {
            $classes = Classes::orderBy('name')->get();
            return view('student-attendance.select-class', compact('classes'));
        }

        $class = Classes::with(['students' => function ($q) {
            $q->orderBy('first_name');
        }])->findOrFail($classId);


        $existingAttendances = StudentAttendance::where('class_id', $classId)
            ->where('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        $students = $class->students;

        return view('student-attendance.bulk', compact('class', 'students', 'date', 'existingAttendances'));
    }

    /**
     * Store bulk attendance.
     */
    public function bulkStore(Request $request)
    {
        Log::info('BULK ATTENDANCE STORE', $request->all());

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'attendance_date' => 'required|date',
            'students' => 'required|array',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.status' => 'required|in:present,absent,late,excused,holiday',
            'students.*.check_in_time' => 'nullable|date_format:H:i',
            'students.*.absence_reason' => 'nullable|string|max:255',
            'physical_headcount' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $presentCount = 0;

            foreach ($validated['students'] as $studentData) {
                $lateMinutes = 0;
                if (!empty($studentData['check_in_time']) && $studentData['status'] === 'late') {
                    $checkIn = \Carbon\Carbon::parse($studentData['check_in_time']);
                    $lateThreshold = \Carbon\Carbon::parse('08:30');
                    if ($checkIn->gt($lateThreshold)) {
                        $lateMinutes = $lateThreshold->diffInMinutes($checkIn);
                    }
                }

                $student = Student::find($studentData['student_id']);


                $sponsorship = $student->sponsorship_type;


                if (in_array($studentData['status'], ['present', 'late'])) {
                    $presentCount++;
                }

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $studentData['student_id'],
                        'attendance_date' => $validated['attendance_date'],
                        'class_id' => $validated['class_id'],
                    ],
                    [
                        'status' => $studentData['status'],
                        'sponsorship' => $sponsorship,
                        'check_in_time' => $studentData['check_in_time'] ?? null,
                        'late_minutes' => $lateMinutes,
                        'absence_reason' => $studentData['absence_reason'] ?? null,
                        'recorded_by' => Auth::id(),
                    ]
                );
            }


            if ($request->filled('physical_headcount')) {
                $this->verifyHeadcount(
                    $validated['class_id'],
                    $validated['attendance_date'],
                    $request->physical_headcount,
                    $presentCount
                );
            }

            DB::commit();

            return redirect()->route('student-attendance.index')
                ->with('success', 'Attendance recorded successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('BULK ATTENDANCE ERROR', ['msg' => $th->getMessage()]);
            return back()->with('error', 'Failed to save attendance: ' . $th->getMessage());
        }
    }

    /**
     * Verify headcount for anti-ghost compliance.
     */
    /**
     * Verify headcount for anti-ghost compliance.
     */
    private function verifyHeadcount($classId, $date, $physicalCount, $presentCount = null)
    {

        $systemCount = $presentCount ?? StudentAttendance::where('class_id', $classId)
            ->where('attendance_date', $date)
            ->whereIn('status', ['present', 'late'])
            ->count();

        if ($physicalCount != $systemCount) {
            $difference = abs($physicalCount - $systemCount);

            $notes = $physicalCount > $systemCount
                ? "Physical count higher by $difference students. Some students may have been missed in system."
                : "System count higher by $difference students. Possible ghost students or data entry error.";

            AttendanceDiscrepancy::create([
                'class_id' => $classId,
                'date' => $date,
                'physical_count' => $physicalCount,
                'system_count' => $systemCount,
                'difference' => $difference,
                'reported_by' => Auth::id(),
                'notes' => $notes,
            ]);

            $alertType = $difference <= 2 ? 'warning' : 'danger';
            session()->flash($alertType, "Headcount mismatch! Physical: $physicalCount, System (Present+Late): $systemCount. Difference: $difference students.");
        } else {

            AttendanceDiscrepancy::where('class_id', $classId)
                ->where('date', $date)
                ->update(['resolved' => true]);
        }
    }
    /**
     * Show attendance report for a class.
     */
    public function classReport(Classes $class, Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        $attendances = StudentAttendance::where('class_id', $class->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->with('student')
            ->get()
            ->groupBy('student_id');

        $summary = [
            'total_students' => $class->students()->count(),
            'government_students' => $class->students()->where('sponsorship', 'government')->count(),
            'private_students' => $class->students()->where('sponsorship', 'private')->count(),
            'total_days' => \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1,
            'average_attendance' => 0,
            'government_attendance' => 0,
            'private_attendance' => 0,
        ];

        return view('student-attendance.report', compact('class', 'attendances', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Generate MoES compliance report.
     */
    public function moesReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();
        $classId = $request->class_id;

        $query = StudentAttendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->with(['student', 'class']);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $attendances = $query->get();

        $report = [
            'period' => \Carbon\Carbon::parse($startDate)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y'),
            'government' => [
                'total_present' => $attendances->where('sponsorship', 'government')->where('status', 'present')->count(),
                'total_absent' => $attendances->where('sponsorship', 'government')->where('status', 'absent')->count(),
                'total_late' => $attendances->where('sponsorship', 'government')->where('status', 'late')->count(),
                'attendance_rate' => 0,
            ],
            'private' => [
                'total_present' => $attendances->where('sponsorship', 'private')->where('status', 'present')->count(),
                'total_absent' => $attendances->where('sponsorship', 'private')->where('status', 'absent')->count(),
                'total_late' => $attendances->where('sponsorship', 'private')->where('status', 'late')->count(),
                'attendance_rate' => 0,
            ],
            'discrepancies' => AttendanceDiscrepancy::whereBetween('date', [$startDate, $endDate])
                ->with('class')
                ->get(),
            'classes' => Classes::withCount('students')->get(),
        ];

        
        $totalGovernmentDays = $attendances->where('sponsorship', 'government')->count();
        $totalPrivateDays = $attendances->where('sponsorship', 'private')->count();

        $report['government']['attendance_rate'] = $totalGovernmentDays > 0
            ? round(($report['government']['total_present'] / $totalGovernmentDays) * 100, 2) : 0;
        $report['private']['attendance_rate'] = $totalPrivateDays > 0
            ? round(($report['private']['total_present'] / $totalPrivateDays) * 100, 2) : 0;

        $classes = Classes::orderBy('name')->get();

        return view('student-attendance.moes-report', compact('report', 'startDate', 'endDate', 'classes', 'classId'));
    }

    /**
     * Edit a single attendance record.
     */
    public function edit(StudentAttendance $attendance)
    {
        return view('student-attendance.edit', compact('attendance'));
    }

    /**
     * Update a single attendance record.
     */
    public function update(Request $request, StudentAttendance $attendance)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:present,absent,late,excused,holiday',
            'check_in_time' => 'nullable|date_format:H:i',
            'absence_reason' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $lateMinutes = 0;
        if ($request->status === 'late' && $request->check_in_time) {
            $checkIn = \Carbon\Carbon::parse($request->check_in_time);
            $lateThreshold = \Carbon\Carbon::parse('08:30');
            if ($checkIn->gt($lateThreshold)) {
                $lateMinutes = $lateThreshold->diffInMinutes($checkIn);
            }
        }

        $attendance->update([
            'status' => $request->status,
            'check_in_time' => $request->check_in_time,
            'late_minutes' => $lateMinutes,
            'absence_reason' => $request->absence_reason,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('student-attendance.index')
            ->with('success', 'Attendance updated successfully.');
    }
}
