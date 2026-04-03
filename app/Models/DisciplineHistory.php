<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineHistory extends Model
{

    protected $fillable = [
        'students_id',
        'has_disciplinary_issues',
        'disciplinary_issues',
        'reason',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }
}
