<?php

namespace App\Models;

use App\Helpers\ReportCardStatus;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    protected $fillable = [
        'student_id',
        'term',
        'year',
        'generated_by',
        'generated_at',
        'pdf_path'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
