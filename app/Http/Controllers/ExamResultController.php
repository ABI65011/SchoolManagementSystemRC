<?php

namespace App\Http\Controllers;

use App\Helpers\AoICriteriaCode;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Subject;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class ExamResultController extends Controller
{
    use AuthorizesRequests;
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
        $query = ExamResult::with(['student', 'exam', 'subject', 'gradedBy']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('term')) {
            $query->whereHas('exam', fn($q) => $q->where('term', $request->term));
        }

        if ($request->filled('year')) {
            $query->whereHas('exam', fn($q) => $q->where('year', $request->year));
        }

        $results = $query->orderBy('created_at', 'desc')->get();


        if (!$request->filled('exam_id')) {
            return view('exam-results.index', [
                'groupedResults' => collect(),
                'exams' => Exam::orderBy('name')->get(),
                'noExamSelected' => true
            ]);
        }

        $groupedResultsArray = [];

        foreach ($results as $result) {
            $studentId = $result->student_id;

            if (!isset($groupedResultsArray[$studentId])) {
                $groupedResultsArray[$studentId] = [
                    'student' => $result->student,
                    'subjects' => [],
                    'total_score' => 0,
                    'average' => ['mark' => 0, 'grade' => 'N/A', 'grade_class' => 'average'],
                ];
            }

            $subjectData = [
                'result_id' => $result->id,
                'subject' => $result->subject,
                'raw_mark' => $result->raw_mark,
                'final_mark' => $result->final_mark,
                'grade' => $result->grade,
                'descriptor' => $this->getGradeDescriptor($result->grade, $result->exam),
                'teacher_remark' => $result->teacher_remark,
                'grade_class' => $this->getGradeClass($result->grade),
            ];

            $groupedResultsArray[$studentId]['subjects'][] = $subjectData;
            $groupedResultsArray[$studentId]['total_score'] += $result->final_mark;
        }

        foreach ($groupedResultsArray as $studentId => &$studentData) {
            $subjectCount = count($studentData['subjects']);
            if ($subjectCount > 0) {
                $averageMark = $studentData['total_score'] / $subjectCount;
                $studentData['average'] = [
                    'mark' => $averageMark,
                    'grade' => $this->getOverallGrade($averageMark),
                    'grade_class' => $this->getGradeClassForMark($averageMark),
                ];
            }
        }


        $groupedResults = collect($groupedResultsArray);

        $exams = Exam::orderBy('name')->get();

        return view('exam-results.index', compact('groupedResults', 'exams'));
    }


    private function getGradeDescriptor($grade, $exam)
    {
        if (!$exam || !$exam->gradingScale) {
            return null;
        }

        $item = $exam->gradingScale->items()
            ->where('grade_code', $grade)
            ->first();

        return $item?->descriptor;
    }

    private function getGradeClass($grade)
    {
        if (in_array($grade, ['A', 'D1', 'D2'])) return 'excellent';
        if (in_array($grade, ['B', 'C3', 'C4'])) return 'good';
        if (in_array($grade, ['C', 'C5', 'C6'])) return 'average';
        if (in_array($grade, ['D', 'P7', 'P8'])) return 'pass';
        return 'fail';
    }

    private function getGradeClassForMark($mark)
    {
        if ($mark >= 80) return 'excellent';
        if ($mark >= 70) return 'good';
        if ($mark >= 60) return 'average';
        if ($mark >= 50) return 'pass';
        return 'fail';
    }

    private function getOverallGrade($mark)
    {
        if ($mark >= 80) return 'A';
        if ($mark >= 70) return 'B';
        if ($mark >= 60) return 'C';
        if ($mark >= 50) return 'D';
        return 'E';
    }

    public function studentExamResults(Exam $exam, Student $student)
    {
        $results = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->with(['subject', 'gradedBy', 'aoiCriteria.criterionDefinition'])
            ->get();

        if ($results->isEmpty()) {
            return redirect()->route('exam-results.index')
                ->with('error', 'No results found for this student in this exam.');
        }


        $totalMarks = $results->sum('final_mark');
        $subjectCount = $results->count();
        $averageMark = $subjectCount > 0 ? $totalMarks / $subjectCount : 0;


        $gradingScale = $exam->resolveGradingScale();
        $gradeInfo = $gradingScale ? $this->getGradeInfo($averageMark, $gradingScale) : ['grade' => 'N/A', 'descriptor' => null];



        $aoiSummary = [];
        foreach ($results as $result) {
            foreach ($result->aoiCriteria as $criterion) {
                $code = $criterion->criterion_code;
                if (!isset($aoiSummary[$code])) {
                    $aoiSummary[$code] = [
                        'name' => $criterion->criterion_name,
                        'scores' => [],
                        'total' => 0,
                        'count' => 0,
                    ];
                }
                $aoiSummary[$code]['scores'][] = $criterion->score;
                $aoiSummary[$code]['total'] += $criterion->score;
                $aoiSummary[$code]['count']++;
            }
        }

        foreach ($aoiSummary as &$summary) {
            $summary['average'] = $summary['count'] > 0 ? round($summary['total'] / $summary['count'], 2) : 0;
        }

        return view('exam-results.show', compact('exam', 'student', 'results', 'totalMarks', 'subjectCount', 'averageMark', 'gradeInfo', 'aoiSummary', 'gradingScale'));
    }

    /**
     * Helper to get grade info
     */
    private function getGradeInfo($mark, $gradingScale)
    {
        $gradeItem = $gradingScale->getGradeForMark($mark);
        if (!$gradeItem) {
            return ['grade' => 'UNG', 'descriptor' => 'Ungraded'];
        }
        return [
            'grade' => $gradeItem->grade_code,
            'descriptor' => $gradeItem->descriptor,
        ];
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Exam $exam)
    {
        if ($exam->areResultsReleased()) {
            return back()->with('error', 'Cannot modify results after release.');
        }

        $exam->load(['class.students' => function ($q) {
            $q->orderBy('first_name');
        }]);

        $subjects = Subject::orderBy('name')->get();

        $existingResults = $exam->results()
            ->with(['student', 'subject', 'aoiCriteria.criterionDefinition'])
            ->get()
            ->groupBy('student_id');

        $aoiCriteria = [
            AoICriteriaCode::Relevance,
            AoICriteriaCode::Accuracy,
            AoICriteriaCode::Coherence,
            AoICriteriaCode::Excellence,
        ];

        $aoiDescriptions = [
            'relevance' => 'How relevant is the response to the task?',
            'accuracy' => 'How accurate and correct is the content?',
            'coherence' => 'How well-organized and logical is the presentation?',
            'excellence' => 'Does it demonstrate exceptional quality?',
        ];

        return view('exam-results.create', compact('exam', 'subjects', 'existingResults', 'aoiCriteria', 'aoiDescriptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Exam $exam)
    {
        Log::info('STORE RESULTS - FULL REQUEST', $request->all());

        $validator = Validator::make($request->all(), [
            'results' => 'required|array',
            'save_student' => 'nullable|exists:students,id',
            'save_all' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            Log::error('VALIDATION FAILED', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        $resultsData = $request->input('results', []);

        if (empty($resultsData)) {
            return back()->with('error', 'No results data submitted.');
        }

        try {
            DB::transaction(function () use ($exam, $resultsData) {
                foreach ($resultsData as $studentId => $studentData) {

                    $aoi = $studentData['aoi'] ?? [];


                    foreach ($studentData as $subjectId => $subjectData) {

                        if ($subjectId === 'aoi') continue;


                        $rawMark = $subjectData['raw_mark'] ?? null;
                        if ($rawMark === null || $rawMark === '') continue;

                        $this->gradingService->processSingleExamResult($exam, [
                            'student_id' => $subjectData['student_id'] ?? $studentId,
                            'subject_id' => $subjectData['subject_id'] ?? $subjectId,
                            'raw_mark' => $rawMark,
                            'aoi_criteria' => $aoi,
                        ]);
                    }
                }
            });

            $message = $request->has('save_student')
                ? 'Student results saved successfully.'
                : 'All results saved successfully.';

            if ($request->has('save_student')) {
                return redirect()->route('exam-results.create', $exam)
                    ->with('success', $message);
            }

            return redirect()->route('exams.show', $exam)->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error saving results: ' . $e->getMessage());
            return back()->with('error', 'Error saving results: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamResult $examResult)
    {
        if ($examResult->exam->areResultsReleased()) {
            return back()->with('error', 'Cannot modify results after release.');
        }

        $examResult->load(['exam', 'student', 'subject', 'aoiCriteria.criterionDefinition']);

        $aoiCriteria = [
            AoICriteriaCode::Relevance,
            AoICriteriaCode::Accuracy,
            AoICriteriaCode::Coherence,
            AoICriteriaCode::Excellence,
        ];

        return view('exam-results.edit', compact('examResult', 'aoiCriteria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamResult $examResult)
    {
        if ($examResult->exam->areResultsReleased()) {
            return back()->with('error', 'Cannot modify results after release.');
        }

        $validator = Validator::make($request->all(), [
            'raw_mark' => 'nullable|numeric|min:0|max:100',
            'teacher_remark' => 'nullable|string|max:500',
            'aoi_criteria' => 'nullable|array',
            'aoi_criteria.*' => 'nullable|integer|min:1|max:3',
        ]);

        $validated = $validator->validate();

        try {
            DB::transaction(function () use ($examResult, $validated) {
                $examResult->update([
                    'raw_mark' => $validated['raw_mark'] ?? null,
                    'teacher_remark' => $validated['teacher_remark'] ?? null,
                ]);

                if (isset($validated['aoi_criteria'])) {
                    foreach ($validated['aoi_criteria'] as $criterionCode => $score) {
                        $definition = \App\Models\CriterionDefinition::findByCode($criterionCode);

                        if ($definition && $score) {
                            $examResult->aoiCriteria()->updateOrCreate(
                                ['criterion_definition_id' => $definition->id],
                                ['score' => $score, 'graded_by' => Auth::id()]
                            );
                        }
                    }
                }
            });

            return redirect()->route('exams.show', $examResult->exam)
                ->with('success', 'Result updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating result: ' . $e->getMessage());
        }
    }

    public function uploadForm(Exam $exam)
    {
        return view('exam-results.upload', compact('exam'));
    }

    public function upload(Request $request, Exam $exam)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
        ]);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Results uploaded successfully.');
    }

    public function releaseResults(Exam $exam)
    {
        if ($exam->status !== 'completed') {
            return back()->with('error', 'Exam must be completed before releasing results.');
        }

        $exam->releaseResults();

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Results released successfully.');
    }

    /**
     * Print result slip for a student.
     */
    public function printResult(Exam $exam, Student $student)
    {
        $result = $exam->results()
            ->where('student_id', $student->id)
            ->with(['subject', 'aoiCriteria.criterionDefinition'])
            ->get();

        if ($result->isEmpty()) {
            return back()->with('error', 'No results found for this student.');
        }

        $pdf = $this->gradingService->generateResultSlip($exam, $student, $result);

        $filename = "result_{$student->admission_number}_{$exam->code}.pdf";
        return $pdf->download($filename);
    }
}
