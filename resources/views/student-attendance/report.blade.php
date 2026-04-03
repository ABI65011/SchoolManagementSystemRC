@extends('layouts.main')

@section('title', 'Attendance Report - ' . $class->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3>
                <i class="fas fa-chart-bar mr-2"></i>
                Attendance Report: {{ $class->name }}
            </h3>
            <p class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="col-md-4 text-right">
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-info">
                <div class="info-box-content">
                    <span class="info-box-text">Total Students</span>
                    <span class="info-box-number">{{ $summary['total_students'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-primary">
                <div class="info-box-content">
                    <span class="info-box-text">Government (UPE/USE)</span>
                    <span class="info-box-number">{{ $summary['government_students'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <div class="info-box-content">
                    <span class="info-box-text">Private</span>
                    <span class="info-box-number">{{ $summary['private_students'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <div class="info-box-content">
                    <span class="info-box-text">Total Days</span>
                    <span class="info-box-number">{{ $summary['total_days'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Attendance Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Student Attendance Summary</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Admission No</th>
                            <th>Sponsorship</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Excused</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $studentId => $records)
                            @php
                                $student = $records->first()->student;
                                $present = $records->where('status', 'present')->count();
                                $absent = $records->where('status', 'absent')->count();
                                $late = $records->where('status', 'late')->count();
                                $excused = $records->where('status', 'excused')->count();
                                $total = $present + $absent + $late + $excused;
                                $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $student->full_name }}</strong></td>
                                <td>{{ $student->admission_number }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $student->sponsorship === 'government' ? 'primary' : 'warning' }}">
                                        {{ $student->sponsorship === 'government' ? 'UPE/USE' : 'Private' }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $present }}</td>
                                <td class="text-center">{{ $absent }}</td>
                                <td class="text-center">{{ $late }}</td>
                                <td class="text-center">{{ $excused }}</td>
                                <td class="text-center">
                                    <span class="badge text-bg-{{ $percentage >= 85 ? 'success' : ($percentage >= 70 ? 'warning' : 'danger') }}">
                                        {{ $percentage }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
