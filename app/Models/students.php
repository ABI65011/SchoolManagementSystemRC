<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class students extends Model
{
    protected $fillable = [
        'user_id',
        'admission_year',
        'joining_class',
        'first_name',
        'middle_name',
        'last_name',
        'dob',
        'gender',
        'citizenship',
        'id_type',
        'id_no',
        'id_image_path',
        'a_level_combination',
        'applying_section',
        'religious_affiliation',
        'other_religious_affiliation',
        'has_additional_info',
        'additional_info',
        'spoken_languages'
    ];

    protected $casts = [
        'dob'               => 'date',
        'spoken_languages'  => 'array',
    ];

    /* Accessor to show full name in tables */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => trim("{$this->first_name} {$this->middle_name} {$this->last_name}")
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function academicHistories()
    {
        return $this->hasMany(AcademicHistory::class, 'student_id');
    }

    public function disciplineHistory()
    {
        return $this->hasOne(DisciplineHistory::class, 'student_id');
    }

    public function medicalHistory()
    {
        return $this->hasOne(MedicalHistory::class, 'student_id');
    }

    public function careerAspiration()
    {
        return $this->hasOne(CareerAspiration::class, 'student_id');
    }
}
