<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDiscrepancy extends Model
{
    protected $fillable = [
        'class_id',
        'date',
        'physical_count',
        'system_count',
        'difference',
        'notes',
        'reported_by',
        'resolved',
    ];

    protected $casts = [
        'date' => 'date',
        'resolved' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
