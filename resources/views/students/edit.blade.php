@extends('layouts.main')
@section('title', 'Edit Student')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mdb.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Editing Student: {{ $student->first_name }} {{ $student->last_name }}</h3>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">Please fix the following errors:</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @include('students.form')
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/mdb.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2-multiple').select2({
                    placeholder: 'Select options',
                    width: '100%'
                });
            }

            // Stepper state
            var currentStep = 1;
            var totalSteps = 4;

            // Auto-generate name from firstName, lastName and middleName
            function updateAutoName() {
                var firstName = $('#first_name').val().trim();
                var lastName = $('#last_name').val().trim();
                var middleName = $('#middle_name').val().trim();

                var fullName = '';
                if (firstName) fullName += firstName;
                if (lastName) {
                    if (fullName) fullName += ' ';
                    fullName += lastName;
                }
                if (middleName) {
                    if (fullName) fullName += ' ';
                    fullName += middleName;
                }

                // Update preview and hidden field
                $('#auto_name').val(fullName);
                $('#preview_name').val(fullName || ' ');
            }

            // Bind name input changes
            $('#first_name, #last_name, #middle_name').on('input blur', updateAutoName);

            updateAutoName();

            function showStep(stepNumber) {
                $('.setup-content').hide();
                $('#step-' + stepNumber).show();

                $('.steps-step button').removeClass('btn-indigo').addClass('btn-secondary');
                $('.steps-step button[data-step="step-' + stepNumber + '"]').removeClass('btn-secondary').addClass(
                    'btn-indigo');

                currentStep = stepNumber;
            }

            showStep(1);

            // Step button clicks
            $('.steps-step button').click(function() {
                if (!$(this).is(':disabled')) {
                    var step = $(this).data('step');
                    var stepNumber = parseInt(step.split('-')[1]);
                    showStep(stepNumber);
                }
            });

            // Next button
            $('.nextBtn').click(function() {
                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        showStep(currentStep + 1);
                    }
                }
            });

            // Previous button
            $('.prevBtn').click(function() {
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            });
            // Validation function for each step
            function validateStep(stepNumber) {
                var isValid = true;
                var stepElement = $('#step-' + stepNumber);

                if (stepElement.length === 0) return true;

                // Clear previous errors
                stepElement.find('.has-error').removeClass('has-error');
                stepElement.find('.error-text').remove();


                stepElement.find('input[required], select[required], textarea[required]').each(function() {
                    var field = $(this);
                    var value = field.val();
                    var isSelect2 = field.hasClass('select2-multiple');

                    // Skip file inputs - handled separately
                    if (field.attr('type') === 'file') return true;

                    var hasValue = isSelect2 ?
                        (field.select2('data') && field.select2('data').length > 0) :
                        (value && value.toString().trim() !== '');

                    if (!hasValue) {
                        markFieldError(field, 'This field is required');
                        isValid = false;
                    }
                });

                // Validate file inputs (only if they have required attribute)
                stepElement.find('input[type="file"][required]').each(function() {
                    var field = $(this);
                    if (field[0].files.length === 0) {
                        markFieldError(field, 'Please upload a file');
                        isValid = false;
                    } else if (field[0].files[0].size > 2 * 1024 * 1024) {
                        markFieldError(field, 'File size must be less than 2MB');
                        isValid = false;
                    }
                });

                // Special validation for Step 1: Email format and name generation
                if (stepNumber === 1) {
                    var emailField = stepElement.find('input[name="email"]');
                    var emailValue = emailField.val().trim();
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (emailValue && !emailRegex.test(emailValue)) {
                        markFieldError(emailField, 'Please enter a valid email address');
                        isValid = false;
                    }


                    if (!$('#auto_name').val().trim()) {
                        markFieldError($('#first_name'), 'Please fill in First Name');
                        markFieldError($('#last_name'), 'Please fill in Last Name');
                        isValid = false;
                    }
                }


                if (!isValid) {
                    var $firstError = stepElement.find('.has-error').first();
                    if ($firstError.length && $firstError.offset()) {
                        $('html, body').animate({
                            scrollTop: $firstError.offset().top - 100
                        }, 500);
                    }
                }

                return isValid;
            }


            function markFieldError(field, message) {
                var container = field.closest(
                    '.mb-3, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-6, .col-md-12, .form-group');
                container.addClass('has-error');
                if (field.siblings('.error-text').length === 0) {
                    field.after('<div class="error-text text-danger small mt-1">' + message + '</div>');
                }
            }


            $('#has_health_issues').change(function() {
                $('#health-details').toggle($(this).val() === '1');
            });


            $('#has_disciplinary_issues').change(function() {
                $('#discipline-details').toggle($(this).val() === '1');
            });


            let academicIndex =
                {{ count(old('academic_history', $student->academicHistories ?? [])) > 0 ? count(old('academic_history', $student->academicHistories ?? [])) : 1 }};

            // Add academic history block
            $(document).on('click', '#add-academic', function() {
                // Remove ID from existing button to prevent duplicate IDs
                $(this).removeAttr('id');
                $(this).removeClass('btn-outline-primary').addClass('btn-outline-danger remove-academic');
                $(this).html('<i class="fas fa-times me-1"></i> Remove');

                const html = `
            <div class="card mb-3 academic-block">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="required-field">Academic Level</label>
                            <select name="academic_history[${academicIndex}][academic_level]" class="form-select academic-level-select" required>
                                <option value="">-- Select --</option>
                                @foreach (array_column(\App\Helpers\AcademicLevel::cases(), 'value') as $lvl)
                                    <option value="{{ $lvl }}">{{ $lvl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="required-field">School Name</label>
                            <input type="text" name="academic_history[${academicIndex}][school_name]" class="form-control" placeholder="School" required>
                        </div>
                        <div class="col-md-2">
                            <label class="required-field">From Year</label>
                            <input type="number" name="academic_history[${academicIndex}][from_year]" class="form-control" placeholder="From YYYY" required>
                        </div>
                        <div class="col-md-2">
                            <label class="required-field">To Year</label>
                            <input type="number" name="academic_history[${academicIndex}][to_year]" class="form-control" placeholder="To YYYY" required>
                        </div>
                        <div class="col-md-1">
                            <label class="required-field">Agg Score</label>
                            <input type="text" name="academic_history[${academicIndex}][aggregate_score]" class="form-control" placeholder="Agg" required>
                        </div>
                        <div class="col-md-1">
                            <label>Grade</label>
                            <input type="text" name="academic_history[${academicIndex}][grade]" class="form-control" placeholder="Grade">
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-academic">
                                <i class="fas fa-plus me-1"></i> Add More
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label>Repeated class?</label>
                            <select name="academic_history[${academicIndex}][repeat_class]" class="form-select repeat-toggle">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-3 repeated-class-field" style="display:none;">
                            <label>Repeated class</label>
                            <input type="text" name="academic_history[${academicIndex}][repeated_class]" class="form-control" placeholder="eg: S.1">
                        </div>
                        <div class="col-md-3">
                            <label>Skipped class?</label>
                            <select name="academic_history[${academicIndex}][skip_class]" class="form-select skip-toggle">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-3 skipped-class-field" style="display:none;">
                            <label>Skipped class</label>
                            <input type="text" name="academic_history[${academicIndex}][skipped_class]" class="form-control" placeholder="eg: S.1">
                        </div>
                    </div>

                    <div class="row mt-3 file-row">
                        <div class="col-md-4 file-group ple-file" style="display:none;">
                            <label>PLE File</label>
                            <input type="file" name="academic_history[${academicIndex}][ple_file]" class="form-control">
                        </div>
                        <div class="col-md-4 file-group o-level-file" style="display:none;">
                            <label>O-Level File</label>
                            <input type="file" name="academic_history[${academicIndex}][o_level_file]" class="form-control">
                        </div>
                        <div class="col-md-4 file-group other-file" style="display:none;">
                            <label>Other File</label>
                            <input type="file" name="academic_history[${academicIndex}][other_file]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>`;

                $('#academic-history-wrapper').append(html);
                academicIndex++;
            });


            $(document).on('click', '.remove-academic', function() {
                var $block = $(this).closest('.academic-block');


                if ($('.academic-block').length === 1) {
                    $block.find('input, select').val('');
                    $block.find('.file-row .file-group').hide();
                } else {
                    $block.remove();
                }


                if ($('#add-academic').length === 0) {
                    $('.academic-block').first().find('.remove-academic')
                        .removeClass('btn-outline-danger remove-academic')
                        .addClass('btn-outline-primary')
                        .attr('id', 'add-academic')
                        .html('<i class="fas fa-plus me-1"></i> Add More');
                }
            });


            $(document).on('change', '.academic-level-select', function() {
                var $block = $(this).closest('.academic-block');
                var level = $(this).val();


                $block.find('.file-group').hide().find('input').prop('required', false);

                if (level) {
                    switch (level) {
                        case 'PLE':
                            $block.find('.ple-file').show();
                            break;
                        case 'O Level':
                            $block.find('.o-level-file').show();
                            break;
                        case 'A Level':
                            $block.find('.o-level-file, .other-file').show();
                            break;
                        default:
                            $block.find('.other-file').show();
                    }
                }
            });


            $(document).on('change', '.repeat-toggle', function() {
                var $block = $(this).closest('.academic-block');
                var show = $(this).val() === '1';
                $block.find('.repeated-class-field').toggle(show);
                $block.find('.repeated-class-field input').prop('required', show);
            });


            $(document).on('change', '.skip-toggle', function() {
                var $block = $(this).closest('.academic-block');
                var show = $(this).val() === '1';
                $block.find('.skipped-class-field').toggle(show);
                $block.find('.skipped-class-field input').prop('required', show);
            });


            $('#studentForm').on('submit', function(e) {
                e.preventDefault();

                // Validate all steps
                let firstInvalid = null;
                for (let i = 1; i <= totalSteps; i++) {
                    if (!validateStep(i) && firstInvalid === null) {
                        firstInvalid = i;
                    }
                }

                if (firstInvalid !== null) {
                    showStep(firstInvalid);
                    var $errorElement = $('#step-' + firstInvalid).find('.has-error').first();
                    if ($errorElement.length && $errorElement.offset()) {
                        $('html, body').animate({
                            scrollTop: $errorElement.offset().top - 100
                        }, 500);
                    }
                    return false;
                }


                this.submit();
            });


            $('.academic-block').each(function() {
                var $block = $(this);
                var level = $block.find('.academic-level-select').val();

                if (level) {
                    $block.find('.academic-level-select').trigger('change');
                }


                if ($block.find('.repeat-toggle').val() === '1') {
                    $block.find('.repeated-class-field').show();
                }
                if ($block.find('.skip-toggle').val() === '1') {
                    $block.find('.skipped-class-field').show();
                }
            });


            $('#has_health_issues').trigger('change');
            $('#has_disciplinary_issues').trigger('change');

        });
    </script>
@endsection
