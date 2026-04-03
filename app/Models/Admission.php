<?php

namespace App\Models;

use App\Helpers\AdmissionStatus;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $fillable = [
        'student_id',
        'status',
        'comments',
        'admitted_by'
    ];

    protected $casts = [
        'status' => AdmissionStatus::class
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

