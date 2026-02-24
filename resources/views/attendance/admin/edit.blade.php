@extends('layouts.main')

@section('title', $attendance->exists ? 'Edit Attendance' : 'Manual Attendance Entry')

@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas {{ $attendance->exists ? 'fa-edit' : 'fa-plus-circle' }} mr-2"></i>
                        {{ $attendance->exists ? 'Edit Attendance Record' : 'Manual Attendance Entry' }}
                    </h3>
                </div>
                <form action="{{ $attendance->exists ? route('admin.attendance.update', $attendance) : route('admin.attendance.store') }}" method="POST">
                    @csrf
                    @if($attendance->exists)
                        @method('PUT')
                    @endif

                    <div class="card-body">
                        <!-- Staff Selection -->
                        <div class="form-group">
                            <label for="staff_id">Staff Member <span class="text-danger">*</span></label>
                            <select name="staff_id" id="staff_id" class="form-control select2 @error('staff_id') is-invalid @enderror" required>
                                <option value="">Select Staff</option>
                                @foreach($staffList as $staff)
                                    <option value="{{ $staff->id }}" {{ old('staff_id', $attendance->staff_id) == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="attendance_date">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="attendance_date" id="attendance_date"
                                        class="form-control @error('attendance_date') is-invalid @enderror"
                                        value="{{ old('attendance_date', $attendance->attendance_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                        required>
                                    @error('attendance_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', $attendance->status) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_in">Check In Time</label>
                                    <input type="datetime-local" name="check_in" id="check_in"
                                        class="form-control @error('check_in') is-invalid @enderror"
                                        value="{{ old('check_in', $attendance->check_in?->format('Y-m-d\TH:i')) }}">
                                    @error('check_in')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty if absent</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_out">Check Out Time</label>
                                    <input type="datetime-local" name="check_out" id="check_out"
                                        class="form-control @error('check_out') is-invalid @enderror"
                                        value="{{ old('check_out', $attendance->check_out?->format('Y-m-d\TH:i')) }}">
                                    @error('check_out')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty if not checked out</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="check_in_method">Check In Method</label>
                            <select name="check_in_method" id="check_in_method" class="form-control @error('check_in_method') is-invalid @enderror">
                                @foreach($methods as $value => $label)
                                    <option value="{{ $value }}" {{ old('check_in_method', $attendance->check_in_method ?? 'manual') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('check_in_method')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="check_out_method">Check Out Method</label>
                            <select name="check_out_method" id="check_out_method" class="form-control @error('check_out_method') is-invalid @enderror">
                                @foreach($methods as $value => $label)
                                    <option value="{{ $value }}" {{ old('check_out_method', $attendance->check_out_method ?? 'manual') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('check_out_method')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Optional notes about this attendance record...">{{ old('notes', $attendance->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> Working hours, late minutes, and early departure will be automatically calculated when you save.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            {{ $attendance->exists ? 'Update Record' : 'Save Record' }}
                        </button>
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-default ml-2">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('javascript')
<script src="{{ asset('js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select staff member",
            allowClear: true
        });
    });
</script>
@endsection
