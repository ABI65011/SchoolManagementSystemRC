<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StudentAttendance extends Model
{
    protected $table = 'student_attendances';

    protected $fillable = [
        'student_id',
        'class_id',
        'attendance_date',
        'sponsorship',
        'status',
        'check_in_time',
        'late_minutes',
        'check_out_time',
        'early_departure_minutes',
        'absence_reason',
        'absence_notes',
        'is_verified',
        'verified_by',
        'verified_at',
        'physical_headcount',
        'recorded_by',
        'remarks',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'late_minutes' => 'integer',
        'early_departure_minutes' => 'integer',
        'physical_headcount' => 'integer',
    ];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }


    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
            'holiday' => 'Holiday',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'present' => 'success',
            'late' => 'warning',
            'excused' => 'info',
            'absent' => 'danger',
            'holiday' => 'secondary',
            default => 'secondary',
        };
    }

    public function getSponsorshipLabelAttribute()
    {
        return $this->sponsorship === 'government' ? 'UPE/USE (Government)' : 'Private';
    }


    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeGovernment($query)
    {
        return $query->where('sponsorship', 'government');
    }

    public function scopePrivate($query)
    {
        return $query->where('sponsorship', 'private');
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }


    public function verify($physicalCount)
    {
        $this->physical_headcount = $physicalCount;
        $this->is_verified = true;
        $this->verified_by = Auth::id();
        $this->verified_at = now();
        $this->save();

        
        if ($physicalCount != $this->where('class_id', $this->class_id)
            ->where('attendance_date', $this->attendance_date)
            ->where('status', 'present')
            ->count()
        ) {

            AttendanceDiscrepancy::create([
                'class_id' => $this->class_id,
                'date' => $this->attendance_date,
                'physical_count' => $physicalCount,
                'system_count' => $this->where('class_id', $this->class_id)
                    ->where('attendance_date', $this->attendance_date)
                    ->where('status', 'present')->count(),
                'difference' => abs($physicalCount - $this->where('class_id', $this->class_id)
                    ->where('attendance_date', $this->attendance_date)
                    ->where('status', 'present')->count()),
                'reported_by' => Auth::id(),
            ]);
        }
    }
}
