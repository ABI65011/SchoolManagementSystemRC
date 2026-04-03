<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AoiCriterion extends Model
{
    protected $table = 'aoi_criteria';

    protected $fillable = [
        'exam_result_id',
        'criterion_definition_id',
        'score',
        'graded_by',
        'comment'
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function examResult()
    {
        return $this->belongsTo(ExamResult::class);
    }

    public function criterionDefinition()
    {
        return $this->belongsTo(CriterionDefinition::class, 'criterion_definition_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getCriterionNameAttribute(): ?string
    {
        return $this->criterionDefinition?->name?->value ?? null;
    }

    public function getCriterionCodeAttribute(): ?string
    {
        return $this->criterionDefinition?->code?->value ?? null;
    }

    public function getMaxScoreAttribute(): int
    {
        return $this->criterionDefinition?->max_score ?? 3;
    }

    public function getPercentageAttribute(): float
    {
        return round(($this->score / $this->max_score) * 100, 2);
    }

    public function getDescriptorAttribute(): string
    {
        return match ($this->score) {
            3 => 'Exceeds Expectations',
            2 => 'Meets Expectations',
            1 => 'Below Expectations',
            default => 'Not Rated',
        };
    }

    public static function isValidScore($score): bool
    {
        return in_array($score, [1, 2, 3]);
    }

    public function scopeForCriterion($query, $criterionCode)
    {
        return $query->whereHas('criterionDefinition', fn($q) => $q->where('code', $criterionCode));
    }

    public function scopeWithScore($query, $score)
    {
        return $query->where('score', $score);
    }

    public function scopeMinScore($query, $minScore)
    {
        return $query->where('score', '>=', $minScore);
    }

    public static function getForExamResult($examResultId)
    {
        return self::where('exam_result_id', $examResultId)
            ->with('criterionDefinition')
            ->get()
            ->keyBy('criterion_code');
    }
}
