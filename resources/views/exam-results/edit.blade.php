@extends('layouts.main')

@section('title', 'Edit Result - ' . $examResult->student->full_name)
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
                        <i class="fas fa-edit mr-2"></i>
                        Edit Exam Result
                    </h3>
                    <div class="card-tools">
                        <span class="badge text-bg-info">{{ $examResult->exam->name }}</span>
                    </div>
                </div>
                <form action="{{ route('exam-results.update', $examResult) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <!-- Student Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Student</label>
                                    <input type="text" class="form-control" value="{{ $examResult->student->full_name }} ({{ $examResult->student->admission_number }})" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Subject</label>
                                    <input type="text" class="form-control" value="{{ $examResult->subject->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Exam Info -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Exam</label>
                                    <input type="text" class="form-control" value="{{ $examResult->exam->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Term/Year</label>
                                    <input type="text" class="form-control" value="Term {{ $examResult->exam->term }}, {{ $examResult->exam->year }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Current Grade</label>
                                    <input type="text" class="form-control" value="{{ $examResult->grade ?? 'Not graded' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Raw Mark -->
                        <div class="form-group">
                            <label for="raw_mark">Raw Mark <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="raw_mark"
                                   id="raw_mark"
                                   class="form-control @error('raw_mark') is-invalid @enderror"
                                   value="{{ old('raw_mark', $examResult->raw_mark) }}"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   required>
                            @error('raw_mark')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Enter mark out of 100</small>
                        </div>

                        <!-- AoI Criteria -->
                        <div class="card card-outline card-info mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-tasks mr-2"></i>
                                    Activity of Integration (AoI) Scores
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($aoiCriteria as $criterion)
                                        @php
                                            $existingScore = $examResult->aoiCriteria
                                                ->where('criterion_code', $criterion->value)
                                                ->first();
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="aoi_{{ $criterion->value }}">{{ ucfirst($criterion->value) }}</label>
                                                <select name="aoi_criteria[{{ $criterion->value }}]"
                                                        id="aoi_{{ $criterion->value }}"
                                                        class="form-control @error('aoi_criteria.'.$criterion->value) is-invalid @enderror">
                                                    <option value="">Not Assessed</option>
                                                    @for($i = 1; $i <= 3; $i++)
                                                        <option value="{{ $i }}" {{ old('aoi_criteria.'.$criterion->value, $existingScore?->score) == $i ? 'selected' : '' }}>
                                                            {{ $i }} - {{ $i == 3 ? 'Exceeds Expectations' : ($i == 2 ? 'Meets Expectations' : 'Below Expectations') }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                @error('aoi_criteria.'.$criterion->value)
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-muted mb-0">
                                    <small>AoI scores are on a scale of 1-3 (1=Below, 2=Meets, 3=Exceeds)</small>
                                </p>
                            </div>
                        </div>

                        <!-- Teacher Remark -->
                        <div class="form-group">
                            <label for="teacher_remark">Teacher Remark</label>
                            <textarea name="teacher_remark"
                                      id="teacher_remark"
                                      rows="3"
                                      class="form-control @error('teacher_remark') is-invalid @enderror"
                                      placeholder="Optional remarks about this student's performance...">{{ old('teacher_remark', $examResult->teacher_remark) }}</textarea>
                            @error('teacher_remark')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Auto-calculated fields -->
                        <div class="card card-outline card-secondary mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Calculated Fields
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>CA Contribution (20%)</label>
                                            <input type="text" class="form-control" value="{{ number_format($examResult->continuous_assessment_contribution, 2) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Final Mark</label>
                                            <input type="text" class="form-control" value="{{ number_format($examResult->final_mark, 2) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Predicted Grade</label>
                                            <input type="text" class="form-control" value="{{ $examResult->grade ?? 'Pending' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Update Result
                        </button>
                        <a href="{{ route('exams.show', $examResult->exam) }}" class="btn btn-default ml-2">
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
            placeholder: "Select option",
            allowClear: true
        });


        $('#raw_mark').on('input', function() {
            var rawMark = parseFloat($(this).val()) || 0;
            var caContribution = {{ $examResult->continuous_assessment_contribution ?? 0 }};
            var finalMark = rawMark * 0.8 + caContribution;


            if (!isNaN(finalMark)) {
                
            }
        });
    });
</script>
@endsection
