@extends('layouts.main')

@section('title', 'Generate Report Card')
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
                        Generate Report Card
                    </h3>
                </div>
                <form action="{{ route('report-cards.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <!-- Student Selection -->
                        <div class="form-group">
                            <label for="student_id">Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="student_id" class="form-control select2 @error('student_id') is-invalid @enderror" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->full_name }}  - {{ $student->current_class_name ?? 'No Class' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Term and Year -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="term">Term <span class="text-danger">*</span></label>
                                    <select name="term" id="term" class="form-control @error('term') is-invalid @enderror" required>
                                        <option value="">Select Term</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->value }}" {{ old('term') == $term->value ? 'selected' : '' }}>
                                                Term {{ $term->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('term')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year">Year <span class="text-danger">*</span></label>
                                    <select name="year" id="year" class="form-control @error('year') is-invalid @enderror" required>
                                        <option value="">Select Year</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ old('year', now()->year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('year')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> The report card will be generated based on all completed exams for the selected term and year.
                            Make sure all results have been entered before generating.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Generate Report Card
                        </button>
                        <a href="{{ route('report-cards.index') }}" class="btn btn-default ml-2">
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
            placeholder: "Select student",
            allowClear: true
        });
    });
</script>
@endsection
