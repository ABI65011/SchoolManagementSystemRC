<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineHistory extends Model
{

    protected $fillable = [
        'has_disciplinary_issues',
        'disciplinary_issues',
        'reason',
    ];

    public function student()
    {
        return $this->belongsTo(students::class, 'student_id');
    }
}
