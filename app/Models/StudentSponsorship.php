<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSponsorship extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'start_date',
        'end_date',
        'reference_number',
        'approved_by',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    
    public function getTypeLabelAttribute()
    {
        return $this->type === 'government' ? 'UPE/USE (Government)' : 'Private';
    }

    public function getTypeColorAttribute()
    {
        return $this->type === 'government' ? 'primary' : 'warning';
    }
}
