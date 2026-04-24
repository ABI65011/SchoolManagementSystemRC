<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use App\Models\StudentCheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentCheckInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with(['class', 'currentSponsorship', 'admissions'])->whereHas('admissions', function ($query) {
            $query->where('status', 'admitted');
        })->orderBy('first_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%")->orWhere('middle_name', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section')) {
            $query->where('applying_section', $request->section);
        }

        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'checked_in') {
                $query->whereHas('todayCheckIn');
            } elseif ($request->status_filter === 'not_checked_in') {
                $query->whereDoesntHave('todayCheckIn');
            }
        }

        $students = $query->paginate(2)->withQueryString();


        $todayCheckIns = StudentCheckIn::whereIn('student_id', $students->pluck('id'))
            ->where('check_in_date', now()->toDateString())
            ->get()
            ->keyBy('student_id');

        $classes = Classes::orderBy('name')->get();
        $sections = ['day' => 'Day', 'boarding' => 'Boarding'];

        return view('student-checkins.index', compact('students', 'todayCheckIns', 'classes', 'sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentCheckIn $studentCheckIn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentCheckIn $studentCheckIn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentCheckIn $studentCheckIn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentCheckIn $studentCheckIn)
    {
        //
    }

    public function checkIn(Request $request, Student $student)
    {
        $today = now()->toDateString();
        $now = now()->toTimeString();

        $existingCheckIn = StudentCheckIn::where('student_id', $student->id)
            ->where('check_in_date', $today)
            ->first();

        if ($existingCheckIn) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student already checked in today at ' . $existingCheckIn->formatted_time
                ], 422);
            }
            return back()->with('error', 'Student already checked in today');
        }

        try {
            DB::beginTransaction();

            $checkIn = StudentCheckIn::create([
                'student_id' => $student->id,
                'check_in_date' => $today,
                'check_in_time' => $now,
                'entered_by' => Auth::id(),
            ]);

            DB::commit();

            $responseData = [
                'success' => true,
                'message' => $student->full_name . ' checked in successfully at ' . $checkIn->formatted_time,
                'check_in' => [
                    'time' => $checkIn->formatted_time,
                    'entered_by' => Auth::user()->name,
                ]
            ];

            if ($request->wantsJson()) {
                return response()->json($responseData);
            }

            return back()->with('success', $responseData['message']);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record check-in: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to record check-in: ' . $e->getMessage());
        }
    }

    public function summary()
    {
        $today = now()->toDateString();

        $totalStudents = Student::where('status', 'admitted')->count();
        $checkedInCount = StudentCheckIn::where('check_in_date', $today)->count();
        $notCheckedInCount = $totalStudents - $checkedInCount;

        $bySection = [
            'day' => [
                'total' => Student::where('applying_section', 'day')->where('status', 'admitted')->count(),
                'checked_in' => StudentCheckIn::where('check_in_date', $today)
                    ->whereHas('student', fn($q) => $q->where('applying_section', 'day'))
                    ->count(),
            ],
            'boarding' => [
                'total' => Student::where('applying_section', 'boarding')->where('status', 'admitted')->count(),
                'checked_in' => StudentCheckIn::where('check_in_date', $today)
                    ->whereHas('student', fn($q) => $q->where('applying_section', 'boarding'))
                    ->count(),
            ],
        ];

        return response()->json([
            'total_students' => $totalStudents,
            'checked_in' => $checkedInCount,
            'not_checked_in' => $notCheckedInCount,
            'by_section' => $bySection,
        ]);
    }
}
