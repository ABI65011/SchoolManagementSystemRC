<?php

namespace App\Models;

use App\Helpers\ExamStatus;
use App\Helpers\ExamType;
use App\Helpers\Term;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'name',
        'code',
        'exam_category_id',
        // 'exam_type_override',
        // 'is_external_override',
        // 'grading_scale_id',
        'requires_continuous_assessment',
        'class_id',
        'term',
        'year',
        'exam_date',
        'entry_start_date',
        'entry_end_date',
        'result_release_date',
        'max_mark',
        'weight',
        'status',
        'is_active',
        'description',
        'instructions',
        // 'metadata'
    ];

    protected $casts = [
        'term' => Term::class,
        'year' => 'integer',
        'exam_date' => 'date',
        'entry_start_date' => 'date',
        'entry_end_date' => 'date',
        'result_release_date' => 'date',
        'max_mark' => 'decimal:2',
        'weight' => 'decimal:2',
        'requires_continuous_assessment' => 'boolean',
        'status' => ExamStatus::class,
        'is_active' => 'boolean',
        // 'metadata' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ExamCategory::class);
    }

    public function gradingScale()
    {
        return $this->belongsTo(GradingScale::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    // Scopes


    public function scopeForTerm($query, $term, $year)
    {
        return $query->where('term', $term)->where('year', $year);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeInternal($query)
    {
        return $query->where(function ($q) {
            $q->where('exam_type_override', 'internal')
                ->orWhereHas('category', fn($cq) => $cq->internal());
        });
    }

    public function scopeExternal($query)
    {
        return $query->where(function ($q) {
            $q->where('exam_type_override', 'external')
                ->orWhereHas('category', fn($cq) => $cq->external());
        });
    }

    public function scopeMock($query)
    {
        return $query->whereHas('category', fn($q) => $q->mock());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>', now())
            ->whereIn('status', [ExamStatus::Published->value, ExamStatus::Draft->value])
            ->orderBy('exam_date');
    }

    // End Scopes

    // Exam Type Methods
    public function getExamTypeAttribute(): string
    {
        if ($this->exam_type_override) {
            return $this->exam_type_override;
        }

        if ($this->category) {
            return $this->category->exam_type;
        }

        return 'Internal';
    }

    public function isExternal(): bool
    {
        if ($this->is_external_override !== null) {
            return $this->is_external_override;
        }

        if ($this->category) {
            return $this->category->isExternal();
        }

        return false;
    }

    public function isInternal(): bool
    {
        return !$this->isExternal();
    }

    public function isMock(): bool
    {
        if (!$this->category) {
            return false;
        }

        return $this->category->isMock();
    }
    // End Exam Type Methods


    // Status Management (Exam-level)

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'published' => 'Published',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'results_released' => 'Results Released',
            default => ucfirst($this->status->value)
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'published' => 'blue',
            'ongoing' => 'green',
            'completed' => 'yellow',
            'results_released' => 'purple',
            default => 'gray'
        };
    }

    public function isDraft(): bool
    {
        return $this->status === ExamStatus::Draft->value;
    }

    public function isPublished(): bool
    {
        return $this->status === ExamStatus::Published->value;
    }

    public function isOngoing(): bool
    {
        return $this->status === ExamStatus::Ongoing->value;
    }

    public function isCompleted(): bool
    {
        return $this->status === ExamStatus::Completed->value;
    }

    public function areResultsReleased(): bool
    {
        return $this->status === 'results_released' ||
               ($this->result_release_date && $this->result_release_date->isPast());
    }

    public function publish(): void
    {
        $this->status = ExamStatus::Published;
        $this->save();
    }

    public function start(): void
    {
        $this->status = ExamStatus::Ongoing;
        $this->save();
    }

    public function complete(): void
    {
        $this->status = ExamStatus::Completed;
        $this->save();
    }

    public function releaseResults(): void
    {
        $this->status = ExamStatus::ResultsReleased;
        $this->result_release_date = now();
        $this->save();
    }

    // Continuous Assessment Logic

    public function getRequiresContinuousAssessmentAttribute($value): bool
    {
        if (isset($this->attributes['requires_continuous_assessment'])) {
            return (bool) $this->attributes['requires_continuous_assessment'];
        }
        if ($this->category) {
            return $this->category->requires_continuous_assessment;
        }

        return $this->isInternal();
    }
    //End


    // Grading Scale Resolution

    public function resolveGradingScale()
    {
        if ($this->grading_scale_id) {
            return $this->gradingScale;
        }

        if ($this->category && $this->category->grading_scale_id) {
            return $this->category->gradingScale;
        }

        if ($this->isMock()) {
            return GradingScale::where('type', 'uneb_traditional')
                ->where('is_active', true)
                ->first();
        }

        if ($this->isExternal()) {
            return GradingScale::where('type', 'uneb_traditional')
                ->where('is_active', true)
                ->first();
        }

        return GradingScale::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    // End


    //Display Methods
    public function getCategoryPathAttribute(): string
    {
        return $this->category?->full_path ?? 'Uncategorized';
    }

    public function getTermDisplayAttribute(): string
    {
        return "Term {$this->term->value}, {$this->year}";
    }

    // End Display Methods

    // Business Logic
    public function isEntryOpen(): bool
    {
        $now = now();

        if (!$this->entry_start_date || !$this->entry_end_date) {
            return false;
        }

        return $now->between($this->entry_start_date, $this->entry_end_date);
    }

    public function isReadyForGrading(): bool
    {
        return $this->status === 'completed' && !$this->areResultsReleased();
    }

    public function registeredStudents()
    {
        return $this->class->students();
    }

    public function pendingStudents()
    {
        $studentIdsWithResults = $this->results()->pluck('student_id');

        return $this->registeredStudents()
            ->whereNotIn('students.id', $studentIdsWithResults);
    }
// End Business Logic

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exam) {
            if (empty($exam->code)) {
                $exam->code = strtoupper(substr($exam->name, 0, 3)) . '-' . $exam->year;
            }
        });
    }
}
