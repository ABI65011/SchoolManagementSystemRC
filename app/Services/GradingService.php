<?php

namespace App\Services;

use App\Helpers\ExamStatus;
use App\Models\AoiCriterion;
use App\Models\ContinuousAssessment;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\GradingScale;
use App\Models\Student;
use App\Models\Subject;
use App\Helpers\Term;
use App\Helpers\GradingScaleType;
use App\Models\CriterionDefinition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GradingService
{
    /**
     * Calculate final mark for an exam result
     */
    public function calculateFinalMark(?float $rawMark, float $caContribution, Exam $exam): ?float
    {
        if ($rawMark === null) {
            return null;
        }

        if ($exam->requires_continuous_assessment) {
            $examPortion = $rawMark * 0.8;
            return round($examPortion + $caContribution, 2);
        }

        return round($rawMark, 2);
    }

    /**
     * Determine grade based on mark and grading scale
     */
    public function determineGrade(float $mark, GradingScale $gradingScale): array
    {
        $gradeItem = $gradingScale->getGradeForMark($mark);

        if (!$gradeItem) {
            return [
                'grade' => 'UNG',
                'descriptor' => 'Ungraded',
                'points' => null,
            ];
        }

        return [
            'grade' => $gradeItem->grade_code,
            'descriptor' => $gradeItem->descriptor,
            'points' => $gradeItem->points,
        ];
    }

    /**
     * Calculate continuous assessment contribution for a student
     */
    public function calculateContinuousAssessmentContribution(
        int $studentId,
        int $subjectId,
        Term $term,
        int $year
    ): float {
        $contribution = ContinuousAssessment::calculateTermContribution(
            $studentId,
            $subjectId,
            $term,
            $year
        );

        return min(max($contribution, 0), 20);
    }

    /**
     * Calculate AoI total from criteria scores
     */
    public function calculateAoiTotal(array $criteriaScores): int
    {
        return array_sum($criteriaScores);
    }

    /**
     * Calculate AoI score from RACE criteria (1-3 scale)
     */
    public function calculateAoiScoreFromRACE(array $criteriaScores): int
    {
        $total = array_sum($criteriaScores);
        $max = count($criteriaScores) * 3;
        $percentage = ($total / $max) * 100;

        return match (true) {
            $percentage >= 80 => 3,
            $percentage >= 50 => 2,
            default => 1,
        };
    }

    /**
     * Get descriptor for AoI score
     */
    public function getAoiDescriptor(int $score): string
    {
        return match ($score) {
            3 => 'Achieved Excellence in Integration',
            2 => 'Achieved Moderate Competency',
            1 => 'Achieved Basic Competency',
            default => 'Not Assessed',
        };
    }

    /**
     * Process bulk exam results
     */
    public function processBulkExamResults(Exam $exam, array $resultsData): Collection
    {
        $processed = collect();

        DB::transaction(function () use ($exam, $resultsData, &$processed) {
            foreach ($resultsData as $data) {
                $result = $this->processSingleExamResult($exam, $data);
                $processed->push($result);
            }
        });

        return $processed;
    }

    /**
     * Process a single exam result
     */
    public function processSingleExamResult(Exam $exam, array $data): ExamResult
    {
        $caContribution = 0;
        if ($exam->requires_continuous_assessment) {
            $caContribution = $this->calculateContinuousAssessmentContribution(
                $data['student_id'],
                $data['subject_id'],
                $exam->term,
                $exam->year
            );
        }

        $finalMark = $this->calculateFinalMark($data['raw_mark'] ?? null, $caContribution, $exam);

        $result = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $data['student_id'],
            'subject_id' => $data['subject_id'],
            'raw_mark' => $data['raw_mark'] ?? null,
            'final_mark' => $finalMark,
            'teacher_remark' => $data['teacher_remark'] ?? null,
            'graded_by' => $data['graded_by'] ?? Auth::id(),
        ]);

        if (isset($data['aoi_criteria']) && is_array($data['aoi_criteria'])) {
            $this->processAoiCriteria($result, $data['aoi_criteria'], $data['graded_by'] ?? null);
        }

        $gradingScale = $exam->resolveGradingScale();
        if ($gradingScale && $finalMark !== null) {
            $gradeInfo = $this->determineGrade($finalMark, $gradingScale);
            $result->grade = $gradeInfo['grade'];
            $result->saveQuietly();
        }

        return $result->fresh(['aoiCriteria.criterionDefinition']);
    }

    /**
     * Process AoI criteria for an exam result
     */
    public function processAoiCriteria(ExamResult $examResult, array $criteriaData, ?int $gradedBy = null): void
    {
        foreach ($criteriaData as $criterionCode => $score) {
            if ($score === null || $score === '') continue;

            $definition = CriterionDefinition::findByCode($criterionCode);

            if ($definition && AoiCriterion::isValidScore((int)$score)) {
                $examResult->aoiCriteria()->updateOrCreate(
                    [
                        'criterion_definition_id' => $definition->id,
                    ],
                    [
                        'score' => (int)$score,
                        'graded_by' => $gradedBy ?? Auth::id(),
                        'comment' => $criteriaData['comment'] ?? null,
                    ]
                );
            }
        }
    }

    /**
     * Generate report card data (no database storage)
     */
    public function generateReportCardData(int $studentId, string $term, int $year, ?int $gradingScaleId = null): array
    {
        $student = Student::with('class')->findOrFail($studentId);


        $examResults = ExamResult::where('student_id', $studentId)
            ->whereHas('exam', fn($q) => $q->where('term', $term)->where('year', $year))
            ->with(['subject', 'exam.gradingScale'])
            ->get();


        $continuousAssessments = ContinuousAssessment::where('student_id', $studentId)
            ->where('term', $term)
            ->where('year', $year)
            ->with('subject')
            ->get();


        $groupedResults = $examResults->groupBy('subject_id');

        $subjects = [];
        foreach ($groupedResults as $subjectId => $results) {
            $subject = $results->first()->subject;
            $examAvg = $results->avg('final_mark');


            $caTotal = $continuousAssessments->where('subject_id', $subjectId)->sum('weighted_score');

            $firstResult = $results->first();
            $requiresCA = $firstResult && $firstResult->exam->requires_continuous_assessment;

            if ($requiresCA) {
                $finalMark = ($examAvg * 0.8) + $caTotal;
            } else {
                $finalMark = $examAvg;
            }

            $gradingScale = $firstResult ? $firstResult->exam->resolveGradingScale() : null;
            $gradeInfo = $gradingScale ? $this->determineGrade($finalMark, $gradingScale) : ['grade' => 'N/A', 'descriptor' => null];

            $subjects[] = [
                'subject' => $subject,
                'exam_mark' => round($examAvg, 2),
                'ca_mark' => round($caTotal, 2),
                'final_mark' => round($finalMark, 2),
                'grade' => $gradeInfo['grade'],
                'descriptor' => $gradeInfo['descriptor'],
                'teacher_remark' => $results->first()?->teacher_remark,
            ];
        }


        $gradingScale = $gradingScaleId ? GradingScale::find($gradingScaleId) : null;
        if (!$gradingScale && $examResults->isNotEmpty()) {
            $gradingScale = $examResults->first()->exam->resolveGradingScale();
        }

        return [
            'student' => $student,
            'class' => $student->class,
            'subjects' => $subjects,
            'grading_scale' => $gradingScale,
            'generated_at' => now(),
        ];
    }

    /**
     * Get subject data for report card
     */
    protected function getSubjectReportData(int $studentId, Subject $subject, string $term, int $year, GradingScale $gradingScale): array
    {
        $examResults = ExamResult::where('student_id', $studentId)
            ->where('subject_id', $subject->id)
            ->whereHas('exam', fn($q) => $q->where('term', $term)->where('year', $year))
            ->with('exam')
            ->get();

        $totalWeight = 0;
        $weightedMarks = 0;

        foreach ($examResults as $result) {
            $weight = $result->exam->weight ?? 1.0;
            $totalWeight += $weight;
            $weightedMarks += ($result->final_mark * $weight);
        }

        $averageMark = $totalWeight > 0 ? round($weightedMarks / $totalWeight, 2) : 0;

        $caTotal = ContinuousAssessment::where('student_id', $studentId)
            ->where('subject_id', $subject->id)
            ->where('term', $term)
            ->where('year', $year)
            ->sum('weighted_score');

        $gradeInfo = $this->determineGrade($averageMark, $gradingScale);

        return [
            'subject' => $subject,
            'exam_mark' => $averageMark,
            'ca_mark' => $caTotal,
            'final_mark' => $averageMark,
            'grade' => $gradeInfo['grade'],
            'descriptor' => $gradeInfo['descriptor'],
            'teacher_remark' => $examResults->first()?->teacher_remark,
        ];
    }

    /**
     * Resolve which grading scale to use
     */
    protected function resolveGradingScale(Collection $exams, ?int $preferredScaleId = null): GradingScale
    {
        if ($preferredScaleId) {
            return GradingScale::findOrFail($preferredScaleId);
        }

        if ($exams->contains(fn($exam) => $exam->isExternal())) {
            $unebScale = GradingScale::where('type', GradingScaleType::UNEB_Traditional->value)
                ->where('is_active', true)
                ->first();

            if ($unebScale) {
                return $unebScale;
            }
        }

        return GradingScale::where('is_default', true)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Generate report card PDF
     */
    public function generateReportCardPdf(int $studentId, string $term, int $year, ?int $gradingScaleId = null): \Barryvdh\DomPDF\PDF
    {
        $data = $this->generateReportCardData($studentId, $term, $year, $gradingScaleId);

        $schoolName = config('app.name', 'School Name');

        $pdfData = [
            'student' => $data['student'],
            'class' => $data['class'],
            'subjects' => $data['subjects'],
            'term' => $term,
            'year' => $year,
            'grading_scale' => $data['grading_scale'] ?? null,
            'school_name' => $schoolName,
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('report-cards.pdf', $pdfData);

        return $pdf;
    }

    /**
     * Get student performance summary
     */
    public function getStudentPerformanceSummary(int $studentId, string $term, int $year): array
    {
        $results = ExamResult::where('student_id', $studentId)
            ->whereHas('exam', fn($q) => $q->where('term', $term)->where('year', $year))
            ->with(['exam', 'subject', 'aoiCriteria.criterionDefinition'])
            ->get();

        $subjects = $results->groupBy('subject_id');

        $summary = [
            'total_subjects' => $subjects->count(),
            'subjects' => [],
            'aoi_summary' => [],
            'best_subject' => null,
            'weakest_subject' => null,
        ];

        $highestMark = 0;
        $lowestMark = 100;

        foreach ($subjects as $subjectId => $subjectResults) {
            $subject = $subjectResults->first()->subject;
            $avgMark = $subjectResults->avg('final_mark');

            $subjectData = [
                'subject' => $subject->name,
                'average_mark' => round($avgMark, 2),
                'grade' => $subjectResults->first()->grade,
                'exam_count' => $subjectResults->count(),
            ];

            $summary['subjects'][] = $subjectData;

            if ($avgMark > $highestMark) {
                $highestMark = $avgMark;
                $summary['best_subject'] = $subjectData;
            }

            if ($avgMark < $lowestMark) {
                $lowestMark = $avgMark;
                $summary['weakest_subject'] = $subjectData;
            }


            foreach ($subjectResults as $result) {
                foreach ($result->aoiCriteria as $criterion) {
                    $code = $criterion->criterion_code;
                    if (!isset($summary['aoi_summary'][$code])) {
                        $summary['aoi_summary'][$code] = [
                            'name' => $criterion->criterion_name,
                            'total_score' => 0,
                            'count' => 0,
                        ];
                    }
                    $summary['aoi_summary'][$code]['total_score'] += $criterion->score;
                    $summary['aoi_summary'][$code]['count']++;
                }
            }
        }

        foreach ($summary['aoi_summary'] as &$criterion) {
            $criterion['average'] = round($criterion['total_score'] / $criterion['count'], 2);
        }

        return $summary;
    }

    /**
     * Validate mark against grading scale
     */
    public function validateMarkAgainstScale(float $mark, GradingScale $gradingScale): bool
    {
        return $gradingScale->getGradeForMark($mark) !== null;
    }

    /**
     * Get grade distribution for an exam
     */
    public function getExamGradeDistribution(Exam $exam): array
    {
        $results = $exam->results()->get();

        $distribution = [];

        foreach ($results as $result) {
            $grade = $result->grade ?? 'UNG';
            if (!isset($distribution[$grade])) {
                $distribution[$grade] = 0;
            }
            $distribution[$grade]++;
        }

        if ($exam->gradingScale) {
            $ordered = [];
            foreach ($exam->gradingScale->items as $item) {
                if (isset($distribution[$item->grade_code])) {
                    $ordered[$item->grade_code] = $distribution[$item->grade_code];
                }
            }
            return $ordered;
        }

        return $distribution;
    }

    /**
     * Calculate class average for an exam
     */
    public function calculateExamClassAverage(Exam $exam): ?float
    {
        return $exam->results()->avg('final_mark');
    }

    /**
     * Get students needing remediation
     */
    public function getStudentsNeedingRemediation(Exam $exam, float $passingMark = 50): Collection
    {
        return $exam->results()
            ->with('student')
            ->where('final_mark', '<', $passingMark)
            ->get()
            ->map(fn($r) => $r->student);
    }

    /**
     * Calculate exam pass rate
     */
    public function calculateExamPassRate(Exam $exam): float
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

    /**
     * Generate result slip for a student
     */
    public function generateResultSlip(Exam $exam, Student $student, $results)
    {
        $data = [
            'exam' => $exam,
            'student' => $student,
            'results' => $results,
            'school_name' => env('APP_NAME', 'School Name'),
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('exam-results.slip', $data);

        return $pdf;
    }

    /**
     * Calculate UNEB 20% contribution
     */
    public function calculateUNEBTwentyPercent(int $studentId, int $year): array
    {
        $terms = [Term::Term1, Term::Term2, Term::Term3];
        $subjects = Subject::all();

        $result = [];

        foreach ($subjects as $subject) {
            $total = 0;
            $count = 0;

            foreach ($terms as $term) {
                $contribution = ContinuousAssessment::calculateTermContribution(
                    $studentId,
                    $subject->id,
                    $term->value,
                    $year
                );

                if ($contribution > 0) {
                    $total += $contribution;
                    $count++;
                }
            }

            $average = $count > 0 ? round($total / $count, 2) : 0;

            $result[$subject->code] = [
                'subject' => $subject->name,
                'term1' => ContinuousAssessment::calculateTermContribution($studentId, $subject->id, Term::Term1, $year),
                'term2' => ContinuousAssessment::calculateTermContribution($studentId, $subject->id, Term::Term2, $year),
                'term3' => ContinuousAssessment::calculateTermContribution($studentId, $subject->id, Term::Term3, $year),
                'average' => $average,
            ];
        }

        return $result;
    }

    /**
     * Get report card data for a student (used by controller)
     *
     * @param int $studentId
     * @param string $term
     * @param int $year
     * @return array
     */
    public function getReportCardData(int $studentId, string $term, int $year): array
    {
        $student = Student::with('class')->findOrFail($studentId);


        $examResults = ExamResult::where('student_id', $studentId)
            ->whereHas('exam', fn($q) => $q->where('term', $term)->where('year', $year))
            ->with(['subject', 'exam.gradingScale'])
            ->get();


        $continuousAssessments = ContinuousAssessment::where('student_id', $studentId)
            ->where('term', $term)
            ->where('year', $year)
            ->with('subject')
            ->get();

            
        $subjectIds = $examResults->pluck('subject_id')->unique();

        $subjects = [];
        foreach ($subjectIds as $subjectId) {
            $subject = Subject::find($subjectId);
            if (!$subject) continue;

            $subjectResults = $examResults->where('subject_id', $subjectId);
            $examAvg = $subjectResults->avg('final_mark');

            $caTotal = $continuousAssessments->where('subject_id', $subjectId)->sum('weighted_score');

            $firstResult = $subjectResults->first();
            $requiresCA = $firstResult && $firstResult->exam->requires_continuous_assessment;

            if ($requiresCA) {
                $finalMark = ($examAvg * 0.8) + $caTotal;
            } else {
                $finalMark = $examAvg;
            }

            $gradingScale = $firstResult ? $firstResult->exam->resolveGradingScale() : null;
            $gradeInfo = $gradingScale ? $this->determineGrade($finalMark, $gradingScale) : ['grade' => 'N/A', 'descriptor' => null];

            $subjects[] = [
                'subject' => $subject,
                'exam_mark' => round($examAvg, 2),
                'ca_mark' => round($caTotal, 2),
                'final_mark' => round($finalMark, 2),
                'grade' => $gradeInfo['grade'],
                'descriptor' => $gradeInfo['descriptor'],
                'teacher_remark' => $subjectResults->first()?->teacher_remark,
            ];
        }

        return [
            'student' => $student,
            'class' => $student->class,
            'subjects' => $subjects,
            'term' => $term,
            'year' => $year,
            'generated_at' => now(),
        ];
    }
}
