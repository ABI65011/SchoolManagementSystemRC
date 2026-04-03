@extends('layouts.main')

@section('title', 'Student Attendance')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        .attendance-present {
            background-color: #d4edda;
        }

        .attendance-absent {
            background-color: #f8d7da;
        }

        .attendance-late {
            background-color: #fff3cd;
        }

        .attendance-excused {
            background-color: #d1ecf1;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="m-0">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Student Attendance
                </h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('student-attendance.bulk') }}" class="btn btn-success">
                    <i class="fas fa-plus mr-2"></i>
                    Take Attendance
                </a>
                <a href="{{ route('student-attendance.moes-report') }}" class="btn btn-info ml-2">
                    <i class="fas fa-chart-line mr-2"></i>
                    MoES Report
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('student-attendance.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="class_id" class="form-control select2">
                                    <option value="">All Classes</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sponsorship</label>
                                <select name="sponsorship" class="form-control">
                                    <option value="">All</option>
                                    <option value="government"
                                        {{ request('sponsorship') == 'government' ? 'selected' : '' }}>Government (UPE/USE)
                                    </option>
                                    <option value="private" {{ request('sponsorship') == 'private' ? 'selected' : '' }}>
                                        Private</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fas fa-filter mr-2"></i> Apply
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('student-attendance.index') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-2"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Attendance Records</h3>
                <div class="card-tools">
                    <span class="badge text-bg-primary">{{ $attendances->total() }} records</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Sponsorship</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Late (min)</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                                    <td>
                                        <strong>{{ $attendance->student->full_name ?? 'N/A' }}</strong>
                                        <br>
                                        <small
                                            class="text-muted">{{ $attendance->student->admission_number ?? '' }}</small>
                                    </td>
                                    <td>{{ $attendance->class->name ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge text-bg-{{ $attendance->sponsorship === 'government' ? 'primary' : 'warning' }}">
                                            {{ $attendance->sponsorship_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $attendance->status_color }}">
                                            {{ $attendance->status_label }}
                                        </span>
                                        @if ($attendance->absence_reason)
                                            <br>
                                            <small class="text-muted">{{ $attendance->absence_reason }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') : '-' }}
                                    </td>
                                    <td>{{ $attendance->late_minutes ?: '-' }}</td>
                                    <td>{{ $attendance->recordedBy?->name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('student-attendance.edit', $attendance) }}"
                                            class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                        <br>
                                        No attendance records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select class",
                allowClear: true
            });
        });
    </script>
@endsection
