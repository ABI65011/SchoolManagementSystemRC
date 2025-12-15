<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{

    protected $fillable = [
        'students_id',
        'has_health_issues',
        'health_issues',
        'files',
    ];

    public function student()
    {
        return $this->belongsTo(students::class, 'students_id');
    }
}
