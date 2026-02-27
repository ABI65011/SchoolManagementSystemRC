@extends('layouts.main')

@section('header')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
            }

            .card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .badge {
                border: 1px solid #888;
                color: #000 !important;
            }

            .callout {
                border: 1px solid #ddd !important;
            }

            .content-header {
                display: none !important;
            }
        }

        .summary-card {
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection

@section('page-title', $title)

@section('content')
    <div class="content-wrapper">
        {{-- <div class="content-header no-print">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leave.applications.index') }}">Leave
                                    Applications</a></li>
                            <li class="breadcrumb-item active">Summary</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="content">
            <div class="container-fluid" id="print-area">

                {{-- Print Header (visible only when printing) --}}
                <div class="d-none d-print-block mb-4 text-center">
                    <h2>{{ $title }}</h2>
                    <p class="text-muted">Generated on {{ now()->format('d M Y, h:i A') }}</p>
                </div>

                {{-- Action Buttons --}}
                <div class="row mb-3 no-print">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between">
                                <h5 class="mb-0">Report Actions</h5>
                                <div class="ms-auto">
                                    <a class="btn btn-warning mr-2" onclick="printReport()">
                                        <i class="fas fa-print mr-1"></i> Print Report
                                    </a>
                                    <a class="btn btn-primary" href="{{ route('leave.applications.index') }}">
                                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Applications List --}}
                @forelse($applications as $app)
                    <div class="card summary-card card-outline-primary mb-4 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-user mr-2 text-primary"></i>
                                {{ $app->employee->user->name }}
                            </h3>
                            @php
                                $statusBadge =
                                    [
                                        'Fully Approved' => 'success',
                                        'Pending' => 'warning',
                                        'Approved' => 'success',
                                        'Rejected' => 'danger',
                                    ][$app->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $statusBadge }} badge-pill px-3 py-2">
                                {{ $app->status }}
                            </span>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="info-box p-2 rounded d-flex align-items-center">
                                        <span class="rounded-circle bg-success elevation-1 d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="fas fa-user-tie text-white"></i>
                                        </span>
                                        <span class="info-box-text font-weight-bold d-block">Supervisor: &nbsp;</span>
                                        <span class="info-box-number text-muted">
                                            {{ $app->supervisor->user->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box p-2 rounded d-flex align-items-center">
                                        <span class="rounded-circle bg-warning elevation-1 d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="fas fa-user-clock text-white" style="font-size: 0.8rem;"></i>
                                        </span>
                                        <span class="info-box-textfont-weight-bold d-block">Replacement: &nbsp;</span>
                                        <span class="info-box-number text-muted text-sm ">
                                            {{ $app->replacement->user->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box p-2 rounded d-flex align-items-center">
                                        <span class="rounded-circle bg-info elevation-1 d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="fas fa-clock text-white" style="font-size: 0.8rem;"></i>
                                        </span>
                                        <span class="info-box-text font-weight-bold d-block">Last Updated: &nbsp;</span>
                                        <span class="info-box-number text-muted text-sm">
                                            {{ $app->updated_at->format('d M Y, h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="row">
                                {{-- Leave Details --}}
                                <div class="col-md-6">
                                    <div class="callout callout-info">
                                        <h5 class="mb-3"><i class="fas fa-info-circle mr-1"></i> Leave Details</h5>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="font-weight-bold text-muted" style="width: 25%">Type:</td>
                                                <td>
                                                    <span class="badge text-bg-secondary">{{ $app->type }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold text-muted">Dates:</td>
                                                <td>
                                                    <i class="fas fa-calendar text-primary mr-1"></i>
                                                    {{ $app->start_date->format('d M Y') }}
                                                    <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                                    {{ $app->end_date->format('d M Y') }}
                                                    <span class="badge text-bg-light border ml-2">
                                                        {{ $app->start_date->diffInDays($app->end_date) + 1 }} day(s)
                                                    </span>
                                                </td>
                                            </tr>
                                            @if ($app->other_reason)
                                                <tr>
                                                    <td class="font-weight-bold text-muted">Reason:</td>
                                                    <td class="font-italic">{{ $app->other_reason }}</td>
                                                </tr>
                                            @endif
                                            @if ($app->study_days_note)
                                                <tr>
                                                    <td class="font-weight-bold text-muted">Study Note:</td>
                                                    <td class="font-italic">{{ $app->study_days_note }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>

                                {{-- Approval Progress --}}
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-tasks mr-1"></i> Approval Details</h5>
                                    @forelse($app->approvals as $step)
                                        <div
                                            class="callout callout-{{ $step->action === 'Approved' ? 'success' : ($step->action === 'Rejected' ? 'danger' : 'warning') }} py-2 px-3 mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <strong class="text-sm">{{ $step->approver_role }}</strong>
                                                <span
                                                    class="badge badge-{{ $step->action === 'Approved' ? 'success' : ($step->action === 'Rejected' ? 'danger' : 'secondary') }} badge-sm">
                                                    {{ $step->action ?? 'Pending' }}
                                                </span>
                                            </div>
                                            <div class="text-muted text-sm mb-1">
                                                <i class="fas fa-user mr-1"></i> {{ $step->approver->name }}
                                            </div>
                                            @if ($step->comment)
                                                <div class="text-sm font-italic border-top pt-1 mt-1">
                                                    <i class="fas fa-comment mr-1 text-muted"></i> {{ $step->comment }}
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="alert alert-secondary py-2">
                                            <i class="fas fa-info-circle mr-1"></i> No approval stages yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-muted text-sm no-print">
                            <i class="fas fa-hashtag mr-1"></i> Application ID: {{ $app->id }}
                            <span class="float-end">
                                <i class="fas fa-calendar-plus mr-1"></i> Created: {{ $app->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> No Records Found</h5>
                        No {{ strtolower(str_replace(' Summary', '', $title)) }} found matching your criteria.
                    </div>
                @endforelse

            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        function printReport() {

            let originalContents = document.body.innerHTML;
            let printContents = document.getElementById("print-area").innerHTML;


            let printWrapper = `
                <div class="wrapper">
                    <div class="content-wrapper" style="margin-left: 0;">
                        ${printContents}
                    </div>
                </div>
            `;

            document.body.innerHTML = printWrapper;


            window.print();


            document.body.innerHTML = originalContents;


            location.reload();
        }

        $(document).keydown(function(e) {
            if (e.ctrlKey && e.keyCode == 80) {
                e.preventDefault();
                printReport();
            }
        });
    </script>
@endsection
