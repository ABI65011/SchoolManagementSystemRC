<?php

namespace App\Http\Controllers;

use App\Helpers\AdmissionStatus;
use App\Models\Admission;
use App\Models\Classes;
use App\Models\Stream;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (Auth::user()->hasAnyRole('Admin|Super|Staff')) {
            $query = Admission::with([
                'student.academicHistories',
                'student.medicalHistory',
                'student.disciplineHistory',
                'student.careerAspiration',
                'user'
            ]);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('students_id')) {
                $query->where('students_id', $request->students_id);
            }
            if ($request->filled('admitted_id')) {
                $query->where('admitted_id', $request->admitted_id);
            }
            $admissions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

            $studentsWithoutAdmissions = Student::whereDoesntHave('admissions')->get();
            // dd($studentsWithoutAdmissions);
            foreach ($studentsWithoutAdmissions as $student)
                {
                    Admission::create([
                    'student_id' => $student->id,
                    'status' => AdmissionStatus::Pending->value,]);
                }

            return view('admission.index', compact('admissions'));
        }
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
    public function show(Admission $admission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admission $admission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admission $admission)
    {
        Log::info('UPDATE ADMISSION REACHED', [
            'admission_id' => $admission->id,
            'status' => $request->status,
            'user_id' => Auth::id()
        ]);
        $request->validate([
            'status' => 'required|in:admitted,rejected',
            'comments' => 'required_if:status,rejected|nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $statusValue = ucfirst($request->status);

            $admission->update([
                'status' => AdmissionStatus::from(strtolower($request->status)),
                'comments' => $request->comments,
                'admitted_by' => Auth::id(),
            ]);

            if ($request->status === 'admitted') {
                $this->assignStudentToClassAndStream($admission->student);
            }

            Log::info('Admission record updated successfully', [
                'admission_id' => $admission->id,
                'new_status' => $admission->status->value,
                'admitted_by' => $admission->admitted_by
            ]);

            DB::commit();

            $message = $request->status === 'admitted' ? 'Student admitted successfully.' : 'Admission rejected.';
            return back()->with('success', $message);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('ADMISSION UPDATE EXCEPTION', [
                'msg' => $th->getMessage(),
                'admission_id' => $admission->id,
                'trace' => $th->getTraceAsString()
            ]);

            return back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admission $admission)
    {
        //
    }

    private function assignStudentToClassAndStream(Student $student): void
    {
        $class = $this->findClass($student->joining_class);

        if (!$class) {
            throw new \Exception("Class not found for joining class: " . $student->joining_class);
        }

        $streams = Stream::where('class_id', $class->id)
            ->where('is_active', true)
            ->get();

        if ($streams->isEmpty()) {
            throw new \Exception("No streams available for class: " . $class->name);
        }

        $randomStream = $streams->random();

        $existingPivot = DB::table('class_student')
            ->where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->first();

        if (!$existingPivot) {

            DB::table('class_student')
                ->where('student_id', $student->id)
                ->update(['is_current' => false]);


            DB::table('class_student')->insert([
                'class_id' => $class->id,
                'student_id' => $student->id,
                'stream' => $randomStream->name,
                'is_current' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Student assigned to class and stream', [
                'student_id' => $student->id,
                'class_id' => $class->id,
                'class_name' => $class->name,
                'stream' => $randomStream->name
            ]);
        }
    }

    /**
     * Find class by joining_class name
     */
    private function findClass(string $joiningClass): ?Classes
    {
        $class = Classes::where('name', $joiningClass)->first();

        if ($class) {
            return $class;
        }

        $standardized = $this->standardizeClassName($joiningClass);

        if ($standardized) {
            return Classes::where('name', $standardized)->first();
        }

        return null;
    }


    private function standardizeClassName(string $className): ?string
    {
        $className = strtoupper(trim($className));
        $clean = preg_replace('/[.\s]/', '', $className);

        $map = [
            'S1' => 'S1',
            'S.1' => 'S1',
            'SENIOR1' => 'S1',
            'FORM1' => 'S1',
            'S2' => 'S2',
            'S.2' => 'S2',
            'SENIOR2' => 'S2',
            'FORM2' => 'S2',
            'S3' => 'S3',
            'S.3' => 'S3',
            'SENIOR3' => 'S3',
            'FORM3' => 'S3',
            'S4' => 'S4',
            'S.4' => 'S4',
            'SENIOR4' => 'S4',
            'FORM4' => 'S4',
            'S5' => 'S5',
            'S.5' => 'S5',
            'SENIOR5' => 'S5',
            'FORM5' => 'S5',
            'S6' => 'S6',
            'S.6' => 'S6',
            'SENIOR6' => 'S6',
            'FORM6' => 'S6',
        ];

        return $map[$clean] ?? $map[$className] ?? null;
    }


    public function bulkAdmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admission_ids' => 'required|array',
            'admission_ids.*' => 'exists:admissions,id',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($request->admission_ids as $id) {
            $admission = Admission::with('student')->find($id);

            if (!$admission || $admission->status !== AdmissionStatus::Pending) {
                $failed++;
                $errors[] = "Admission ID {$id} is not pending or doesn't exist";
                continue;
            }

            try {
                DB::beginTransaction();

                $admission->update([
                    'status' => AdmissionStatus::Admitted,
                    'admitted_by' => Auth::id(),
                ]);

                $this->assignStudentToClassAndStream($admission->student);

                DB::commit();
                $success++;
            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
                $errors[] = "Failed to admit student {$admission->student->full_name}: " . $e->getMessage();

                Log::error('Bulk admit failed', [
                    'admission_id' => $admission->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = "Bulk admission completed. Success: $success, Failed: $failed";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'success_count' => $success,
                    'failed_count' => $failed,
                    'errors' => $errors
                ]
            ]);
        }

        if ($failed > 0) {
            return back()->with('warning', $message . ' Check logs for details.');
        }

        return back()->with('success', $message);
    }
}
