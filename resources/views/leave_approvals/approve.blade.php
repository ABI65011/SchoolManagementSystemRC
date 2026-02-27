@extends('layouts.main')

@section('header')

    <style>
        .approval-card {
            border-left: 4px solid #007bff;
        }
        .stage-badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
        }
    </style>
@endsection

@section('page-title', 'Review Leave Application')

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-8">

                        <div class="card card-primary card-outline approval-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-gavel mr-2"></i>
                                    Reviewed by <span class="text-info">
                                        {{ $approval->approver_role }}
                                        </span>
                                </h3>
                                <div class="card-tools">
                                    <span class="badge badge-info stage-badge">
                                        <i class="fas fa-user-check mr-2"></i>
                                        {{ $approval->approver->name }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Application Details --}}
                                <div class="callout callout-info mb-4">
                                    <h5 class="mb-3"><i class="fas fa-file-alt mr-2"></i>&nbsp; Application Details</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td class="font-weight-bold text-muted" style="width: 30%">Employee:</td>
                                                    <td>{{ $approval->application->employee->user->name ?? '—' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold text-muted">Supervisor:</td>
                                                    <td>{{ $approval->application->supervisor->user->name ?? '—' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td class="font-weight-bold text-muted" style="width: 30%">Type:</td>
                                                    <td>
                                                        <span class="badge badge-secondary">{{ $approval->application->type ?? '—' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold text-muted">Period:</td>
                                                    <td>
                                                        <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                                        {{ $approval->application->start_date?->format('d-M-Y') ?? '—' }}
                                                        <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                                        {{ $approval->application->end_date?->format('d-M-Y') ?? '—' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Approval Form --}}
                                <form action="{{ route('leave.approvals.submit', $approval->id) }}" method="POST">
                                    @csrf

                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold">
                                            <i class="fas fa-tasks mr-1"></i>&nbsp;Decision <span class="text-danger">*</span>
                                        </label>
                                        @php
                                            use App\Helpers\LeaveAction;
                                        @endphp

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="custom-control custom-radio mb-2">
                                                    <input type="radio" id="actionApprove" name="action" value="{{ LeaveAction::Approved->value }}" class="custom-control-input" required>
                                                    <label class="custom-control-label text-success font-weight-bold" for="actionApprove">
                                                        <i class="fas fa-check-circle mr-1"></i> Approve
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="custom-control custom-radio mb-2">
                                                    <input type="radio" id="actionReject" name="action" value="{{ LeaveAction::Rejected->value }}" class="custom-control-input" required>
                                                    <label class="custom-control-label text-danger font-weight-bold" for="actionReject">
                                                        <i class="fas fa-times-circle mr-1"></i> Reject
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        @error('action')
                                            <div class="text-danger small mt-2">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold">
                                            <i class="fas fa-comment-alt mr-1"></i>&nbsp;Comment (Optional)
                                        </label>
                                        <textarea name="comment" rows="4" class="form-control" placeholder="Enter your comments or remarks regarding this decision..."></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('leave.applications.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i>&nbsp;Back
                                        </a>
                                        <button class="btn btn-primary btn-lg" type="submit">
                                            <i class="fas fa-paper-plane mr-1"></i>&nbsp;Submit Decision
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('input[name="action"]').on('change', function() {
                if ($(this).val() === '{{ LeaveAction::Approved->value }}') {
                    $('.approval-card').removeClass('border-left-danger').addClass('border-left-success');
                } else {
                    $('.approval-card').removeClass('border-left-success').addClass('border-left-danger');
                }
            });
        });
    </script>
@endsection
