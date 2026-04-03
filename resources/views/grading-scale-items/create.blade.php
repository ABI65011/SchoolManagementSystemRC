@extends('layouts.main')

@section('title', 'Add Grade Item - ' . $gradingScale->name->value)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Add Grade Item to {{ $gradingScale->name->value }}
                        </h3>
                        <div class="card-tools">
                            <span class="badge text-bg-info">{{ str_replace('_', ' ', $gradingScale->type->value) }}</span>
                        </div>
                    </div>
                    <form action="{{ route('grading-scale-items.store', $gradingScale) }}" method="POST">
                        @csrf

                        <div class="card-body">
                            <!-- Grade Code -->
                            <div class="form-group">
                                <label for="grade_code">Grade Code <span class="text-danger">*</span></label>
                                <select name="grade_code" id="grade_code"
                                    class="form-control @error('grade_code') is-invalid @enderror" required>
                                    <option value="">Select Grade Code</option>
                                    @foreach ($availableGrades as $grade)
                                        <option value="{{ $grade }}"
                                            {{ old('grade_code') == $grade ? 'selected' : '' }}>
                                            {{ $grade }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('grade_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Select the grade code for this range</small>
                            </div>

                            <!-- Mark Range -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="min_mark">Minimum Mark <span class="text-danger">*</span></label>
                                        <input type="number" name="min_mark" id="min_mark"
                                            class="form-control @error('min_mark') is-invalid @enderror"
                                            value="{{ old('min_mark') }}" min="0" max="100" required>
                                        @error('min_mark')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_mark">Maximum Mark <span class="text-danger">*</span></label>
                                        <input type="number" name="max_mark" id="max_mark"
                                            class="form-control @error('max_mark') is-invalid @enderror"
                                            value="{{ old('max_mark') }}" min="0" max="100" required>
                                        @error('max_mark')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Achievement Level -->
                            <div class="form-group">
                                <label for="achievement_level">Achievement Level</label>
                                <select name="achievement_level" id="achievement_level"
                                    class="form-control @error('achievement_level') is-invalid @enderror">
                                    <option value="">Select Achievement Level (Optional)</option>
                                    @foreach (\App\Helpers\AchievementLevel::cases() as $level)
                                        <option value="{{ $level->value }}"
                                            {{ old('achievement_level') == $level->value ? 'selected' : '' }}>
                                            {{ $level->value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('achievement_level')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Descriptor -->
                            <div class="form-group">
                                <label for="descriptor">Descriptor</label>
                                <input type="text" name="descriptor" id="descriptor"
                                    class="form-control @error('descriptor') is-invalid @enderror"
                                    value="{{ old('descriptor') }}"
                                    placeholder="e.g., Distinction One, Achieved Excellence">
                                @error('descriptor')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Descriptive text for this grade (appears on report
                                    cards)</small>
                            </div>

                            <!-- Points -->
                            <div class="form-group">
                                <label for="points">Points</label>
                                <input type="number" name="points" id="points"
                                    class="form-control @error('points') is-invalid @enderror" value="{{ old('points') }}"
                                    min="0" step="1">
                                @error('points')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">For UACE scales (A=6, B=5, etc.)</small>
                            </div>

                            <!-- Order -->
                            <input type="hidden" name="order" value="{{ $nextOrder }}">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Note:</strong> Grade ranges should not overlap. They will be ordered from highest to
                                lowest.
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Add Grade Item
                            </button>
                            <a href="{{ route('grading-scales.show', $gradingScale) }}" class="btn btn-default ml-2">
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
    <script>
        $(document).ready(function() {

            $('#min_mark, #max_mark').on('input', function() {
                var min = parseInt($('#min_mark').val()) || 0;
                var max = parseInt($('#max_mark').val()) || 0;

                if (min > max) {
                    $('#max_mark').addClass('is-invalid');
                    toastr.warning('Maximum mark must be greater than minimum mark');
                } else {
                    $('#max_mark').removeClass('is-invalid');
                }
            });

            
            $('#grade_code').change(function() {
                var gradeCode = $(this).val();
                var descriptorField = $('#descriptor');

                if (!descriptorField.val()) {
                    var suggestions = {
                        'A': 'Achieved Excellence',
                        'B': 'Achieved Above Standard',
                        'C': 'Achieved Standard',
                        'D': 'Achieved Basic Competency',
                        'E': 'Below Standard',
                        'U': 'Ungraded',
                        'D1': 'Distinction One',
                        'D2': 'Distinction Two',
                        'C3': 'Credit Three',
                        'C4': 'Credit Four',
                        'C5': 'Credit Five',
                        'C6': 'Credit Six',
                        'P7': 'Pass Seven',
                        'P8': 'Pass Eight',
                        'F9': 'Fail Nine'
                    };

                    if (suggestions[gradeCode]) {
                        descriptorField.val(suggestions[gradeCode]);
                    }
                }
            });
        });
    </script>
@endsection
