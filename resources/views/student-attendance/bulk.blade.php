@extends('layouts.main')

@section('title', 'Bulk Attendance - ' . $class->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3>
                <i class="fas fa-calendar-check mr-2"></i>
                Attendance: {{ $class->name }}
            </h3>
            <p class="text-muted">Date: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('student-attendance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route('student-attendance.bulk-store') }}" method="POST">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="attendance_date" value="{{ $date }}">

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Student Name</strong>
                    </div>
                    <div class="col-md-2">
                        <strong>Sponsorship</strong>
                    </div>
                    <div class="col-md-2">
                        <strong>Status</strong>
                    </div>
                    <div class="col-md-2">
                        <strong>Check In Time</strong>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($students as $student)
                    @php
                        $existing = $existingAttendances[$student->id] ?? null;
                        $sponsorship = $student->sponsorship ?? 'government';
                    @endphp
                    <div class="border-bottom p-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <input type="hidden" name="students[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                <strong>{{ $student->full_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $student->admission_number }}</small>
                            </div>
                            <div class="col-md-2">
                                <span class="badge text-bg-{{ $sponsorship === 'government' ? 'primary' : 'warning' }}">
                                    {{ $sponsorship === 'government' ? 'UPE/USE' : 'Private' }}
                                </span>
                                <input type="hidden" name="students[{{ $loop->index }}][sponsorship]" value="{{ $sponsorship }}">
                            </div>
                            <div class="col-md-2">
                                <select name="students[{{ $loop->index }}][status]" class="form-control form-control-sm status-select">
                                    <option value="present" {{ $existing && $existing->status === 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="absent" {{ $existing && $existing->status === 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="late" {{ $existing && $existing->status === 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="excused" {{ $existing && $existing->status === 'excused' ? 'selected' : '' }}>Excused</option>
                                    <option value="holiday" {{ $existing && $existing->status === 'holiday' ? 'selected' : '' }}>Holiday</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="time" name="students[{{ $loop->index }}][check_in_time]"
                                       class="form-control form-control-sm check-in-time"
                                       value="{{ $existing && $existing->check_in_time ? \Carbon\Carbon::parse($existing->check_in_time)->format('H:i') : '' }}"
                                       style="display: none;">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <input type="text" name="students[{{ $loop->index }}][absence_reason]"
                                       class="form-control form-control-sm absence-reason"
                                       placeholder="Reason for absence (if applicable)"
                                       value="{{ $existing && $existing->absence_reason ? $existing->absence_reason : '' }}"
                                       style="display: none;">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Physical Headcount (for verification)</label>
                            <input type="number" name="physical_headcount" class="form-control" placeholder="Enter physical count from register">
                            <small class="text-muted">This helps prevent ghost student claims</small>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.status-select').change(function() {
            var row = $(this).closest('.border-bottom');
            var checkInTime = row.find('.check-in-time');
            var absenceReason = row.find('.absence-reason');

            if ($(this).val() === 'late') {
                checkInTime.show();
                absenceReason.hide();
            } else if ($(this).val() === 'absent' || $(this).val() === 'excused') {
                checkInTime.hide();
                absenceReason.show();
            } else {
                checkInTime.hide();
                absenceReason.hide();
            }
        });

        $('.status-select').trigger('change');
    });
</script>
@endsection
