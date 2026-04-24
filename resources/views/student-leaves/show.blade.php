@extends('layouts.main')

@section('title', 'Leave Request Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="m-0">
                <i class="fas fa-door-open mr-2"></i>
                Leave Request Details
            </h3>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('student-leaves.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Main Details Card -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Request Information</h3>
                    <div class="card-tools">
                        <span class="status-badge status-{{ $studentLeave->status }}">
                            {{ $studentLeave->status_label }}
                        </span>
                        @if($studentLeave->is_overdue)
                            <span class="badge badge-danger ml-2">Overdue</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Student:</th>
                                    <td>
                                        <strong>{{ $studentLeave->student->full_name }}</strong>
                                     </td>
                                 </tr>
                                 <tr>
                                    <th>Class:</th>
                                    <td>{{ $studentLeave->student->current_class->name ?? 'N/A' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Leave Type:</th>
                                    <td>{{ $studentLeave->type_label }}</td>
                                 </tr>
                                 <tr>
                                    <th>Destination:</th>
                                    <td>{{ $studentLeave->destination }}</td>
                                 </tr>
                                 <tr>
                                    <th>Reason:</th>
                                    <td>{{ $studentLeave->reason }}</td>
                                 </tr>
                             </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                 <tr>
                                    <th width="150">Departure:</th>
                                    <td>{{ $studentLeave->departure_time->format('d/m/Y H:i') }}</td>
                                 </tr>
                                 <tr>
                                    <th>Expected Return:</th>
                                    <td>{{ $studentLeave->expected_return_time->format('d/m/Y H:i') }}</td>
                                 </tr>
                                 <tr>
                                    <th>Actual Return:</th>
                                    <td>{{ $studentLeave->actual_return_time ? $studentLeave->actual_return_time->format('d/m/Y H:i') : 'Not yet returned' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Authorized By:</th>
                                    <td>{{ $studentLeave->authorizedBy->name ?? 'Pending' }}</td>
                                 </tr>
                                 @if($studentLeave->notes)
                                 <tr>
                                    <th>Notes:</th>
                                    <td>{{ $studentLeave->notes }}</td>
                                 </tr>
                                 @endif
                             </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Leave Timeline</h3>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li>
                            <i class="fas fa-file-alt bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time"> {{ $studentLeave->created_at->format('d/m/Y H:i') }}</span>
                                <h3 class="timeline-header">Request Created</h3>
                                <div class="timeline-body">
                                    Leave request submitted by {{ $studentLeave->authorizedBy->name ?? 'System' }}
                                </div>
                            </div>
                        </li>
                        @if($studentLeave->status != 'pending')
                        <li>
                            <i class="fas fa-check-circle bg-success"></i>
                            <div class="timeline-item">
                                <span class="time">{{ $studentLeave->updated_at->format('d/m/Y H:i') }}</span>
                                <h3 class="timeline-header">Request {{ ucfirst($studentLeave->status) }}</h3>
                                <div class="timeline-body">
                                    @if($studentLeave->status == 'approved')
                                        Leave request approved by {{ $studentLeave->authorizedBy->name }}
                                    @elseif($studentLeave->status == 'denied')
                                        Leave request denied
                                    @elseif($studentLeave->status == 'active')
                                        Student signed out at gate
                                    @elseif($studentLeave->status == 'returned')
                                        Student signed in upon return
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons Sidebar -->
        <div class="col-md-4">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @if($studentLeave->status == 'pending')
                            <form action="{{ route('student-leaves.approve', $studentLeave) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Approve this leave request?')">
                                    <i class="fas fa-check-circle"></i> Approve Leave
                                </button>
                            </form>
                            <form action="{{ route('student-leaves.deny', $studentLeave) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Deny this leave request?')">
                                    <i class="fas fa-times-circle"></i> Deny Leave
                                </button>
                            </form>
                        @endif

                        @if($studentLeave->status == 'approved')
                            <form action="{{ route('student-leaves.sign-out', $studentLeave) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block" onclick="return confirm('Sign out this student?')">
                                    <i class="fas fa-sign-out-alt"></i> Sign Out Student
                                </button>
                            </form>
                        @endif

                        @if($studentLeave->status == 'active')
                            <form action="{{ route('student-leaves.sign-in', $studentLeave) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Sign in this student?')">
                                    <i class="fas fa-sign-in-alt"></i> Sign In Student
                                </button>
                            </form>
                        @endif
@if ($studentLeave->status == 'returned')

<p>
    Student is currently <strong>{{ $studentLeave->status_label }}</strong>. No actions required.
</p>
@endif
                    </div>
                </div>
            </div>

            <!-- Student Info Card -->
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Student Information</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="student-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px;">
                            {{ strtoupper(substr($studentLeave->student->first_name, 0, 1)) }}{{ strtoupper(substr($studentLeave->student->last_name, 0, 1)) }}
                        </div>
                    </div>
                    <table class="table table-sm">

                         <tr>
                            <th>Section:</th>
                            <td>{{ ucfirst($studentLeave->student->applying_section ?? 'N/A') }}</td>
                         </tr>
                         <tr>
                            <th>Gender:</th>
                            <td>{{ ucfirst($studentLeave->student->gender ?? 'N/A') }}</td>
                         </tr>
                     </table>
                    <a href="{{ route('students.show', $studentLeave->student) }}" class="btn btn-info btn-block">
                        <i class="fas fa-user"></i> View Full Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-pending { background: #ffc107; color: #856404; }
    .status-approved { background: #17a2b8; color: white; }
    .status-denied { background: #dc3545; color: white; }
    .status-active { background: #007bff; color: white; }
    .status-returned { background: #28a745; color: white; }

    .timeline {
        position: relative;
        padding: 0;
        list-style: none;
    }
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #ddd;
        left: 31px;
        margin: 0;
        border-radius: 2px;
    }
    .timeline > li {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline > li .timeline-item {
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        border-radius: 3px;
        margin-top: 0;
        margin-left: 60px;
        margin-right: 15px;
        padding: 0;
        position: relative;
    }
    .timeline > li .timeline-header {
        border-bottom: 1px solid #f4f4f4;
        padding: 8px 10px;
        font-size: 16px;
    }
    .timeline > li .timeline-body {
        padding: 10px;
    }
    .timeline > li .time {
        color: #999;
        float: right;
        padding: 10px;
        font-size: 12px;
    }
    .timeline > li i {
        width: 30px;
        height: 30px;
        line-height: 30px;
        font-size: 15px;
        text-align: center;
        position: absolute;
        color: #fff;
        background: #d2d6de;
        border-radius: 50%;
        top: 0;
        left: 18px;
        z-index: 1;
    }
</style>
@endsection
