@extends('layouts.main')

@section('title', 'Create Exam Category')
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
                        Create Exam Category
                    </h3>
                </div>
                <form action="{{ route('exam-categories.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <!-- Category Name -->
                        <div class="form-group">
                            <label for="name">Category Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., End of Term, Mock, Quiz"
                                   required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Main Category (Parent) -->
                        <div class="form-group">
                            <label for="main_category_id">Main Category</label>
                            <select name="main_category_id" id="main_category_id" class="form-control select2 @error('main_category_id') is-invalid @enderror">
                                <option value="">— None (Top Level Category) —</option>
                                @foreach($mainCategories as $mainCategory)
                                    <option value="{{ $mainCategory->id }}" {{ old('main_category_id') == $mainCategory->id ? 'selected' : '' }}>
                                        {{ $mainCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('main_category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Select a main category if this is a sub-category (e.g., "Mock" under "External Exams")
                            </small>
                        </div>

                        <!-- Exam Type -->
                        <div class="form-group">
                            <label for="exam_type">Exam Type <span class="text-danger">*</span></label>
                            <select name="exam_type" id="exam_type" class="form-control @error('exam_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($examTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('exam_type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('exam_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description"
                                      id="description"
                                      rows="2"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Brief description of this category">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Default Grading Scale -->
                        <div class="form-group">
                            <label for="grading_scale_id">Default Grading Scale</label>
                            <select name="grading_scale_id" id="grading_scale_id" class="form-control select2 @error('grading_scale_id') is-invalid @enderror">
                                <option value="">— Use School Default —</option>
                                @foreach($gradingScales as $scale)
                                    <option value="{{ $scale->id }}" {{ old('grading_scale_id') == $scale->id ? 'selected' : '' }}>
                                        {{ $scale->name->value ?? $scale->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grading_scale_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Grading scale for exams in this category</small>
                        </div>

                        <!-- Settings Row -->
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
                                            Requires Continuous Assessment
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Includes 20% CA component</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Weight -->
                        <div class="form-group">
                            <label for="weight">Weight (Multiplier)</label>
                            <input type="number"
                                   name="weight"
                                   id="weight"
                                   class="form-control @error('weight') is-invalid @enderror"
                                   value="{{ old('weight', 1.00) }}"
                                   min="0"
                                   max="10"
                                   step="0.1">
                            @error('weight')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Weight in final grade calculation (default: 1.0)</small>
                        </div>

                        <!-- Sort Order -->
                        <div class="form-group">
                            <label for="sort_order">Sort Order</label>
                            <input type="number"
                                   name="sort_order"
                                   id="sort_order"
                                   class="form-control @error('sort_order') is-invalid @enderror"
                                   value="{{ old('sort_order', 0) }}"
                                   min="0">
                            @error('sort_order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Lower numbers appear first</small>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> Categories help organize exams (e.g., Internal > End of Term, External > Mock).
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Create Category
                        </button>
                        <a href="{{ route('exam-categories.index') }}" class="btn btn-default ml-2">
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

        
        $('#name').on('keyup', function() {
            var name = $(this).val();
            if (name && !$('#description').val()) {
                $('#description').attr('placeholder', 'Description for ' + name + ' category');
            }
        });
    });
</script>
@endsection
