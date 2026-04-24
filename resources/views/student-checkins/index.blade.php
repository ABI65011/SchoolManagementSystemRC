{{-- resources/views/student-checkins/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Student Check-In')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        .checkin-btn {
            transition: all 0.3s;
        }

        .checkin-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .checked-in-badge {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .not-checked-badge {
            background-color: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="m-0">
                    <i class="fas fa-check-circle mr-2"></i>
                    Student Check-In
                </h3>
                <p class="text-muted">{{ now()->format('l, d M Y') }}</p>
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
                <form method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search Student</label>
                                <input type="text" name="search" class="form-control" placeholder="Search by name..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Section</label>
                                <select name="section" class="form-control">
                                    <option value="">All</option>
                                    <option value="day" {{ request('section') == 'day' ? 'selected' : '' }}>Day</option>
                                    <option value="boarding" {{ request('section') == 'boarding' ? 'selected' : '' }}>
                                        Boarding</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status_filter" class="form-control">
                                    <option value="">All</option>
                                    <option value="checked_in"
                                        {{ request('status_filter') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                                    <option value="not_checked_in"
                                        {{ request('status_filter') == 'not_checked_in' ? 'selected' : '' }}>Not Checked In
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('student-checkins.index') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-2"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Student Check-In List</h3>
                <div class="card-tools">
                    <span class="badge text-bg-primary">{{ $students->total() }} students</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Check-In Time</th>
                                <th>Status</th>
                                <th>Entered By</th>
                                <th width="100">Action</th>
                             </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $checkIn = $todayCheckIns[$student->id] ?? null;
                                    $isCheckedIn = !is_null($checkIn);
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $student->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $student->admission_number }}</small>
                                    </td>
                                    <td>{{ $student->current_class->name ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge text-bg-{{ $student->applying_section === 'day' ? 'info' : 'primary' }}">
                                            {{ ucfirst($student->applying_section ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($isCheckedIn)
                                            <span class="checked-in-badge">
                                                {{ $checkIn->check_in_time->format('H:i:s') }}
                                            </span>&nbsp;
                                            <span class="checked-in-badge">
                                                {{ $checkIn->check_in_date->format('Y-m-d') }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($isCheckedIn)
                                            <span class="badge text-bg-success">Checked In</span>
                                        @else
                                            <span class="badge text-bg-danger">Not Checked In</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($isCheckedIn)
                                            {{ $checkIn->enteredBy->name ?? 'N/A' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!$isCheckedIn)
                                            <form action="{{ route('student-checkins.check-in', $student) }}" method="POST"
                                                style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success checkin-btn">
                                                    <i class="fas fa-check-circle"></i> Check In
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-check"></i> Done
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <br>
                                        No students found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $students->links() }}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Select class",
                allowClear: true
            });

            // Load summary statistics via AJAX (no page reload for stats)
            function loadSummary() {
                $.ajax({
                    url: '{{ route("student-checkins.summary") }}',
                    method: 'GET',
                    success: function(response) {
                        $('#totalStudents').text(response.total_students);
                        $('#checkedInCount').text(response.checked_in);
                        $('#notCheckedInCount').text(response.not_checked_in);
                        var rate = response.total_students > 0 ?
                            ((response.checked_in / response.total_students) * 100).toFixed(1) :
                            0;
                        $('#attendanceRate').text(rate + '%');
                    },
                    error: function(xhr) {
                        console.error('Summary error:', xhr);
                    }
                });
            }

            // Load summary on page load
            loadSummary();

            // Optional: Auto-refresh summary every 30 seconds
            setInterval(loadSummary, 30000);
        });
    </script>
@endsection
