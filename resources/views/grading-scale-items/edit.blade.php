@extends('layouts.main')

@section('title', 'Edit Grade Item - ' . $gradingScaleItem->grade_code)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Grade Item: {{ $gradingScaleItem->grade_code }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge text-bg-info">{{ $gradingScale->name->value ?? $gradingScale->name }}</span>
                    </div>
                </div>
                <form action="{{ route('grading-scale-items.update', $gradingScaleItem) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <!-- Grade Code -->
                        <div class="form-group">
                            <label for="grade_code">Grade Code <span class="text-danger">*</span></label>
                            <select name="grade_code" id="grade_code" class="form-control @error('grade_code') is-invalid @enderror" required>
                                <option value="">Select Grade Code</option>
                                @foreach($availableGrades as $grade)
                                    <option value="{{ $grade }}" {{ old('grade_code', $gradingScaleItem->grade_code) == $grade ? 'selected' : '' }}>
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
                                    <input type="number"
                                           name="min_mark"
                                           id="min_mark"
                                           class="form-control @error('min_mark') is-invalid @enderror"
                                           value="{{ old('min_mark', $gradingScaleItem->min_mark) }}"
                                           min="0"
                                           max="100"
                                           step="1"
                                           required>
                                    @error('min_mark')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_mark">Maximum Mark <span class="text-danger">*</span></label>
                                    <input type="number"
                                           name="max_mark"
                                           id="max_mark"
                                           class="form-control @error('max_mark') is-invalid @enderror"
                                           value="{{ old('max_mark', $gradingScaleItem->max_mark) }}"
                                           min="0"
                                           max="100"
                                           step="1"
                                           required>
                                    @error('max_mark')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Range Preview -->
                        <div class="alert alert-info py-2" id="rangePreview">
                            <i class="fas fa-info-circle mr-2"></i>
                            Range: <span id="minPreview">{{ $gradingScaleItem->min_mark }}</span> - <span id="maxPreview">{{ $gradingScaleItem->max_mark }}</span>
                        </div>

                        <!-- Achievement Level -->
                        <div class="form-group">
                            <label for="achievement_level">Achievement Level</label>
                            <select name="achievement_level" id="achievement_level" class="form-control @error('achievement_level') is-invalid @enderror">
                                <option value="">Select Achievement Level (Optional)</option>
                                @foreach(\App\Helpers\AchievementLevel::cases() as $level)
                                    <option value="{{ $level->value }}" {{ old('achievement_level', $gradingScaleItem->achievement_level) == $level->value ? 'selected' : '' }}>
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
                            <input type="text"
                                   name="descriptor"
                                   id="descriptor"
                                   class="form-control @error('descriptor') is-invalid @enderror"
                                   value="{{ old('descriptor', $gradingScaleItem->descriptor) }}"
                                   placeholder="e.g., Distinction One, Achieved Excellence">
                            @error('descriptor')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Descriptive text for this grade (appears on report cards)</small>
                        </div>

                        <!-- Points -->
                        <div class="form-group">
                            <label for="points">Points</label>
                            <input type="number"
                                   name="points"
                                   id="points"
                                   class="form-control @error('points') is-invalid @enderror"
                                   value="{{ old('points', $gradingScaleItem->points) }}"
                                   min="0"
                                   step="1">
                            @error('points')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">For UACE scales (A=6, B=5, etc.)</small>
                        </div>

                        <!-- Order (hidden) -->
                        <input type="hidden" name="order" value="{{ $gradingScaleItem->order }}">

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Note:</strong> Grade ranges should not overlap with other items in this scale.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Update Grade Item
                        </button>
                        <a href="{{ route('grading-scales.show', $gradingScale) }}" class="btn btn-info ml-2">
                            <i class="fas fa-eye mr-2"></i>
                            View Scale
                        </a>
                        <a href="{{ route('grading-scale-items.index', $gradingScale) }}" class="btn btn-default ml-2">
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

        function updateRangePreview() {
            var min = $('#min_mark').val() || 0;
            var max = $('#max_mark').val() || 0;
            $('#minPreview').text(min);
            $('#maxPreview').text(max);

            if (parseInt(min) > parseInt(max)) {
                $('#rangePreview').removeClass('alert-info').addClass('alert-danger');
            } else {
                $('#rangePreview').removeClass('alert-danger').addClass('alert-info');
            }
        }

        $('#min_mark, #max_mark').on('input', updateRangePreview);


        $('#min_mark, #max_mark').on('change', function() {
            var min = parseInt($('#min_mark').val()) || 0;
            var max = parseInt($('#max_mark').val()) || 0;

            if (min > max) {
                toastr.warning('Minimum mark cannot be greater than maximum mark');
                $('#max_mark').val(min);
                updateRangePreview();
            }
        });


        $('#grade_code').change(function() {
            var gradeCode = $(this).val();
            var descriptorField = $('#descriptor');
            var currentDescriptor = descriptorField.val();


            if (!currentDescriptor || currentDescriptor === getPreviousSuggestion()) {
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
                    storePreviousSuggestion(suggestions[gradeCode]);
                }
            }
        });

        
        function getPreviousSuggestion() {
            return $('#descriptor').data('previous-suggestion');
        }

        function storePreviousSuggestion(suggestion) {
            $('#descriptor').data('previous-suggestion', suggestion);
        }
    });
</script>
@endsection
