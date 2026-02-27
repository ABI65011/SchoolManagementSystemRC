@extends('layouts.main')

@section('page-title', 'Leave Application Details')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-invoice me-2"></i>Application #{{ $leave_application->id }}
                        </h3>
                        <div class="card-tools">
                            @php
                                $badgeClass = [
                                    'Pending' => 'warning',
                                    'Approved' => 'success',
                                    'Rejected' => 'danger',
                                    'Awaiting_Replacement_Confirmation' => 'secondary',
                                    'Replacement_Rejected' => 'danger',
                                ][$leave_application->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }} fs-6 px-3">
                                {{ str_replace('_', ' ', $leave_application->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            {{-- Left Column --}}
                            <div class="col-md-6">

                                {{-- Employee Info --}}
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-user"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-uppercase text-muted fs-7">Employee</span>
                                        <span class="info-box-number fs-5">{{ $leave_application->employee->user->name }}</span>
                                    </div>
                                </div>

                                {{-- Supervisor Info --}}
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-user-tie"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-uppercase text-muted fs-7">Supervisor</span>
                                        <span class="info-box-number fs-5">
                                            {{ $leave_application->supervisor?->user->name ?? 'Not Assigned' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Leave Type --}}
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-tag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-uppercase text-muted fs-7">Leave Type</span>
                                        <span class="info-box-number fs-5">{{ $leave_application->type }}</span>
                                        @if ($leave_application->type === 'Other' && $leave_application->other_reason)
                                            <span class="progress-description text-muted">
                                                Reason: {{ $leave_application->other_reason }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            {{-- Right Column --}}
                            <div class="col-md-6">

                                {{-- Replacement Info --}}
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-user-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-uppercase text-muted fs-7">Replacement</span>
                                        <span class="info-box-number fs-5">
                                            {{ $leave_application->replacementEmployee?->user->name ?? 'Not Assigned' }}
                                        </span>
                                        @if($leave_application->replacementEmployee)
                                            @php
                                                $repBadge = [
                                                    'Pending' => 'warning',
                                                    'Accepted' => 'success',
                                                    'Rejected' => 'danger',
                                                ][$leave_application->replacement_status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $repBadge }} mt-1">
                                                {{ $leave_application->replacement_status }}
                                            </span>

                                            {{-- Action Buttons --}}
                                            @if (auth()->check() &&
                                                auth()->user()->staff?->id == $leave_application->replacement_employee_id &&
                                                $leave_application->replacement_status === \App\Helpers\ReplacementStatus::Pending->value)
                                                <div class="mt-2">
                                                    <form method="POST" action="{{ route('leave.replacement.respond', $leave_application->id) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="accept">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check"></i> Accept
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('leave.replacement.respond', $leave_application->id) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                {{-- Date Range --}}
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-uppercase text-muted fs-7">Date Range</span>
                                        <span class="info-box-number fs-5">
                                            {{ $leave_application->start_date?->format('d-M-Y') }}
                                        </span>
                                        <span class="progress-description">
                                            <i class="fas fa-arrow-right"></i>
                                            {{ $leave_application->end_date?->format('d-M-Y') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Study Note --}}
                                @if ($leave_application->study_days_note)
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="fas fa-book me-3"></i>
                                        <div>
                                            <strong>Study Note:</strong> {{ $leave_application->study_days_note }}
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Approval Flow --}}
                        <h4 class="mb-3"><i class="fas fa-tasks me-2"></i>Approval Progress</h4>

                        @forelse ($leave_application->approvals as $step)
                            <div class="callout callout-{{ $step->signed_at ? 'success' : 'warning' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">
                                        Stage {{ $step->stage }}: {{ $step->approver_role }}
                                    </h5>
                                    <span class="badge bg-{{ $step->signed_at ? 'success' : 'warning' }}">
                                        {{ $step->signed_at ? 'Completed' : 'Pending' }}
                                    </span>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Approver:</strong> {{ $step->approver->name }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1">
                                            <strong>Action:</strong>
                                            <span class="text-{{ ($step->action ?? 'pending') == 'approved' ? 'success' : (($step->action ?? 'pending') == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($step->action ?? 'Pending') }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1">
                                            <strong>Signed:</strong>
                                            {{ $step->signed_at?->format('d-M-Y') ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                                @if (!is_null($step->comment))
                                    <div class="mt-2 p-2 bg-white rounded">
                                        <small class="text-muted">Comment:</small>
                                        <p class="mb-0">{{ $step->comment }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-secondary">
                                <i class="fas fa-info-circle me-2"></i>
                                Approval process not started yet.
                            </div>
                        @endforelse

                    </div>

                    <div class="card-footer">
                        <a href="{{ route('leave.applications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
