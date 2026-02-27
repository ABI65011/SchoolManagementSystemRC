<?php

namespace App\Http\Controllers;

use App\Models\leave_application;
use Illuminate\Http\Request;

class LeaveReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applications = leave_application::with(['employee.user', 'supervisor.user', 'approvals', 'replacement.user'])
            ->latest()->get();
        return view('leave_reports.summary', [
            'applications' => $applications,
            'title' => 'All Applications Summary',
        ]);
    }

    public function pending()
    {
        $applications = $this->getLeaveApplicationsByStatus('Pending');
        return view('leave_reports.summary', [
            'applications' => $applications,
            'title' => 'Pending Applications Summary',
        ]);
    }
    public function approved()
    {
        $applications = $this->getLeaveApplicationsByStatus('Fully Approved');
        return view('leave_reports.summary', [
            'applications' => $applications,
            'title' => 'Approved Applications Summary',
        ]);
    }
    public function rejected()
    {
        $applications = $this->getLeaveApplicationsByStatus('Rejected');
        return view('leave_reports.summary', [
            'applications' => $applications,
            'title' => 'Rejected Applications Summary',
        ]);
    }



    private function getLeaveApplicationsByStatus($status)
    {
        return leave_application::with([
            'employee.user',
            'supervisor.user',
            'approvals',
            'replacement.user'
        ])->where('status', $status)->orderBy('updated_at', 'desc')->get();
    }


}
