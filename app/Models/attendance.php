<?php

namespace App\Models;

use App\Helpers\AttendanceStatus;
use App\Helpers\CheckInMethod;
use Carbon\Carbon;
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
        'check_in_method' => CheckInMethod::class,
        'check_out_method' => CheckInMethod::class,
        'status' => AttendanceStatus::class,
        'is_holiday' => 'boolean',
        'is_weekend' => 'boolean',
        'working_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(staff::class);
    }

    public function calculateMetrics(AttendanceLocation $location): void
    {

        if ($this->check_in) {
            $checkInTime = Carbon::parse($this->check_in->format('H:i:s'));
            $lateThreshold = Carbon::parse($location->late_threshold);

            if ($checkInTime->gt($lateThreshold)) {
                $this->late_minutes = $lateThreshold->diffInMinutes($checkInTime);

                if ($this->status !== AttendanceStatus::Holiday->value) {
                    $this->status = AttendanceStatus::Late->value;
                }
            } else {
                $this->late_minutes = 0;
            }
        }


        if ($this->check_out) {
            $this->working_hours = $this->check_in->diffInMinutes($this->check_out) / 60;

            $checkOutTime = Carbon::parse($this->check_out->format('H:i:s'));
            $workEnd = Carbon::parse($location->working_day_end);

            if ($checkOutTime->lt($workEnd)) {
                $this->early_departure_minutes = $checkOutTime->diffInMinutes($workEnd);
            } else {
                $this->early_departure_minutes = 0;
            }

            
            $expectedEnd = $this->check_in->copy()->addHours((float) $location->full_day_hours);

            if ($this->check_out->gt($expectedEnd)) {
                $this->overtime_hours = $this->check_out->diffInMinutes($expectedEnd) / 60;
            } else {
                $this->overtime_hours = 0;
            }
        } else {
            $this->working_hours = null;
        }

        $this->save();
    }
}
