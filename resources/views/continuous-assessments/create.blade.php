@extends('layouts.main')

@section('title', 'Record Continuous Assessment')
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
                            Record Continuous Assessment
                        </h3>
                        <div class="card-tools">
                            <span class="badge text-bg-info">20% UNEB Component</span>
                        </div>
                    </div>
                    <form action="{{ route('continuous-assessments.store') }}" method="POST">
                        @csrf

                        <div class="card-body">
                            <!-- Student Selection -->
                            <div class="form-group">
                                <label for="student_id">Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id"
                                    class="form-control select2 @error('student_id') is-invalid @enderror" required>
                                    <option value="">Select Student</option>
                                    @foreach ($classes as $class)
                                        @if ($class->currentStudents->count() > 0)
                                            <optgroup
                                                label="{{ $class->name }} ({{ $class->currentStudents->count() }} students)"
                                                data-class-id="{{ $class->id }}">
                                                @foreach ($class->currentStudents as $student)
                                                    <option value="{{ $student->id }}" data-class-id="{{ $class->id }}"
                                                        data-stream="{{ $student->pivot->stream ?? '' }}"
                                                        {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                        {{ $student->full_name }} ({{ $student->admission_number }})
                                                        @if (!empty($student->pivot->stream))
                                                            - Stream {{ $student->pivot->stream }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Class (auto-filled) -->
                            <input type="hidden" name="class_id" id="class_id" value="{{ old('class_id') }}">

                            <!-- Subject and Assessment Type -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="subject_id">Subject <span class="text-danger">*</span></label>
                                        <select name="subject_id" id="subject_id"
                                            class="form-control select2 @error('subject_id') is-invalid @enderror" required>
                                            <option value="">Select Subject</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}"
                                                    {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                        <label for="assessment_type_id">Assessment Type <span
                                                class="text-danger">*</span></label>
                                        <select name="assessment_type_id" id="assessment_type_id"
                                            class="form-control select2 @error('assessment_type_id') is-invalid @enderror"
                                            required>
                                            <option value="">Select Type</option>
                                            @foreach ($assessmentTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('assessment_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name->value }} ({{ $type->default_weight }}%)
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
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                    placeholder="e.g., Project 1: Ecosystems, End of Topic Test">
                                @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Term and Year -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="term">Term <span class="text-danger">*</span></label>
                                        <select name="term" id="term"
                                            class="form-control @error('term') is-invalid @enderror" required>
                                            <option value="">Select Term</option>
                                            @foreach ($terms as $term)
                                                <option value="{{ $term->value }}"
                                                    {{ old('term') == $term->value ? 'selected' : '' }}>
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
                                        <select name="year" id="year"
                                            class="form-control @error('year') is-invalid @enderror" required>
                                            <option value="">Select Year</option>
                                            @foreach (range(now()->year - 2, now()->year + 2) as $year)
                                                <option value="{{ $year }}"
                                                    {{ old('year', now()->year) == $year ? 'selected' : '' }}>
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
                                        <input type="number" name="max_score" id="max_score"
                                            class="form-control @error('max_score') is-invalid @enderror"
                                            value="{{ old('max_score', 100) }}" min="1" max="100" required>
                                        @error('max_score')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Score -->
                            <div class="form-group">
                                <label for="raw_score">Raw Score <span class="text-danger">*</span></label>
                                <input type="number" name="raw_score" id="raw_score"
                                    class="form-control @error('raw_score') is-invalid @enderror"
                                    value="{{ old('raw_score') }}" min="0" max="100" step="0.01"
                                    required>
                                @error('raw_score')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Enter the student's actual score out of the maximum
                                    score</small>
                            </div>

                            <!-- Live Percentage Preview -->
                            <div class="form-group">
                                <label>Percentage Score</label>
                                <div class="input-group">
                                    <input type="text" id="percentage_preview" class="form-control bg-light" readonly
                                        value="0%">
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
                                <textarea name="teacher_comment" id="teacher_comment" rows="3"
                                    class="form-control @error('teacher_comment') is-invalid @enderror"
                                    placeholder="Optional feedback for this assessment...">{{ old('teacher_comment') }}</textarea>
                                @error('teacher_comment')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Info Alert -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Note:</strong> This assessment will contribute to the student's 20% continuous
                                assessment component for UNEB.
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Save Assessment
                            </button>
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
                placeholder: "Select student",
                allowClear: true
            });


            $('#student_id').change(function() {
                var selected = $(this).find(':selected');
                var classId = selected.data('class-id');

                console.log('Selected student:', selected.val(), 'Class ID:', classId);


                if (classId) {
                    $('#class_id').val(classId);
                } else {

                    var optgroup = selected.closest('optgroup');
                    if (optgroup.length) {

                        var optgroupClassId = optgroup.data('class-id');
                        if (optgroupClassId) {
                            $('#class_id').val(optgroupClassId);
                        }
                    }
                }
            });


            if ($('#student_id').val()) {
                $('#student_id').trigger('change');
            }

            
            $('form').on('submit', function() {
                console.log('Submitting with class_id:', $('#class_id').val());
            });
        });
    </script>
@endsection
