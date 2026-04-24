<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StudentCheckIn extends Model
{
    protected $fillable = [
        'student_id',
        'check_in_date',
        'check_in_time',
        'entered_by'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_in_time' => 'datetime',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }
    public function enteredBy() {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function getFormattedTimeAtribute() {
        return $this->check_in_time ? Carbon::parse($this->check_in_time)->format('h:i A') : null;
    }
    public function scopeForDate($query, $date = null)
    {
        $date = $date ?? now()->toDateString();
        return $query->where('check_in_date', $date);
    }

    public function scopeToday($query)
    {
        return $query->where('check_in_date', now()->toDateString());
    }
}
