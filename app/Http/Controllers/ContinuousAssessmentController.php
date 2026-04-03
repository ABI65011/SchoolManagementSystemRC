<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\Term;
use App\Models\ContinuousAssessment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\AssessmentType;
use App\Models\Classes;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContinuousAssessmentController extends Controller
{
    protected $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContinuousAssessment::with(['student', 'subject', 'assessmentType', 'class', 'recordedBy']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $assessments = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $classes = Classes::with(['currentStudents'])->get();
        $subjects = Subject::orderBy('name')->get();
        $terms = Term::cases();
        $years = range(now()->year - 2, now()->year + 2);

        return view('continuous-assessments.index', compact('assessments', 'classes', 'subjects', 'terms', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = Classes::with(['currentStudents'])->get();

        $subjects = Subject::orderBy('name')->get();
        $assessmentTypes = AssessmentType::where('category', 'continuous')->get();
        $terms = Term::cases();

        return view('continuous-assessments.create', compact('classes', 'subjects', 'assessmentTypes', 'terms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('STORE CONTINUOUS ASSESSMENT REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type_id' => 'required|exists:assessment_types,id',
            'class_id' => 'nullable|exists:classes,id',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2000|max:2100',
            'title' => 'nullable|string|max:255',
            'raw_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:1|max:100',
            'teacher_comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        if (empty($validated['class_id'])) {
            $student = Student::with('currentClass')->find($validated['student_id']);
            if ($student && $student->currentClass) {
                $validated['class_id'] = $student->currentClass->id;
            } else {
                return back()->with('error', 'Student does not have a current class assignment.')->withInput();
            }
        }

        $validated['recorded_by'] = Auth::id();

        try {
            DB::beginTransaction();

            $assessment = ContinuousAssessment::create($validated);
            Log::info('Continuous Assessment CREATED', ['id' => $assessment->id]);

            DB::commit();
            return redirect()->route('continuous-assessments.index')
                ->with('success', 'Continuous assessment recorded successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('EXCEPTION', ['msg' => $th->getMessage()]);
            return back()->with('error', 'Something went wrong: ' . $th->getMessage())->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(ContinuousAssessment $continuousAssessment)
    {
        $continuousAssessment->load(['student', 'subject', 'assessmentType', 'class', 'recordedBy']);

        return view('continuous-assessments.show', compact('continuousAssessment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContinuousAssessment $continuousAssessment)
    {
        $subjects = Subject::orderBy('name')->get();
        $assessmentTypes = AssessmentType::where('category', 'continuous')->get();
        $terms = Term::cases();

        return view('continuous-assessments.edit', compact('continuousAssessment', 'subjects', 'assessmentTypes', 'terms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContinuousAssessment $continuousAssessment)
    {
        Log::info('UPDATE CONTINUOUS ASSESSMENT REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type_id' => 'required|exists:assessment_types,id',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2000|max:2100',
            'title' => 'nullable|string|max:255',
            'raw_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:1|max:100',
            'teacher_comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $continuousAssessment->update($validated);
            Log::info('Continuous Assessment UPDATED', ['id' => $continuousAssessment->id]);

            DB::commit();
            return redirect()->route('continuous-assessments.show', $continuousAssessment)
                ->with('success', 'Continuous assessment updated successfully.');
        } catch (\Throwable $th) {
            Log::error('EXCEPTION INSIDE TRY', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            DB::rollBack();
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContinuousAssessment $continuousAssessment)
    {
        try {
            $continuousAssessment->delete();
            return redirect()->route('continuous-assessments.index')
                ->with('success', 'Continuous assessment deleted successfully.');
        } catch (\Throwable $th) {
            Log::error('DELETE ERROR', ['msg' => $th->getMessage()]);
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()));
        }
    }

    /**
     * Display student's continuous assessments.
     */
    public function studentAssessments(Student $student, Request $request)
    {
        $query = ContinuousAssessment::where('student_id', $student->id)
            ->with(['subject', 'assessmentType']);

        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $assessments = $query->orderBy('year', 'desc')
            ->orderBy('term')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(['year', 'term']);

        $terms = Term::cases();
        $years = range(now()->year - 2, now()->year + 2);

        return view('continuous-assessments.student', compact('student', 'assessments', 'terms', 'years'));
    }

    /**
     * Bulk store for class.
     */
    public function bulkCreate(Classes $class, Request $request)
    {
        $subjects = Subject::orderBy('name')->get();
        $assessmentTypes = AssessmentType::where('category', 'continuous')->get();
        $terms = Term::cases();
        $students = $class->students()->orderBy('first_name')->get();

        return view('continuous-assessments.bulk-create', compact('class', 'students', 'subjects', 'assessmentTypes', 'terms'));
    }

    /**
     * Bulk store for class.
     */
    public function bulkStore(Request $request, Classes $class)
    {
        Log::info('BULK STORE CONTINUOUS ASSESSMENT REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type_id' => 'required|exists:assessment_types,id',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2000|max:2100',
            'title' => 'nullable|string|max:255',
            'max_score' => 'required|numeric|min:1|max:100',
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.raw_score' => 'nullable|numeric|min:0|max:100',
            'scores.*.teacher_comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $count = 0;
            foreach ($validated['scores'] as $scoreData) {
                if ($scoreData['raw_score'] !== null) {
                    ContinuousAssessment::create([
                        'student_id' => $scoreData['student_id'],
                        'subject_id' => $validated['subject_id'],
                        'assessment_type_id' => $validated['assessment_type_id'],
                        'class_id' => $class->id,
                        'term' => $validated['term'],
                        'year' => $validated['year'],
                        'title' => $validated['title'],
                        'raw_score' => $scoreData['raw_score'],
                        'max_score' => $validated['max_score'],
                        'teacher_comment' => $scoreData['teacher_comment'] ?? null,
                        'recorded_by' => Auth::id(),
                    ]);
                    $count++;
                }
            }

            Log::info('Bulk Continuous Assessments CREATED', ['count' => $count]);

            DB::commit();
            return redirect()->route('continuous-assessments.index', ['class_id' => $class->id])
                ->with('success', "$count continuous assessments recorded successfully.");
        } catch (\Throwable $th) {
            Log::error('EXCEPTION INSIDE TRY', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            DB::rollBack();
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))->withInput();
        }
    }

}
