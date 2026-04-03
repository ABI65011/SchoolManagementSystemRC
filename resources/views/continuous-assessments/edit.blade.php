{{-- resources/views/continuous-assessments/edit.blade.php --}}
@extends('layouts.main')

@section('title', 'Edit Continuous Assessment')
@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<style>
    .score-input {
        width: 120px;
        text-align: center;
        font-weight: bold;
    }
    .score-input.passing {
        background-color: #d4edda;
        border-color: #28a745;
    }
    .score-input.failing {
        background-color: #f8d7da;
        border-color: #dc3545;
    }
    .percentage-badge {
        display: inline-block;
        width: 80px;
        text-align: center;
        padding: 3px 8px;
        border-radius: 3px;
        font-weight: bold;
    }
    .percentage-badge.high {
        background-color: #28a745;
        color: white;
    }
    .percentage-badge.medium {
        background-color: #ffc107;
        color: #856404;
    }
    .percentage-badge.low {
        background-color: #dc3545;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Continuous Assessment
                    </h3>
                    <div class="card-tools">
                        <span class="badge text-bg-info">20% UNEB Component</span>
                    </div>
                </div>
                <form action="{{ route('continuous-assessments.update', $continuousAssessment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <!-- Student Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Student</label>
                                    <input type="text" class="form-control"
                                           value="{{ $continuousAssessment->student->full_name }} ({{ $continuousAssessment->student->admission_number }})"
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Class</label>
                                    <input type="text" class="form-control"
                                           value="{{ $continuousAssessment->class->name }}"
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Subject and Assessment Type -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject_id">Subject <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject_id" class="form-control select2 @error('subject_id') is-invalid @enderror" required>
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id', $continuousAssessment->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="assessment_type_id">Assessment Type <span class="text-danger">*</span></label>
                                    <select name="assessment_type_id" id="assessment_type_id" class="form-control select2 @error('assessment_type_id') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        @foreach($assessmentTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('assessment_type_id', $continuousAssessment->assessment_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name->value ?? $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assessment_type_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="form-group">
                            <label for="title">Assessment Title</label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $continuousAssessment->title) }}"
                                   placeholder="e.g., Project 1: Ecosystems, End of Topic Test">
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Term, Year and Max Score -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="term">Term <span class="text-danger">*</span></label>
                                    <select name="term" id="term" class="form-control @error('term') is-invalid @enderror" required>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->value }}"
                                                {{ old('term', $continuousAssessment->term->value) == $term->value ? 'selected' : '' }}>
                                                Term {{ $term->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('term')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="year">Year <span class="text-danger">*</span></label>
                                    <select name="year" id="year" class="form-control @error('year') is-invalid @enderror" required>
                                        @foreach(range(now()->year - 2, now()->year + 2) as $year)
                                            <option value="{{ $year }}"
                                                {{ old('year', $continuousAssessment->year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('year')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_score">Max Score <span class="text-danger">*</span></label>
                                    <input type="number"
                                           name="max_score"
                                           id="max_score"
                                           class="form-control @error('max_score') is-invalid @enderror"
                                           value="{{ old('max_score', $continuousAssessment->max_score) }}"
                                           min="1"
                                           max="100"
                                           required>
                                    @error('max_score')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Raw Score -->
                        <div class="form-group">
                            <label for="raw_score">Raw Score <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number"
                                       name="raw_score"
                                       id="raw_score"
                                       class="form-control score-input @error('raw_score') is-invalid @enderror"
                                       value="{{ old('raw_score', $continuousAssessment->raw_score) }}"
                                       min="0"
                                       max="{{ $continuousAssessment->max_score }}"
                                       step="0.01"
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">/ {{ $continuousAssessment->max_score }}</span>
                                </div>
                            </div>
                            @error('raw_score')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Enter the student's actual score out of the maximum score</small>
                        </div>

                        <!-- Live Percentage Preview -->
                        <div class="form-group">
                            <label>Percentage Score</label>
                            <div class="input-group">
                                <input type="text" id="percentage_preview" class="form-control bg-light" readonly value="0%">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="percentage_indicator">
                                        <i class="fas fa-circle text-secondary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Teacher Comment -->
                        <div class="form-group">
                            <label for="teacher_comment">Teacher's Comment</label>
                            <textarea name="teacher_comment"
                                      id="teacher_comment"
                                      rows="3"
                                      class="form-control @error('teacher_comment') is-invalid @enderror"
                                      placeholder="Optional feedback for this assessment...">{{ old('teacher_comment', $continuousAssessment->teacher_comment) }}</textarea>
                            @error('teacher_comment')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> This assessment contributes to the student's 20% continuous assessment component for UNEB.
                            The weighted score will be calculated automatically.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Update Assessment
                        </button>
                        <a href="{{ route('continuous-assessments.show', $continuousAssessment) }}" class="btn btn-info ml-2">
                            <i class="fas fa-eye mr-2"></i>
                            View
                        </a>
                        <a href="{{ route('continuous-assessments.index') }}" class="btn btn-default ml-2">
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


        $('#max_score').on('change', function() {
            var maxScore = $(this).val();
            $('#raw_score').attr('max', maxScore);
            updatePercentage();
        });


        function updatePercentage() {
            var rawScore = parseFloat($('#raw_score').val()) || 0;
            var maxScore = parseFloat($('#max_score').val()) || 100;
            var percentage = (rawScore / maxScore) * 100;

            var displayText = percentage.toFixed(2) + '%';
            $('#percentage_preview').val(displayText);


            var indicator = $('#percentage_indicator i');
            if (percentage >= 75) {
                indicator.removeClass('text-secondary text-warning text-danger').addClass('text-success');
            } else if (percentage >= 50) {
                indicator.removeClass('text-secondary text-success text-danger').addClass('text-warning');
            } else if (percentage > 0) {
                indicator.removeClass('text-secondary text-success text-warning').addClass('text-danger');
            } else {
                indicator.removeClass('text-success text-warning text-danger').addClass('text-secondary');
            }


            $('#percentage_preview').removeClass('bg-success bg-warning bg-danger');
            if (percentage >= 70) {
                $('#percentage_preview').addClass('bg-success text-white');
            } else if (percentage >= 50) {
                $('#percentage_preview').addClass('bg-warning');
            } else if (percentage > 0) {
                $('#percentage_preview').addClass('bg-danger text-white');
            }
        }

        
        $('#raw_score, #max_score').on('input', updatePercentage);
        updatePercentage();
    });
</script>
@endsection
