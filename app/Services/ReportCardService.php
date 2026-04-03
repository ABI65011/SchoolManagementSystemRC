<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ReportCard;
use App\Models\ExamResult;
use App\Models\ContinuousAssessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportCardService {
    public function generate(Student $student, string $term, int $year)
    {
        $examResults = ExamResult::where('student_id', $student->id)
            ->whereHas('exam', function ($q) use ($term, $year) {
                $q->where('term', $term)->where('year', $year);
            })
            ->with(['exam', 'subject', 'exam.gradingScale'])
            ->get();

        $continuousAssessments = ContinuousAssessment::where('student_id', $student->id)
            ->where('term', $term)
            ->where('year', $year)
            ->with(['subject', 'assessmentType'])
            ->get();

        $subjects = [];
        foreach ($examResults->groupBy('subject_id') as $subjectId => $results) {
            $subject = $results->first()->subject;
            $examMarks = $results->avg('final_mark');
            $caTotal = $continuousAssessments
                ->where('subject_id', $subjectId)
                ->sum('weighted_score');

            $subjects[] = [
                'name' => $subject->name,
                'code' => $subject->code,
                'exam_mark' => round($examMarks, 2),
                'ca_mark' => round($caTotal, 2),
                'final_mark' => round(($examMarks * 0.8) + $caTotal, 2),
                'grade' => $results->first()->grade,
            ];
        }

        $pdf = Pdf::loadView('report-cards.template', [
            'student' => $student,
            'subjects' => $subjects,
            'term' => $term,
            'year' => $year,
            'generated_at' => now(),
        ]);

        $reportCard = ReportCard::create([
            'student_id' => $student->id,
            'generated_by' => Auth::id(),
            'generated_at' => now(),
        ]);

        $filename = "report_card_{$student->admission_number}_T{$term}_{$year}.pdf";
        $path = "report-cards/{$filename}";
        Storage::put($path, $pdf->output());

        $reportCard->update(['pdf_path' => $path]);

        return $pdf;
    }

    public function getReportCardData(Student $student, string $term, int $year)
    {
        //
    }
}
