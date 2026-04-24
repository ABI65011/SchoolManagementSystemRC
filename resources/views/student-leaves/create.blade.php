@extends('layouts.main')

@section('title', 'Request Student Leave')
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
                        <i class="fas fa-plus-circle mr-2"></i>
                        Request Student Leave
                    </h3>
                </div>
                <form action="{{ route('student-leaves.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <!-- Student Selection -->
                        <div class="form-group">
                            <label for="student_id">Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="student_id" class="form-control select2 @error('student_id') is-invalid @enderror" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->full_name }} - {{ $student->current_class->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Leave Type -->
                        <div class="form-group">
                            <label for="type">Leave Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Destination -->
                        <div class="form-group">
                            <label for="destination">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" id="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination') }}" placeholder="e.g., Kampala, Home, Hospital" required>
                            @error('destination')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="form-group">
                            <label for="reason">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" rows="2" class="form-control @error('reason') is-invalid @enderror" placeholder="Reason for leave..." required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Date and Time -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departure_time">Departure Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="departure_time" id="departure_time" class="form-control @error('departure_time') is-invalid @enderror" value="{{ old('departure_time') }}" required>
                                    @error('departure_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expected_return_time">Expected Return Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="expected_return_time" id="expected_return_time" class="form-control @error('expected_return_time') is-invalid @enderror" value="{{ old('expected_return_time') }}" required>
                                    @error('expected_return_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> Leave requests require approval from authorized staff. Student must sign out at the gate before departure and sign in upon return.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Request
                        </button>
                        <a href="{{ route('student-leaves.index') }}" class="btn btn-default ml-2">
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
            placeholder: "Select student",
            allowClear: true
        });

        
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        var minDateTime = now.toISOString().slice(0, 16);

        $('#departure_time').attr('min', minDateTime);

        $('#departure_time').change(function() {
            $('#expected_return_time').attr('min', $(this).val());
        });
    });
</script>
@endsection
