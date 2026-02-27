<?php

namespace App\Http\Controllers;

use App\Helpers\LeaveAction;
use App\Helpers\LeaveStatus;
use App\Models\leave_application;
use App\Models\leave_approval;
use App\Models\staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class LeaveApprovalController extends Controller
{
    public function approveFormNew(leave_application $application)
    {

        $approval = $application->approvals()
            ->where('approver_id', Auth::id())
            ->first();
        // $approval = $application->approvals()
        //     ->where('approver_id', 'HR')->whereNull('signed_at')
        //     ->first();

        // if (!$approval) {
        //     return back()->with('error', 'No pending approval found for this application.');
        // }

        if (!Auth::user()->hasAnyRole(['HR', 'Admin', 'Super'])) {
            abort(403, 'Only HR can approve leave applications.');
        }


        // if ()

        return view('leave_approvals.approve', compact('approval'));
    }

    public function approve(Request $request, leave_approval $approval)
    {
        $approval->load('application.employee');

        if (!Auth::user()->hasAnyRole(['HR', 'Admin', 'Super'])) {
            abort(403, 'You are not authorized to approve this application.');
        }

        $request->validate([
            'action'    => ['required', new Enum(LeaveAction::class)],
            'comment'   => 'nullable|string',
        ]);


        $approval->update([
            'action'         => $request->action,
            'comment'        => $request->comment,
            'signed_at'      => now(),
            'approver_id'    => Auth::id(),
        ]);

      if ($request->action === LeaveAction::Rejected->value) {
        $approval->application->update(['status' => LeaveStatus::Rejected->value]);
        return redirect()->route('leave.applications.index')->with('error', 'Application rejected rejected');
      }

      $approval->application->update(['status' => LeaveStatus::Fully_Approved->value]);


        return redirect()->route('leave.applications.index')->with('success', 'Approval submitted successfully.');
    }

    public function pendingForHR()
    {
        $pendingApprovals = leave_approval::with(['application.employee.user'])->where('approver_role', 'HR')->whereNull('signed_at')->latest()->get();

        return view('leave_applications.index', compact('pendingApprovals'));
    }
}
