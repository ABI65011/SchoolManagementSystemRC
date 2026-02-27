<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class leave_approval extends Model
{
    protected $fillable = [
        'leave_application_id',
        'approver_id',
        'approver_role',
        'signature_path',
        'action',
        'comment',
        'signed_at',
        'stage',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(leave_application::class, 'leave_application_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function userApproval()
    {
        $llm = leave_application::where('employee_id', Auth::id())->get();
        dd($llm);
        // return leave_approval::where('')
    }
}
