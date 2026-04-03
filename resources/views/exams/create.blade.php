@extends('layouts.main')

@section('title', 'Create New Exam')

@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Create New Exam
                    </h3>
                </div>
                <form action="{{ route('exams.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Exam Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           placeholder="e.g., End of Term 1 Examination 2026"
                                           required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Exam Code</label>
                                    <input type="text"
                                           name="code"
                                           id="code"
                                           class="form-control @error('code') is-invalid @enderror"
                                           value="{{ old('code') }}"
                                           placeholder="e.g., EOT1-2026">
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Auto-generated if left empty</small>
                                </div>
                            </div>
                        </div>

                        <!-- Category and Class -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exam_category_id">Exam Category</label>
                                    <select name="exam_category_id" id="exam_category_id" class="form-control select2 @error('exam_category_id') is-invalid @enderror">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $groupName => $groupCategories)
                                            <optgroup label="{{ $groupName }}">
                                                @foreach($groupCategories as $category)
                                                    <option value="{{ $category->id }}" {{ old('exam_category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('exam_category_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class_id">Class <span class="text-danger">*</span></label>
                                    <select name="class_id" id="class_id" class="form-control select2 @error('class_id') is-invalid @enderror" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
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
                                        @foreach(range(now()->year - 2, now()->year + 2) as $year)
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

                        <!-- Dates -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exam_date">Exam Date</label>
                                    <input type="date"
                                           name="exam_date"
                                           id="exam_date"
                                           class="form-control @error('exam_date') is-invalid @enderror"
                                           value="{{ old('exam_date') }}">
                                    @error('exam_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="result_release_date">Result Release Date</label>
                                    <input type="date"
                                           name="result_release_date"
                                           id="result_release_date"
                                           class="form-control @error('result_release_date') is-invalid @enderror"
                                           value="{{ old('result_release_date') }}">
                                    @error('result_release_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Entry Window Dates -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="entry_start_date">Entry Start Date</label>
                                    <input type="date"
                                           name="entry_start_date"
                                           id="entry_start_date"
                                           class="form-control @error('entry_start_date') is-invalid @enderror"
                                           value="{{ old('entry_start_date') }}">
                                    @error('entry_start_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="entry_end_date">Entry End Date</label>
                                    <input type="date"
                                           name="entry_end_date"
                                           id="entry_end_date"
                                           class="form-control @error('entry_end_date') is-invalid @enderror"
                                           value="{{ old('entry_end_date') }}">
                                    @error('entry_end_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Marks and Weight -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_mark">Maximum Mark <span class="text-danger">*</span></label>
                                    <input type="number"
                                           name="max_mark"
                                           id="max_mark"
                                           class="form-control @error('max_mark') is-invalid @enderror"
                                           value="{{ old('max_mark', 100) }}"
                                           min="1"
                                           max="500"
                                           required>
                                    @error('max_mark')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="weight">Weight</label>
                                    <input type="number"
                                           name="weight"
                                           id="weight"
                                           class="form-control @error('weight') is-invalid @enderror"
                                           value="{{ old('weight', 1) }}"
                                           min="0"
                                           max="10"
                                           step="0.5">
                                    @error('weight')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Multiplier for final grade calculation</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="grading_scale_id">Grading Scale</label>
                                    <select name="grading_scale_id" id="grading_scale_id" class="form-control select2 @error('grading_scale_id') is-invalid @enderror">
                                        <option value="">Use Category/Default</option>
                                        @foreach($gradingScales as $scale)
                                            <option value="{{ $scale->id }}" {{ old('grading_scale_id') == $scale->id ? 'selected' : '' }}>
                                                {{ $scale->name }} {{ $scale->is_default ? '(Default)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('grading_scale_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="requires_continuous_assessment"
                                               name="requires_continuous_assessment"
                                               value="1"
                                               {{ old('requires_continuous_assessment', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="requires_continuous_assessment">
                                            Requires Continuous Assessment (20% component)
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Uncheck for Mock/External exams</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->value }}" {{ old('status', 'draft') == $status->value ? 'selected' : '' }}>
                                                {{ ucfirst($status->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Optional description of the exam...">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div class="form-group">
                            <label for="instructions">Instructions</label>
                            <textarea name="instructions" id="instructions" rows="4" class="form-control @error('instructions') is-invalid @enderror" placeholder="Instructions for students...">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> The exam type (Internal/External) and grading scale will be inherited from the selected category.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Create Exam
                        </button>
                        <a href="{{ route('exams.index') }}" class="btn btn-default ml-2">
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

        $('#entry_start_date, #entry_end_date, #exam_date, #result_release_date').change(function() {
            var startDate = $('#entry_start_date').val();
            var endDate = $('#entry_end_date').val();
            var examDate = $('#exam_date').val();
            var resultDate = $('#result_release_date').val();

            if (startDate && endDate && startDate > endDate) {
                alert('Entry end date must be after entry start date');
                $('#entry_end_date').val('');
            }

            if (examDate && resultDate && examDate > resultDate) {
                alert('Result release date must be after exam date');
                $('#result_release_date').val('');
            }
        });
    });
</script>
@endsection
