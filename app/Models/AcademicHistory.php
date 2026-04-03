<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicHistory extends Model
{
    protected $fillable = [
        'students_id',
        'academic_level',
        'other_academic_level',
        'school_name',
        'from_year',
        'to_year',
        'aggregate_score',
        'average_position',
        'grade',
        'ple_file',
        'o_level_file',
        'other_file',
        'repeat_class',
        'skip_class',
    ];

    protected $casts = [
        // 'from_year' => 'date',
        // 'to_year'   => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }
}
