@extends('layouts.main')

@section('title', 'Select Class for Attendance')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Take Attendance
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('student-attendance.bulk') }}" method="GET">
                            <div class="form-group">
                                <label for="class_id">Select Class <span class="text-danger">*</span></label>
                                <select name="class_id" id="class_id" class="form-control select2" required>
                                    <option value="">-- Select Class --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" name="date" id="date" class="form-control"
                                    value="{{ now()->toDateString() }}">
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                You will be able to mark attendance for all students in the selected class.
                                Physical headcount verification will be required for MoES compliance.
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Proceed to Attendance
                            </button>
                        </form>
                    </div>
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
                placeholder: "Select class",
                allowClear: true
            });
        });
    </script>
@endsection
