@extends('layouts.main')

@section('page-title', 'Leave Application Details')

@section('header')
    <style>
        .timeline::before {
            left: 31px;
        }

        .timeline>div {
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">

                {{-- Main Application Card --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt me-2"></i>Application Information
                        </h3>
                        <div class="card-tools">
                            @php
                                $statusBadge =
                                    [
                                        'Pending' => 'warning',
                                        'Approved' => 'success',
                                        'Rejected' => 'danger',
                                    ][$leave_application->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusBadge }} fs-6 px-3 py-2">
                                {{ $leave_application->status }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            {{-- Employee --}}
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-uppercase fs-7 fw-bold">Employee</label>
                                <div class="d-flex align-items-center mt-1">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $leave_application->employee->user->name }}</h5>
                                        <small
                                            class="text-muted">{{ $leave_application->employee->user->email ?? 'No email' }}</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Supervisor --}}
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-uppercase fs-7 fw-bold">Supervisor</label>
                                <div class="d-flex align-items-center mt-1">
                                    @if ($leave_application->supervisor)
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ $leave_application->supervisor->user->name }}</h5>
                                            <small class="text-muted">Assigned Supervisor</small>
                                        </div>
                                    @else
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-question"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 text-muted">Not Assigned</h5>
                                            <small class="text-danger">No supervisor found</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            {{-- Leave Type --}}
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-uppercase fs-7 fw-bold">Leave Type</label>
                                <h4 class="mt-1">
                                    <span class="badge bg-info fs-6">{{ $leave_application->type }}</span>
                                </h4>
                                @if ($leave_application->type == 'Other' && $leave_application->other_reason)
                                    <div class="alert alert-light border mt-2">
                                        <strong>Reason:</strong> {{ $leave_application->other_reason }}
                                    </div>
                                @endif
                            </div>

                            {{-- Replacement --}}
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-uppercase fs-7 fw-bold">Replacement Employee</label>
                                @if ($leave_application->replacement)
                                    <div class="d-flex align-items-center mt-1">
                                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-user-clock"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ $leave_application->replacement->user->name }}</h5>
                                            @php
                                                $repBadge =
                                                    [
                                                        'Pending' => 'warning',
                                                        'Accepted' => 'success',
                                                        'Rejected' => 'danger',
                                                    ][$leave_application->replacement_status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $repBadge }}">
                                                {{ $leave_application->replacement_status }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Replacement Action Buttons --}}
                                    @if (auth()->check() &&
                                            auth()->user()->staff?->id == $leave_application->replacement_employee_id &&
                                            $leave_application->replacement_status == \App\Helpers\ReplacementStatus::Pending->value)
                                        <div class="mt-3 d-flex gap-2">
                                            <form method="POST"
                                                action="{{ route('leave.replacement.respond', $leave_application->id) }}"
                                                class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check me-1"></i>Accept Responsibility
                                                </button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('leave.replacement.respond', $leave_application->id) }}"
                                                class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @else
                                    <div class="d-flex align-items-center mt-1 text-muted">
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">No replacement assigned</h5>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <hr>

                        {{-- Dates --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-uppercase fs-7 fw-bold">Start Date</label>
                                <h5 class="mt-1 text-primary">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    {{ $leave_application->start_date?->format('d-M-Y') ?? 'Not set' }}
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-uppercase fs-7 fw-bold">End Date</label>
                                <h5 class="mt-1 text-primary">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    {{ $leave_application->end_date?->format('d-M-Y') ?? 'Not set' }}
                                </h5>
                            </div>
                        </div>
                        @if ($leave_application->other_reason)

                        <div class="col-md-12">
                            <label class="text-muted text-uppercase fs-7 fw-bold">Additional Information</label>
                            <h5 class="mt-1 text-primary">
                                <i class="fas fa-calendar-check me-2"></i>
                                {{ $leave_application->other_reason ?? '-' }}
                            </h5>
                        </div>
                        @endif
                        {{-- Study Days Note --}}
                        @if ($leave_application->study_days_note)
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fas fa-book me-3 fa-lg"></i>
                                <div>
                                    <strong>Study Days Note:</strong><br>
                                    {{ $leave_application->study_days_note }}
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- Sidebar Actions --}}
            <div class="col-md-4">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('leave.applications.index') }}" class="btn btn-secondary btn-block mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>

                        @hasanyrole('Admin|Super')
                            <a href="{{ route('leave.application.edit', $leave_application->id) }}"
                                class="btn btn-warning btn-block mb-2">
                                <i class="fas fa-edit me-1"></i>Edit Application
                            </a>
                            <form action="{{ route('leave.application.destroy', $leave_application->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this application?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash me-1"></i>Delete Application
                                </button>
                            </form>
                        @endhasanyrole
                    </div>
                </div>

                {{-- Application Summary Card --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Summary</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-transparent">
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                <span>Duration</span>
                                <span class="badge bg-primary rounded-pill">
                                    {{ $leave_application->start_date && $leave_application->end_date ? $leave_application->start_date->diffInDays($leave_application->end_date) + 1 . ' days' : 'N/A' }}
                                </span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                <span>Applied On</span>
                                <span>{{ $leave_application->created_at?->format('d-M-Y') }}</span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                <span>Last Updated</span>
                                <span>{{ $leave_application->updated_at?->format('d-M-Y H:i') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
