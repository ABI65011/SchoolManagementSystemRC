<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StudentLeave extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'departure_time',
        'expected_return_time',
        'actual_return_time',
        'destination',
        'reason',
        'authorized_by',
        'signed_out_by',
        'signed_in_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'expected_return_time' => 'datetime',
        'actual_return_time' => 'datetime',
    ];

    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function authorizedBy()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function signedOutBy()
    {
        return $this->belongsTo(User::class, 'signed_out_by');
    }

    public function signedInBy()
    {
        return $this->belongsTo(User::class, 'signed_in_by');
    }


    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'pass_leave' => 'Pass Leave',
            'emergency' => 'Emergency',
            'weekend' => 'Weekend',
            'medical' => 'Medical',
            default => ucfirst($this->type),
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'denied' => 'Denied',
            'active' => 'Active',
            'returned' => 'Returned',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'denied' => 'danger',
            'active' => 'primary',
            'returned' => 'success',
            default => 'secondary',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'active' && now()->gt($this->expected_return_time);
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('departure_time', $date);
    }

    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'authorized_by' => Auth::id(),
        ]);
    }

    public function deny($reason = null)
    {
        $this->update([
            'status' => 'denied',
            'notes' => $reason ?? $this->notes,
        ]);
    }

    public function signOut()
    {
        $this->update([
            'status' => 'active',
            'signed_out_by' => Auth::id(),
        ]);
    }

    public function signIn()
    {
        $this->update([
            'status' => 'returned',
            'actual_return_time' => now(),
            'signed_in_by' => Auth::id(),
        ]);
    }
}
