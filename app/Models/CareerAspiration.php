<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerAspiration extends Model
{
    protected $fillable = [
        'students_id',
        'aspiration',
        'other_aspiration',
        'best_done_subjects',
        'other_best_done_subjects',
        'worst_done_subjects',
        'other_worst_done_subjects',
        'favorite_subjects',
        'other_favorite_subjects',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }
}
