<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentSponsorship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentSponsorshipController extends Controller
{
    public function assign(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:government,private',
            'reference_number' => 'required_if:type,government|nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $sponsorship = $student->assignSponsorship(
            $request->type,
            $request->reference_number,
            $request->notes
        );

        return back()->with('success', "Student sponsorship updated to " .
            ($sponsorship->type === 'government' ? 'UPE/USE' : 'Private'));
    }

    public function history(Student $student)
    {
        $sponsorships = $student->sponsorships()->with('approvedBy')->get();

        return view('students.sponsorship-history', compact('student', 'sponsorships'));
    }

    public function bulkAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'type' => 'required|in:government,private',
            'reference_prefix' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $count = 0;
        foreach ($request->student_ids as $studentId) {
            $student = Student::find($studentId);

            $reference = $request->reference_prefix
                ? $request->reference_prefix . '-' . $studentId
                : null;

            $student->assignSponsorship(
                $request->type,
                $reference,
                'Bulk assignment on ' . now()->toDateString()
            );
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "$count students updated to " .
                ($request->type === 'government' ? 'UPE/USE sponsorship' : 'private sponsorship')
        ]);
    }
}
