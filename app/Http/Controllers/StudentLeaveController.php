<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StudentLeave::with(['student', 'authorizedBy', 'signedOutBy', 'signedInBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $students = Student::orderBy('first_name')->get();
        $statuses = ['pending', 'approved', 'denied', 'active', 'returned'];

        return view('student-leaves.index', compact('leaves', 'students', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::orderBy('first_name')->whereHas('admissions', function ($query) {
            $query->where('status', 'admitted');
        })->get();
        $types = ['pass_leave', 'emergency', 'weekend', 'medical'];

        return view('student-leaves.create', compact('students', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:pass_leave,emergency,weekend,medical',
            'departure_time' => 'required|date|after:now',
            'expected_return_time' => 'required|date|after:departure_time',
            'destination' => 'required|string|max:255',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $leave = StudentLeave::create([
                'student_id' => $request->student_id,
                'type' => $request->type,
                'departure_time' => $request->departure_time,
                'expected_return_time' => $request->expected_return_time,
                'destination' => $request->destination,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'status' => 'pending',
                'authorized_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('student-leaves.index')
                ->with('success', 'Leave request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create leave request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentLeave $studentLeave)
    {
        $studentLeave->load(['student', 'authorizedBy', 'signedOutBy', 'signedInBy']);
        return view('student-leaves.show', compact('studentLeave'));
    }

    public function approve(StudentLeave $studentLeave)
    {
        if ($studentLeave->status !== 'pending') {
            return back()->with('error', 'Leave request cannot be approved');
        }

        $studentLeave->approve();

        return back()->with('success', 'Leave request approved.');
    }

    public function deny(Request $request, StudentLeave $studentLeave)
    {
        if ($studentLeave->status !== 'pending') {
            return back()->with('error', 'Leave request cannot be denied');
        }

        $studentLeave->deny($request->reason);

        return back()->with('success', 'Leave request denied.');
    }

    public function signOut(StudentLeave $studentLeave)
    {
        if ($studentLeave->status !== 'approved') {
            return back()->with('error', 'Leave must be approved before sign out');
        }

        $studentLeave->signOut();

        return back()->with('success', 'Student signed out successfully.');
    }

    public function signIn(StudentLeave $studentLeave)
    {
        if ($studentLeave->status !== 'active') {
            return back()->with('error', 'Student is not on active leave');
        }

        $studentLeave->signIn();

        return back()->with('success', 'Student signed in successfully.');
    }

    public function activeLeaves()
    {
        $activeLeaves = StudentLeave::with(['student'])
            ->where('status', 'active')
            ->orderBy('expected_return_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activeLeaves,
            'count' => $activeLeaves->count(),
        ]);
    }

    public function overdueLeaves()
    {
        $overdueLeaves = StudentLeave::with(['student'])
            ->where('status', 'active')
            ->where('expected_return_time', '<', now())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $overdueLeaves,
            'count' => $overdueLeaves->count(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentLeave $studentLeave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentLeave $studentLeave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentLeave $studentLeave)
    {
        //
    }
}
