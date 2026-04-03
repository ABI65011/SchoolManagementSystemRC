<?php

namespace App\Models;

use App\Helpers\GradingScaleType;
use Illuminate\Database\Eloquent\Model;

class GradingScaleItem extends Model
{
    protected $fillable = [
        'grading_scale_id',
        'grade_code',
        'min_mark',
        'max_mark',
        'achievement_level',
        'descriptor',
        'points',
        'order'
    ];

    protected $casts = [
        'min_mark' => 'integer',
        'max_mark' => 'integer',
        'points' => 'integer',
        'order' => 'integer',
    ];

    public function gradingScale()
    {
        return $this->belongsTo(GradingScale::class);
    }

    public function getLabelAttribute()
    {
        $scaleType = $this->gradingScale?->type;

        return match($scaleType) {
            GradingScaleType::UNEB_Traditional => $this->getUceLabel(),
            GradingScaleType::UACE => $this->getUaceLabel(),
            GradingScaleType::Competency_Based => $this->getCompetencyBasedLabel(),
            GradingScaleType::Custom => $this->getCustomLabel(),
            default => $this->grade_code,

        };
    }
    protected function getUceLabel(): string
    {
        return match ($this->grade_code) {
            'D1' => 'Distinction 1',
            'D2' => 'Distinction 2',
            'C3' => 'Credit 3',
            'C4' => 'Credit 4',
            'C5' => 'Credit 5',
            'C6' => 'Credit 6',
            'P7' => 'Pass 7',
            'P8' => 'Pass 8',
            'F9' => 'Fail 9',
            default => $this->grade_code,
        };
    }

    protected function getUaceLabel(): string
    {
        $points = match ($this->grade_code) {
            'A' => 6,
            'B' => 5,
            'C' => 4,
            'D' => 3,
            'E' => 2,
            'O' => 1,
            'F' => 0,
            default => null,
        };

        return $points ? "{$this->grade_code} ({$points} points)" : $this->grade_code;
    }

    protected function getCompetencyBasedLabel(): string
    {
        return match ($this->grade_code) {
            'A' => 'Achieved Excellence',
            'B' => 'Achieved Above Standard',
            'C' => 'Achieved Standard',
            'D' => 'Achieved Basic Competency',
            'E' => 'Below Standard',
            'U' => 'Ungraded',
            default => $this->grade_code,
        };
    }

    public function isValidForScale(): bool
    {
        $scaleType = $this->gradingScale?->type;

        $validCodes = match ($scaleType) {
            'uneb_traditional' => ['D1', 'D2', 'C3', 'C4', 'C5', 'C6', 'P7', 'P8', 'F9'],
            'uace' => ['A', 'B', 'C', 'D', 'E', 'O', 'F'],
            'competency_based' => ['A', 'B', 'C', 'D', 'E', 'U'],
            default => [],
        };

        return in_array($this->grade_code, $validCodes);
    }


    public function getPointsAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }


        if ($this->gradingScale?->type === 'uace') {
            return match ($this->grade_code) {
                'A' => 6,
                'B' => 5,
                'C' => 4,
                'D' => 3,
                'E' => 2,
                'O' => 1,
                'F' => 0,
                default => null,
            };
        }

        return null;
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!$item->descriptor && $item->gradingScale) {
                $item->descriptor = $item->label;
            }
        });
    }
}
