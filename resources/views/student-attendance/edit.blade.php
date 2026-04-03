@extends('layouts.main')

@section('title', 'Edit Attendance Record')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Attendance Record
                    </h3>
                </div>
                <form action="{{ route('student-attendance.update', $attendance) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Student</label>
                                    <input type="text" class="form-control" value="{{ $attendance->student->full_name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Class</label>
                                    <input type="text" class="form-control" value="{{ $attendance->class->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="text" class="form-control" value="{{ $attendance->attendance_date->format('d M Y') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Sponsorship</label>
                            <input type="text" class="form-control" value="{{ $attendance->sponsorship_label }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ $attendance->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ $attendance->status == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="excused" {{ $attendance->status == 'excused' ? 'selected' : '' }}>Excused</option>
                                <option value="holiday" {{ $attendance->status == 'holiday' ? 'selected' : '' }}>Holiday</option>
                            </select>
                        </div>

                        <div class="form-group" id="checkInGroup" style="display: {{ $attendance->status == 'late' ? 'block' : 'none' }};">
                            <label for="check_in_time">Check In Time</label>
                            <input type="time" name="check_in_time" id="check_in_time" class="form-control"
                                   value="{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '' }}">
                        </div>

                        <div class="form-group" id="reasonGroup" style="display: {{ in_array($attendance->status, ['absent', 'excused']) ? 'block' : 'none' }};">
                            <label for="absence_reason">Reason for Absence</label>
                            <input type="text" name="absence_reason" id="absence_reason" class="form-control"
                                   value="{{ $attendance->absence_reason }}">
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="2" class="form-control">{{ $attendance->remarks }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="{{ route('student-attendance.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#status').change(function() {
            if ($(this).val() === 'late') {
                $('#checkInGroup').show();
                $('#reasonGroup').hide();
            } else if ($(this).val() === 'absent' || $(this).val() === 'excused') {
                $('#checkInGroup').hide();
                $('#reasonGroup').show();
            } else {
                $('#checkInGroup').hide();
                $('#reasonGroup').hide();
            }
        });
    });
</script>
@endsection
