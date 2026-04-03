<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\ExamStatus;
use App\Helpers\Term;
use App\Models\Classes;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\GradingScale;
use App\Services\GradingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{

    use AuthorizesRequests;
    protected $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
        // $this->authorizeResource(Exam::class, 'exam');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['class', 'category', 'gradingScale']);

        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $exams = $query->orderBy('exam_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        $classes = Classes::all();
        $years = range(now()->year - 2, now()->year + 2);

        return view('exams.index', compact('exams', 'classes', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ExamCategory::with('mainCategory')
            ->get()
            ->groupBy(fn($cat) => $cat->mainCategory?->name ?? 'Main Categories');

        $classes = Classes::all();
        $gradingScales = GradingScale::active()->get();
        $terms = Term::cases();
        $statuses = ExamStatus::cases();

        return view('exams.create', compact(
            'categories',
            'classes',
            'gradingScales',
            'terms',
            'statuses'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('STORE ROUTE REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'exam_category_id' => 'nullable|exists:exam_categories,id',
            'grading_scale_id' => 'nullable|exists:grading_scales,id',
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2000|max:2100',
            'exam_date' => 'nullable|date',
            'entry_start_date' => 'nullable|date',
            'entry_end_date' => 'nullable|date|after_or_equal:entry_start_date',
            'result_release_date' => 'nullable|date|after:exam_date',
            'max_mark' => 'required|numeric|min:1|max:500',
            'weight' => 'required|numeric|min:0|max:10',
            'requires_continuous_assessment' => 'boolean',
            'status' => 'required|in:draft,published,ongoing,completed,results_released',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        $validated = $validator->validated();
        $validated['requires_continuous_assessment'] = $request->boolean('requires_continuous_assessment');

        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(substr($validated['name'], 0, 3)) . '-' . $validated['year'];
        }

        try {
            DB::beginTransaction();

            $exam = Exam::create($validated);
            Log::info('Exam CREATED', ['id' => $exam->id, 'name' => $exam->name]);

            DB::commit();

            return redirect()->route('exams.show', $exam)
                ->with('success', 'Exam created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('EXCEPTION', ['msg' => $th->getMessage()]);

            return back()->with('error', 'Failed to create exam: ' . $th->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $categories = ExamCategory::with('mainCategory')
            ->get()
            ->groupBy(fn($cat) => $cat->mainCategory?->name ?? 'Main Categories');

        $classes = Classes::all();
        $gradingScales = GradingScale::active()->get();
        $terms = Term::cases();
        $statuses = ExamStatus::cases();
        return view('exams.edit', compact('exam', 'categories', 'classes', 'gradingScales', 'terms', 'statuses'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $exam->load(['class', 'category', 'gradingScale', 'results' => function ($q) {
            $q->with(['student', 'subject'])->orderBy('student_id');
        }]);

        $resultsByStudent = $exam->results->groupBy('student_id');

        $studentsWithResults = $exam->results->pluck('student_id')->unique();

        $pendingStudents = $exam->class->students()
            ->whereNotIn('students.id', $studentsWithResults)
            ->get();

        $statistics = [
            'total_students' => $exam->class->students()->count(),
            'submitted_count' => $studentsWithResults->count(),
            'pending_count' => $pendingStudents->count(),
            'average_mark' => $exam->results->avg('final_mark'),
            'highest_mark' => $exam->results->max('final_mark'),
            'lowest_mark' => $exam->results->min('final_mark'),
            'pass_rate' => $this->calculatePassRate($exam),
        ];

        return view('exams.show', compact('exam', 'resultsByStudent', 'pendingStudents', 'statistics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        Log::info('UPDATE ROUTE REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            // 'exam_category_id' => 'nullable|exists:exam_categories,id',
            // 'grading_scale_id' => 'nullable|exists:grading_scales,id',
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2000|max:2100',
            'exam_date' => 'nullable|date',
            'entry_start_date' => 'nullable|date',
            'entry_end_date' => 'nullable|date|after_or_equal:entry_start_date',
            'result_release_date' => 'nullable|date|after:exam_date',
            'max_mark' => 'required|numeric|min:1|max:500',
            'weight' => 'required|numeric|min:0|max:10',
            'requires_continuous_assessment' => 'boolean',
            'status' => 'required|in:draft,published,ongoing,completed,results_released',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('VALIDATION FAILED', $validator->errors()->toArray());
            return back()->withErrors($validator->errors())->withInput();
        }

        $validated = $validator->validated();
        $validated['requires_continuous_assessment'] = $request->boolean('requires_continuous_assessment');

        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(substr($validated['name'], 0, 3)) . '-' . $validated['year'];
        }

        
        Log::info('UPDATING EXAM WITH DATA', $validated);

        try {
            DB::beginTransaction();

            $updated = $exam->update($validated);

            Log::info('UPDATE RESULT', ['success' => $updated, 'exam_id' => $exam->id]);

            DB::commit();

            return redirect()->route('exams.show', $exam)
                ->with('success', 'Exam updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('UPDATE EXCEPTION', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to update exam: ' . $th->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        if ($exam->results()->exists()) {
            return back()->with('error', 'Cannot delete exam with existing results.');
        }

        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function updateStatus(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,published,ongoing,completed,results_released',
        ]);

        $exam->status = $validated['status'];

        if ($validated['status'] === 'results_released') {
            $exam->result_release_date = now();
        }

        $exam->save();

        return response()->json([
            'success' => true,
            'status' => $exam->status,
            'label' => ucfirst(str_replace('_', ' ', $exam->status->value)),
        ]);
    }

    public function getResultsData(Exam $exam)
    {
        $results = $exam->results()
            ->with(['student', 'subject'])
            ->get()
            ->groupBy('student_id');

        $data = [];
        foreach ($results as $studentId => $studentResults) {
            $student = $studentResults->first()->student;
            $row = [
                'student_id' => $studentId,
                'student_name' => $student->full_name,
                'admission_number' => $student->admission_number,
            ];

            foreach ($studentResults as $result) {
                $row['subject_' . $result->subject_id] = [
                    'mark' => $result->final_mark,
                    'grade' => $result->grade,
                ];
            }

            $data[] = $row;
        }

        return response()->json(['data' => $data]);
    }

    protected function calculatePassRate(Exam $exam): float
    {
        $total = $exam->results()->count();
        if ($total === 0) {
            return 0;
        }

        $passing = $exam->results()
            ->where('final_mark', '>=', 50)
            ->count();

        return round(($passing / $total) * 100, 2);
    }
}
