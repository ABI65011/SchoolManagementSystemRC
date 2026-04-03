@extends('layouts.main')

@section('title', 'Edit Exam - ' . $exam->name)
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Exam: {{ $exam->name }}
                        </h3>
                    </div>
                    <form action="{{ route('exams.update', $exam) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Exam Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $exam->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Exam Code</label>
                                        <input type="text" name="code" id="code"
                                            class="form-control @error('code') is-invalid @enderror"
                                            value="{{ old('code', $exam->code) }}">
                                        @error('code')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Category and Class -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exam_category_id">Exam Category</label>
                                        <select name="exam_category_id" id="exam_category_id"
                                            class="form-control select2 @error('exam_category_id') is-invalid @enderror">
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $groupName => $groupCategories)
                                                <optgroup label="{{ $groupName }}">
                                                    @foreach ($groupCategories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('exam_category_id', $exam->exam_category_id) == $category->id ? 'selected' : '' }}>
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
                                        <select name="class_id" id="class_id"
                                            class="form-control select2 @error('class_id') is-invalid @enderror" required>
                                            <option value="">Select Class</option>
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}"
                                                    {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
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
                                        <select name="term" id="term"
                                            class="form-control @error('term') is-invalid @enderror" required>
                                            @foreach ($terms as $term)
                                                <option value="{{ $term->value }}"
                                                    {{ old('term', $exam->term->value) == $term->value ? 'selected' : '' }}>
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
                                        <select name="year" id="year"
                                            class="form-control @error('year') is-invalid @enderror" required>
                                            @foreach (range(now()->year - 2, now()->year + 2) as $year)
                                                <option value="{{ $year }}"
                                                    {{ old('year', $exam->year) == $year ? 'selected' : '' }}>
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exam_date">Exam Date</label>
                                        <input type="date" name="exam_date" id="exam_date"
                                            class="form-control @error('exam_date') is-invalid @enderror"
                                            value="{{ old('exam_date', $exam->exam_date?->format('Y-m-d')) }}">
                                        @error('exam_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="entry_start_date">Entry Start Date</label>
                                        <input type="date" name="entry_start_date" id="entry_start_date"
                                            class="form-control @error('entry_start_date') is-invalid @enderror"
                                            value="{{ old('entry_start_date', $exam->entry_start_date?->format('Y-m-d')) }}">
                                        @error('entry_start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="entry_end_date">Entry End Date</label>
                                        <input type="date" name="entry_end_date" id="entry_end_date"
                                            class="form-control @error('entry_end_date') is-invalid @enderror"
                                            value="{{ old('entry_end_date', $exam->entry_end_date?->format('Y-m-d')) }}">
                                        @error('entry_end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="result_release_date">Result Release Date</label>
                                        <input type="date" name="result_release_date" id="result_release_date"
                                            class="form-control @error('result_release_date') is-invalid @enderror"
                                            value="{{ old('result_release_date', $exam->result_release_date?->format('Y-m-d')) }}">
                                        @error('result_release_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_mark">Maximum Mark <span class="text-danger">*</span></label>
                                        <input type="number" name="max_mark" id="max_mark"
                                            class="form-control @error('max_mark') is-invalid @enderror"
                                            value="{{ old('max_mark', $exam->max_mark) }}" min="1" max="500"
                                            required>
                                        @error('max_mark')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="weight">Weight</label>
                                        <input type="number" name="weight" id="weight"
                                            class="form-control @error('weight') is-invalid @enderror"
                                            value="{{ old('weight', $exam->weight) }}" min="0" max="10"
                                            step="0.5">
                                        @error('weight')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Add this in the form, preferably after the weight field --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status"
                                            class="form-control @error('status') is-invalid @enderror" required>
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->value }}"
                                                    {{ old('status', $exam->status->value) == $status->value ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $status->value)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active">Active Status</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_active"
                                                name="is_active" value="1"
                                                {{ old('is_active', $exam->is_active) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input"
                                                id="requires_continuous_assessment" name="requires_continuous_assessment"
                                                value="1"
                                                {{ old('requires_continuous_assessment', $exam->requires_continuous_assessment) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="requires_continuous_assessment">
                                                Requires Continuous Assessment (20% component)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="grading_scale_id">Grading Scale</label>
                                        <select name="grading_scale_id" id="grading_scale_id"
                                            class="form-control select2">
                                            <option value="">Use Category/Default</option>
                                            @foreach ($gradingScales as $scale)
                                                <option value="{{ $scale->id }}"
                                                    {{ old('grading_scale_id', $exam->grading_scale_id) == $scale->id ? 'selected' : '' }}>
                                                    {{ $scale->name->value ?? $scale->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Description and Instructions -->
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" rows="2"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $exam->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="instructions">Instructions</label>
                                <textarea name="instructions" id="instructions" rows="3"
                                    class="form-control @error('instructions') is-invalid @enderror">{{ old('instructions', $exam->instructions) }}</textarea>
                                @error('instructions')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Update Exam
                            </button>
                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-info ml-2">
                                <i class="fas fa-eye mr-2"></i>
                                View
                            </a>
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
        });
    </script>
@endsection
