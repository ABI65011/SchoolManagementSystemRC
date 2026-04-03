<?php

namespace App\Models;

use App\Helpers\AssessmentTypeCategory;
use App\Helpers\AssessmentTypeName;
use Illuminate\Database\Eloquent\Model;

class AssessmentType extends Model
{
    protected $fillable = [
        'name',
        'category',
        'default_weight'
    ];

    protected $casts = [
        'name' => AssessmentTypeName::class,
        'category' => AssessmentTypeCategory::class,
        'default_weight' => 'decimal:2',
    ];
    public function continuousAssessments()
    {
        return $this->hasMany(ContinuousAssessment::class);
    }

    public function scopeContinuous($query)
    {
        return $query->where('category', AssessmentTypeCategory::Continuous);
    }


    public function scopeAoi($query)
    {
        return $query->where('category', AssessmentTypeCategory::AOI);
    }

    
    public function scopeExam($query)
    {
        return $query->where('category', AssessmentTypeCategory::Exam);
    }
}
