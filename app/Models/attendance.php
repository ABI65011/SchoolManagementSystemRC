<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class attendance extends Model
{
    protected $fillable = [
        'staff_id',
        'attendance_date',
        'check_in',
        'check_out',
        'check_in_method',
        'check_out_method',
        'check_in_location',
        'check_out_location',
        'status',
        'late_minutes',
        'early_departure_minutes',
        'working_hours',
        'is_holiday',
        'is_weekend',
        'overtime_hours',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'is_holiday' => 'boolean',
        'is_weekend' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(staff::class);
    }
}
