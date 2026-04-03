@extends('layouts.main')

@section('title', 'MoES Compliance Report')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3>
                <i class="fas fa-chart-line mr-2"></i>
                MoES Compliance Report
            </h3>
            <p class="text-muted">{{ $report['period'] }}</p>
        </div>
        <div class="col-md-4 text-right">
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-6">
            <div class="info-box bg-primary">
                <div class="info-box-content">
                    <span class="info-box-text">Government Sponsored (UPE/USE)</span>
                    <span class="info-box-number">{{ $report['government']['total_present'] }} / {{ $report['government']['total_present'] + $report['government']['total_absent'] }} present</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $report['government']['attendance_rate'] }}%"></div>
                    </div>
                    <span class="progress-description">{{ $report['government']['attendance_rate'] }}% Attendance Rate</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box bg-warning">
                <div class="info-box-content">
                    <span class="info-box-text">Private Students</span>
                    <span class="info-box-number">{{ $report['private']['total_present'] }} / {{ $report['private']['total_present'] + $report['private']['total_absent'] }} present</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $report['private']['attendance_rate'] }}%"></div>
                    </div>
                    <span class="progress-description">{{ $report['private']['attendance_rate'] }}% Attendance Rate</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Discrepancies -->
    @if($report['discrepancies']->count() > 0)
    <div class="card card-danger">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exclamation-triangle"></i> Headcount Discrepancies Detected
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Class</th>
                        <th>Physical Count</th>
                        <th>System Count</th>
                        <th>Difference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['discrepancies'] as $disc)
                    <tr>
                        <td>{{ $disc->date->format('d M Y') }}</td>
                        <td>{{ $disc->class->name }}</td>
                        <td class="text-center">{{ $disc->physical_count }}</td>
                        <td class="text-center">{{ $disc->system_count }}</td>
                        <td class="text-center">
                            <span class="badge text-bg-danger">{{ $disc->difference }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Compliance Note:</strong>
        @if($report['government']['attendance_rate'] >= 85)
            <span class="badge text-bg-success">Compliant - UPE/USE funding is justified</span>
        @elseif($report['government']['attendance_rate'] >= 70)
            <span class="badge text-bg-warning">Attention Needed - DES may schedule inspection</span>
        @else
            <span class="badge text-bg-danger">Intervention Required - Risk of funding audit</span>
        @endif
    </div>
</div>
@endsection
