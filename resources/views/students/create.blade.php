@extends('layouts.main')

@section('title', 'Register Student')
{{-- @section('plugins.Select2', true) AdminLTE plugin flag --}}
@section('header')
   <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .steps-form {
            display: table;
            width: 100%;
            position: relative;
            margin-bottom: 2rem;
        }

        .steps-form .steps-row {
            display: table-row;
        }

        .steps-form .steps-row:before {
            top: 14px;
            bottom: 0;
            position: absolute;
            content: " ";
            width: 100%;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 0;
        }

        .steps-form .steps-row .steps-step {
            display: table-cell;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .steps-form .steps-row .steps-step p {
            margin-top: 0.5rem;
            font-weight: 500;
            color: #6c757d;
        }

        .steps-form .steps-row .steps-step button[disabled] {
            opacity: 1 !important;
            filter: alpha(opacity=100) !important;
        }

        .steps-form .steps-row .steps-step .btn-circle {
            width: 40px;
            height: 40px;
            text-align: center;
            padding: 6px 0;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.428571429;
            border-radius: 50%;
            margin-top: 0;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        .btn-indigo {
            background-color: #6610f2;
            border-color: #6610f2;
            color: white;
        }

        .btn-indigo:hover {
            background-color: #5a0cd8;
            border-color: #5a0cd8;
            color: white;
        }

        .setup-content {
            padding: 20px;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .academic-block {
            border-left: 4px solid #007bff;
        }

        .footer {
            margin-top: 2rem;
            padding: 1rem 0;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }

        .has-error .form-control,
        .has-error .form-select {
            border-color: #dc3545;
        }

        .has-error .error-text {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .step-title {
            color: #343a40;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .has-error .form-control,
    .has-error .form-select,
    .has-error .select2-selection {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }

    .error-text {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    .steps-step button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .steps-step button.btn-indigo {
        background-color: #6610f2;
        border-color: #6610f2;
    }

    .steps-step button.btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-indigo {
        background-color: #6610f2;
        border-color: #6610f2;
        color: white;
    }

    .btn-indigo:hover {
        background-color: #5a0cd8;
        border-color: #5a0cd8;
    }

    .setup-content {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>

    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mdb.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">

@endsection
@section('content')
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Register Student</h3>
            </div>
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
    $(document).ready(function () {
        // Initialize Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-multiple').select2({
                placeholder: 'Select options',
                width: '100%'
            });
        }

        // Step navigation logic
        var currentStep = 1;
        var totalSteps = 3;

        // Function to show a specific step
        function showStep(stepNumber) {
            // Hide all steps
            $('.setup-content').hide();

            // Show the selected step
            $('#step-' + stepNumber).show();

            // Update step indicators
            $('.steps-step button').removeClass('btn-indigo').addClass('btn-secondary');
            $('.steps-step button[data-step="step-' + stepNumber + '"]').removeClass('btn-secondary').addClass('btn-indigo');

            // Update current step
            currentStep = stepNumber;
        }

        // Initialize first step
        showStep(1);

        // Step button click handlers
        $('.steps-step button').click(function () {
            if (!$(this).is(':disabled')) {
                var step = $(this).data('step');
                var stepNumber = parseInt(step.split('-')[1]);
                showStep(stepNumber);
            }
        });

        // Next button click
        $('.nextBtn').click(function () {
            // Validate current step
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    showStep(currentStep + 1);
                }
            }
        });

        // Previous button click
        $('.prevBtn').click(function () {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        });

        // Step validation function - FIXED
        function validateStep(stepNumber) {
            var isValid = true;
            var stepElement = $('#step-' + stepNumber);

            // Clear previous error states
            stepElement.find('.has-error').removeClass('has-error');
            stepElement.find('.error-text').remove();

            // Validate required fields in current step
            stepElement.find('[required]').each(function () {
                var field = $(this);
                var value = field.val();
                var fieldType = field.attr('type');
                var isSelect = field.is('select');
                var isSelect2 = field.hasClass('select2-multiple');

                // Handle Select2 multiple selects
                if (isSelect2) {
                    var select2Data = field.select2('data');
                    if (select2Data.length === 0) {
                        field.closest('.mb-3').addClass('has-error');
                        field.after('<div class="error-text text-danger small mt-1">This field is required</div>');
                        isValid = false;
                    }
                }
                // Handle regular selects
                else if (isSelect && !value) {
                    field.closest('.mb-3').addClass('has-error');
                    field.after('<div class="error-text text-danger small mt-1">This field is required</div>');
                    isValid = false;
                }
                // Handle file inputs
                else if (fieldType === 'file') {
                    if (field[0].files.length === 0) {
                        field.closest('.mb-3').addClass('has-error');
                        field.after('<div class="error-text text-danger small mt-1">Please upload a file</div>');
                        isValid = false;
                    } else {
                        // Check file size (2MB limit)
                        var file = field[0].files[0];
                        if (file.size > 2 * 1024 * 1024) {
                            field.closest('.mb-3').addClass('has-error');
                            field.after('<div class="error-text text-danger small mt-1">File size must be less than 2MB</div>');
                            isValid = false;
                        }
                    }
                }
                // Handle text/date/number inputs
                else if (!value && value !== 0) {
                    field.closest('.mb-3').addClass('has-error');
                    field.after('<div class="error-text text-danger small mt-1">This field is required</div>');
                    isValid = false;
                }
            });

            if (!isValid) {
                // Scroll to first error
                $('html, body').animate({
                    scrollTop: stepElement.find('.has-error').first().offset().top - 100
                }, 500);
            }

            return isValid;
        }

        // Toggle health issues details
        $('#has_health_issues').change(function () {
            const show = $(this).val() === '1';
            $('#health-details').toggle(show);
        });

        // Toggle disciplinary issues details
        $('#has_disciplinary_issues').change(function () {
            const show = $(this).val() === '1';
            $('#discipline-details').toggle(show);
        });

        // Dynamic academic block management
        let academicIndex = {{ count(old('academic_history', [])) > 0 ? count(old('academic_history', [])) : 1 }};

        $('#add-academic').click(function () {
            const html = `
                <div class="card mb-3 academic-block">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Academic Level</label>
                                <select name="academic_history[${academicIndex}][academic_level]" class="form-select">
                                    <option value="">-- Select --</option>
                                    @foreach (array_column(\App\Helpers\AcademicLevel::cases(), 'value') as $lvl)
                                        <option value="{{ $lvl }}">{{ $lvl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>School Name</label>
                                <input type="text" name="academic_history[${academicIndex}][school_name]" class="form-control" placeholder="School">
                            </div>
                            <div class="col-md-2">
                                <label>From Year</label>
                                <input type="number" name="academic_history[${academicIndex}][from_year]" class="form-control" placeholder="From YYYY" min="1900" max="{{ date('Y') }}">
                            </div>
                            <div class="col-md-2">
                                <label>To Year</label>
                                <input type="number" name="academic_history[${academicIndex}][to_year]" class="form-control" placeholder="To YYYY" min="1900" max="{{ date('Y') }}">
                            </div>
                            <div class="col-md-1">
                                <label>Aggregate Score</label>
                                <input type="text" name="academic_history[${academicIndex}][aggregate_score]" class="form-control" placeholder="Agg">
                            </div>
                            <div class="col-md-1">
                                <label>Grade</label>
                                <input type="text" name="academic_history[${academicIndex}][grade]" class="form-control" placeholder="Grade">
                            </div>
                            <div class="col-md-2 align-self-end">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-academic">
                                    <i class="fas fa-times me-1"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            $('#academic-history-wrapper').append(html);
            academicIndex++;
        });

        $(document).on('click', '.remove-academic', function () {
            $(this).closest('.academic-block').remove();
        });

        // FIXED: Form submission validation
        $('#studentForm').submit(function (e) {
            // Don't prevent default here - we'll handle validation differently

            // Validate all steps
            var allValid = true;
            var firstInvalidStep = null;

            for (var i = 1; i <= totalSteps; i++) {
                if (!validateStep(i)) {
                    allValid = false;
                    if (firstInvalidStep === null) {
                        firstInvalidStep = i;
                    }
                }
            }

            if (!allValid) {
                e.preventDefault(); // Only prevent if validation fails
                // Show the first step with errors
                if (firstInvalidStep !== null) {
                    showStep(firstInvalidStep);

                    // Scroll to top of form
                    $('html, body').animate({
                        scrollTop: $('.steps-form').offset().top - 50
                    }, 500);
                }
                return false;
            }

            // If all valid, allow the form to submit normally
            return true;
        });
    });
</script>
@endsection
