<?php

namespace App\Models;

use App\Helpers\ExamType;
use Illuminate\Database\Eloquent\Model;

class ExamCategory extends Model
{
    protected $fillable = [
        'name',
        'main_category_id',
        'exam_type',
        'description',
        'grading_scale_id',
        'requires_continuous_assessment',
        'weight',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'requires_continuous_assessment' => 'boolean',
        'is_active' => 'boolean',
        'weight' => 'decimal:2',
        'sort_order' => 'integer',
    ];





    public function mainCategory()
    {
        return $this->belongsTo(ExamCategory::class, 'main_category_id');
    }


    public function subCategories()
    {
        return $this->hasMany(ExamCategory::class, 'main_category_id')->orderBy('sort_order');
    }

    public function gradingScale()
    {
        return $this->belongsTo(GradingScale::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'exam_category_id');
    }

    public function scopeInternal($query)
    {
        return $query->where('exam_type', ExamType::Internal->value);
    }

    public function scopeExternal($query)
    {
        return $query->where('exam_type', ExamType::External->value);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get only Main Categories (categories that have no main_category_id)
     * These are the top-level categories like "Internal Exams", "External Exams"
     */
    public function scopeMainCategories($query)
    {
        return $query->whereNull('main_category_id');
    }

    /**
     * Get only Sub-Categories (categories that belong to a main category)
     * These are the specific types like "Mock", "End of Term", "Quiz"
     */
    public function scopeSubCategories($query)
    {
        return $query->whereNotNull('main_category_id');
    }

    /**
     * Get the full category path: "External Exams > Mock"
     */
    public function getFullPathAttribute(): string
    {
        if ($this->mainCategory) {
            return "{$this->mainCategory->name} > {$this->name}";
        }
        return $this->name;
    }


    public function getIsMainCategoryAttribute(): bool
    {
        return is_null($this->main_category_id);
    }

    public function getIsSubCategoryAttribute(): bool
    {
        return !is_null($this->main_category_id);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->exam_type === ExamType::Internal->value ? 'Internal' : 'External';
    }


    public function isExternal(): bool
    {
        if ($this->exam_type === ExamType::External->value) {
            return true;
        }

        if ($this->mainCategory) {
            return $this->mainCategory->isExternal();
        }

        return false;
    }

    /**
     * Get the top-level Main Category for this category
     */
    public function getRootMainCategory(): ?self
    {
        if ($this->isMainCategory) {
            return $this;
        }

        return $this->mainCategory?->getRootMainCategory();
    }

    /**
     * Get all Sub-Categories under this main category
     */
    public function allSubCategories()
    {
        $subs = collect();

        foreach ($this->subCategories as $sub) {
            $subs->push($sub);
            $subs = $subs->merge($sub->allSubCategories());
        }

        return $subs;
    }

    /**
     * Check if this category is a Mock exam
     */
    public function isMock(): bool
    {

        if (stripos($this->name, 'mock') !== false) {
            return true;
        }

        
        if ($this->mainCategory && stripos($this->mainCategory->name, 'mock') !== false) {
            return true;
        }

        return false;
    }
}
