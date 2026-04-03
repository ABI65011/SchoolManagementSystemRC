<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Helpers\GradingScaleName;
use App\Helpers\GradingScaleType;
use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_default',
        'is_active'
    ];

    protected $casts = [
        'name' => GradingScaleName::class,
        'type' => GradingScaleType::class,
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(GradingScaleItem::class)->orderBy('order');
    }



    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    public function getGradeForMark(float $mark): ?GradingScaleItem
    {
        return $this->items()
            ->where('min_mark', '<=', $mark)
            ->where('max_mark', '>=', $mark)
            ->first();
    }
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvailableGradeCodes(): array
    {
        if (!$this->type || !$this->type->value) {
            return [];
        }
        return match ($this->type->value) {
            'uneb_traditional' => ['D1', 'D2', 'C3', 'C4', 'C5', 'C6', 'P7', 'P8', 'F9'],
            'uace' => ['A', 'B', 'C', 'D', 'E', 'O', 'F'],
            'competency_based' => ['A', 'B', 'C', 'D', 'E', 'U'],
            default => [],
        };
    }

    public function getGradeLabel(string $gradeCode): string
    {
        $item = $this->items()->where('grade_code', $gradeCode)->first();
        return $item?->label ?? $gradeCode;
    }
}
