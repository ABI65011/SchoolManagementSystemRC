@extends('layouts.main')

@section('title', 'Attendance Records')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="m-0">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Attendance Records
                </h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('admin.attendance.create') }}" class="btn btn-success">
                    <i class="fas fa-plus mr-2"></i>
                    Manual Entry
                </a>
                <a href="{{ route('admin.attendance.location.index') }}" class="btn btn-info ml-2">
                    <i class="fas fa-cog mr-2"></i>
                    Location
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
                <form action="{{ route('admin.attendance.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Staff</label>
                                <select name="staff_id" class="form-control select2">
                                    <option value="">All Staff</option>
                                    @foreach ($staffList as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control"
                                    value="{{ request('from_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present
                                    </option>
                                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late
                                    </option>
                                    <option value="early_departure"
                                        {{ request('status') == 'early_departure' ? 'selected' : '' }}>Early Departure
                                    </option>
                                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent
                                    </option>
                                    <option value="holiday_work"
                                        {{ request('status') == 'holiday_work' ? 'selected' : '' }}>Holiday Work</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter mr-2"></i>
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.attendance.index') }}" class="btn btn-default ml-2">
                                <i class="fas fa-undo mr-2"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Records Table -->
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
                                <th>ID</th>
                                <th>Staff</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                                <th>Status</th>
                                <th>Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->id }}</td>
                                    <td>
                                        <strong>{{ $attendance->staff->user->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $attendance->staff->user->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $attendance->attendance_date->format('M d, Y') }}</td>
                                    <td>
                                        @if ($attendance->check_in)
                                            {{ $attendance->check_in->format('h:i A') }}
                                            <br>
                                            <small class="text-muted">{{ $attendance->check_in_method }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($attendance->check_out)
                                            {{ $attendance->check_out->format('h:i A') }}
                                            <br>
                                            <small class="text-muted">{{ $attendance->check_out_method }}</small>
                                        @else
                                            <span class="badge text-bg-warning">Not checked out</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($attendance->working_hours)
                                            {{ number_format($attendance->working_hours, 2) }} hrs
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge text-bg-{{ $attendance->status === 'present'
                                                ? 'success'
                                                : ($attendance->status === 'late'
                                                    ? 'warning'
                                                    : ($attendance->status === 'early_departure'
                                                        ? 'info'
                                                        : ($attendance->status === 'absent'
                                                            ? 'danger'
                                                            : 'secondary'))) }}">
                                            {{ ucfirst(str_replace('_', ' ', $attendance->status->value)) }}
                                        </span>
                                        @if ($attendance->late_minutes > 0)
                                            <br>
                                            <small class="text-danger">{{ $attendance->late_minutes }} min late</small>
                                        @endif
                                        @if ($attendance->early_departure_minutes > 0)
                                            <br>
                                            <small class="text-warning">{{ $attendance->early_departure_minutes }} min
                                                early</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge text-bg-{{ $attendance->check_in_method === 'manual' ? 'secondary' : 'success' }}">{{ ucfirst(str_replace('_', ' ', $attendance->check_in_method->value)) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.attendance.edit', $attendance) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.attendance.destroy', $attendance) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <br>
                                        No attendance records found
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
                placeholder: "Select staff",
                allowClear: true
            });
        });
    </script>
@endsection
