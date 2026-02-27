<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class leave_application extends Model
{
    protected $fillable = [
        'employee_id',
        'supervisor_id',
        'type',
        'other_reason',
        'start_date',
        'end_date',
        'study_days_note',
        'replacement_employee_id',
        'replacement_status',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(staff::class, 'employee_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(staff::class, 'supervisor_id');
    }

    public function replacement()
    {
        return $this->belongsTo(staff::class, 'replacement_employee_id');
    }



    public function approvals()
    {
        return $this->hasMany(leave_approval::class);
    }

    public function userApproval()
    {
        $this->approvals()->where('approver_id', Auth::id());
    }

    public function scopePendingForUser($query, $userId)
    {
        return $query->whereHas('approvals', function ($q) use ($userId) {
            $q->where('approver_id', $userId)
                ->whereNull('action');
        });
    }

    public function isPendingForUser($userOrId): bool
    {
        $userId = $userOrId instanceof User ? $userOrId->id : $userOrId;

        return $this->approvals()
            ->where('approver_id', $userId)
            ->whereNull('action')
            ->exists();
    }
}
