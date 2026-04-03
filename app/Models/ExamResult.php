<?php

namespace App\Models;

use App\Helpers\ExamType;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'raw_mark',
        // 'continuous_assessment_contribution',
        'final_mark',
        'grade',
        'teacher_remark',
        'graded_by'
    ];

    protected $casts = [
        'raw_mark' => 'decimal:2',
        'final_mark' => 'decimal:2',
    ];
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function aoiCriteria()
    {
        return $this->hasMany(AoiCriterion::class);
    }

    public function getAoiCriteriaWithDefinitionAttribute()
    {
        return $this->aoiCriteria()->with('criterionDefinition')->get();
    }


    public function getAoiTotalAttribute(): ?int
    {
        return $this->aoiCriteria->sum('score');
    }

    public function getGradeDescriptorAttribute(): ?string
    {
        if (!$this->grade || !$this->exam || !$this->exam->gradingScale) {
            return null;
        }

        $item = $this->exam->gradingScale->items()
            ->where('grade_code', $this->grade)
            ->first();

        return $item?->descriptor;
    }

    public function isPassing(): bool
    {

        if ($this->exam->isExternal()) {
            return !in_array($this->grade, ['F9', 'F', 'E', 'U']);
        }

        return !in_array($this->grade, ['E', 'U', 'F9']);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($result) {
            if ($result->exam) {
                if ($result->exam->requires_continuous_assessment) {
                    $examContribution = $result->raw_mark ? ($result->raw_mark * 0.8) : 0;
                    $caContribution = $result->continuous_assessment_contribution ?? 0;
                    $result->final_mark = round($examContribution + $caContribution, 2);
                } else {

                    $result->final_mark = $result->raw_mark;
                }
            }
        });

        static::saved(function ($result) {
            if ($result->final_mark !== null && $result->exam && !$result->grade) {
                $scale = $result->exam->resolveGradingScale();
                if ($scale) {
                    $gradeItem = $scale->getGradeForMark($result->final_mark);
                    if ($gradeItem) {
                        $result->grade = $gradeItem->grade_code;
                        $result->saveQuietly();
                    }
                }
            }
        });
    }
}
