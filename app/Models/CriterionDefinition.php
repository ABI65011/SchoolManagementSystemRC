<?php

namespace App\Models;

use App\Helpers\AoICriteria;
use App\Helpers\AoICriteriaCode;
use Illuminate\Database\Eloquent\Model;

class CriterionDefinition extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'max_score',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'name' => AoICriteria::class,
        'code' => AoICriteriaCode::class,
        'max_score' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function aoiCriteria()
    {
        return $this->hasMany(AoiCriterion::class, 'criterion_definition_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
    public static function getRaceCriteria()
    {
        return self::whereIn('code', [
            AoICriteriaCode::Relevance,
            AoICriteriaCode::Accuracy,
            AoICriteriaCode::Coherence,
            AoICriteriaCode::Excellence
        ])->active()->get();
    }

    public static function findByCode(string $code)
    {
        return self::where('code', $code)->first();
    }

    public function getNameValueAttribute(): string
    {
        return $this->name->value ?? '';
    }

    public function getCodeValueAttribute(): string
    {
        return $this->code->value ?? '';
    }
}
