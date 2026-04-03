<?php

namespace App\Models;

use App\Helpers\Term;
use Illuminate\Database\Eloquent\Model;

class ContinuousAssessment extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'assessment_type_id',
        'class_id',
        'term',
        'year',
        'title',
        'raw_score',
        'max_score',
        'teacher_comment',
        'recorded_by'
    ];

    protected $casts = [
        'term' => Term::class,
        'raw_score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function assessmentType()
    {
        return $this->belongsTo(AssessmentType::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getPercentageAttribute(): float
    {
        return round(($this->raw_score / $this->max_score) * 100, 2);
    }

    public function getIsPassingAttribute(): bool
    {
        return $this->percentage >= 50;
    }

    public static function calculateTermContribution($studentId, $subjectId, $term, $year): float
    {
        $assessments = self::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('term', $term)
            ->where('year', $year)
            ->whereHas('assessmentType', fn($q) => $q->where('category', 'continuous'))
            ->get();

        if ($assessments->isEmpty()) {
            return 0;
        }


        $average = $assessments->avg('weighted_score');
        return round(($average / 100) * 20, 2);
    }

    
    public static function getStudentTermAssessments($studentId, $term, $year)
    {
        return self::where('student_id', $studentId)
            ->where('term', $term)
            ->where('year', $year)
            ->with(['subject', 'assessmentType'])
            ->orderBy('subject_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('subject_id');
    }
}
