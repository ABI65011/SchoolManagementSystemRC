@extends('layouts.main')

@section('title', 'Student Leave Requests')
@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<style>
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
    }
    .status-pending { background: #ffc107; color: #856404; }
    .status-approved { background: #17a2b8; color: white; }
    .status-denied { background: #dc3545; color: white; }
    .status-active { background: #007bff; color: white; }
    .status-returned { background: #28a745; color: white; }
    .overdue { border-left: 3px solid #dc3545; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3 class="m-0">
                <i class="fas fa-door-open mr-2"></i>
                Student Leave Requests
            </h3>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('student-leaves.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>
                New Leave Request
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
            <form method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Student</label>
                            <select name="student_id" class="form-control select2">
                                <option value="">All Students</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->full_name }} ({{ $student->admission_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('student-leaves.index') }}" class="btn btn-default">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Leaves Table -->
    <div class="card card-outline card-secondary mt-2">
        <div class="card-header">
            <h3 class="card-title">Leave Requests</h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $leaves->total() }} records</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Type</th>
                            <th>Destination</th>
                            <th>Departure</th>
                            <th>Expected Return</th>
                            <th>Status</th>
                            <th>Actions</th>
                         </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            <tr class="{{ $leave->is_overdue ? 'overdue' : '' }}">
                                <td>{{ $leave->id }}</td>
                                <td>
                                    <strong>{{ $leave->student->full_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $leave->student->admission_number }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $leave->type_label }}</span>
                                </td>
                                <td>{{ $leave->destination }}</td>
                                <td>{{ $leave->departure_time->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $leave->expected_return_time->format('d/m/Y H:i') }}
                                    @if($leave->is_overdue)
                                        <br>
                                        <span class="badge badge-danger">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $leave->status }}">
                                        {{ $leave->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('student-leaves.show', $leave) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-door-open fa-3x mb-3"></i>
                                    <br>
                                    No leave requests found.
                                 </td>
                            </tr>
                        @endforelse
                    </tbody>
                 </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            {{ $leaves->links() }}
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select student",
            allowClear: true
        });

        function loadCounts() {
            $.ajax({
                url: '{{ route("student-leaves.active-list") }}',
                method: 'GET',
                success: function(response) {
                    $('#activeCount').text(response.count);
                }
            });
            $.ajax({
                url: '{{ route("student-leaves.overdue-list") }}',
                method: 'GET',
                success: function(response) {
                    $('#overdueCount').text(response.count);
                }
            });
        }
        loadCounts();
        setInterval(loadCounts, 30000);
    });
</script>
@endsection
