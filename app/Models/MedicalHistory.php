<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{

    protected $fillable = [
        'has_health_issues',
        'health_issues',
        'files',
    ];

    public function student()
    {
        return $this->belongsTo(students::class, 'student_id');
    }
}
