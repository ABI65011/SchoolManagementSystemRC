<?php

namespace App\Models;

use App\Observers\StudentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

#[ObservedBy([StudentObserver::class])]
class Student extends Model
{
    protected $fillable = [
        'user_id',
        'identification_image',
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
        // 'dob'               => 'date',
        'spoken_languages'  => 'array',
    ];

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
        return $this->hasMany(AcademicHistory::class);
    }

    public function disciplineHistory()
    {
        return $this->hasOne(DisciplineHistory::class);
    }

    public function medicalHistory()
    {
        return $this->hasOne(MedicalHistory::class );
    }

    public function careerAspiration()
    {
        return $this->hasOne(CareerAspiration::class );
    }


    public function class()
    {
        return $this->belongsTo(Classes::class);
    }



    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function continuousAssessments()
    {
        return $this->hasMany(ContinuousAssessment::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function sponsorships()
    {
        return $this->hasMany(StudentSponsorship::class)->orderBy('created_at', 'desc');
    }

    public function currentSponsorship()
    {
        return $this->hasOne(StudentSponsorship::class)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc');
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_student', 'student_id', 'class_id')
            ->withPivot('stream', 'is_current')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }


    public function currentClass()
    {
        return $this->belongsToMany(Classes::class, 'class_student', 'student_id', 'class_id')
            ->wherePivot('is_current', true)
            ->withPivot('stream');
    }


    public function getCurrentClassAttribute()
    {
        return $this->classes()
            ->wherePivot('is_current', true)
            ->first();
    }

    public function getCurrentClassNameAttribute()
    {
        $current = $this->getCurrentClassAttribute();
        return $current ? $current->name : null;
    }

    public function getCurrentStreamAttribute()
    {
        $current = $this->getCurrentClassAttribute();
        return $current ? $current->pivot->stream : null;
    }

    public function getSponsorshipTypeAttribute()
    {
        $activeSponsorship = $this->sponsorships()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();

        return $activeSponsorship ? $activeSponsorship->type : 'private';
    }

    public function getSponsorshipReferenceAttribute()
    {
        $activeSponsorship = $this->sponsorships()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();

        return $activeSponsorship ? $activeSponsorship->reference_number : null;
    }

    public function getSponsorshipLabelAttribute()
    {
        return $this->sponsorship_type === 'government' ? 'UPE/USE (Government)' : 'Private';
    }

    public function getSponsorshipColorAttribute()
    {
        return $this->sponsorship_type === 'government' ? 'primary' : 'warning';
    }

    public function assignSponsorship($type, $reference = null, $notes = null)
    {

        StudentSponsorship::where('student_id', $this->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'end_date' => now()]);


        return StudentSponsorship::create([
            'student_id' => $this->id,
            'type' => $type,
            'start_date' => now(),
            'reference_number' => $reference,
            'approved_by' => Auth::id(),
            'notes' => $notes,
            'is_active' => true,
        ]);
    }


}
