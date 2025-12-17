@extends('layouts.main')
@section('')

@endsection
@section('title', 'Register Student')
{{-- @section('plugins.Select2', true) AdminLTE plugin flag --}}
@section('header')
    <style>
        body {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            margin-bottom: 1rem;
            border-radius: 0.5rem;

            background-color: var(--bs-card-bg, var(--bs-body-bg));
            border: 1px solid var(--bs-border-color, rgba(0, 0, 0, .125));
        }
        }

        .card-header {
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
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--bs-border-color);
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        label {
            font-weight: 500;
            margin-bottom: 0.5rem;
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
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 25px;
            font-weight: 700;
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
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }

        .current-id-image {
            max-width: 200px;
            max-height: 150px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }

        .file-preview {
            margin-top: 5px;
            font-size: 0.9rem;
            color: #6c757d;
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

            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2-multiple').select2({
                    placeholder: 'Select options',
                    width: '100%'
                });
            }

            var currentStep = 1;
            var totalSteps = 3;


            function showStep(stepNumber) {

                $('.setup-content').hide();

                $('#step-' + stepNumber).show();


                $('.steps-step button').removeClass('btn-indigo').addClass('btn-secondary');
                $('.steps-step button[data-step="step-' + stepNumber + '"]').removeClass('btn-secondary').addClass(
                    'btn-indigo');


                currentStep = stepNumber;
            }


            showStep(1);


            $('.steps-step button').click(function() {
                if (!$(this).is(':disabled')) {
                    var step = $(this).data('step');
                    var stepNumber = parseInt(step.split('-')[1]);
                    showStep(stepNumber);
                }
            });


            $('.nextBtn').click(function() {

                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        showStep(currentStep + 1);
                    }
                }
            });


            $('.prevBtn').click(function() {
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            });

            function validateStep(stepNumber) {
                var isValid = true;
                var stepElement = $('#step-' + stepNumber);

                stepElement.find('.has-error').removeClass('has-error');
                stepElement.find('.error-text').remove();

                stepElement.find('[required]').each(function() {
                    var field = $(this);
                    var value = field.val();
                    var fieldType = field.attr('type');
                    var isSelect = field.is('select');
                    var isSelect2 = field.hasClass('select2-multiple');

                    if (isSelect2) {
                        var select2Data = field.select2('data');
                        if (select2Data.length === 0) {
                            field.closest('.mb-3').addClass('has-error');
                            field.after(
                                '<div class="error-text text-danger small mt-1">This field is required</div>'
                            );
                            isValid = false;
                        }
                    }
                    else if (isSelect && !value) {
                        field.closest('.mb-3').addClass('has-error');
                        field.after(
                            '<div class="error-text text-danger small mt-1">This field is required</div>'
                        );
                        isValid = false;
                    }
                    else if (fieldType === 'file') {
                        if (field[0].files.length === 0) {
                            field.closest('.mb-3').addClass('has-error');
                            field.after(
                                '<div class="error-text text-danger small mt-1">Please upload a file</div>'
                            );
                            isValid = false;
                        } else {

                            var file = field[0].files[0];
                            if (file.size > 2 * 1024 * 1024) {
                                field.closest('.mb-3').addClass('has-error');
                                field.after(
                                    '<div class="error-text text-danger small mt-1">File size must be less than 2MB</div>'
                                );
                                isValid = false;
                            }
                        }
                    }
                    else if (!value && value !== 0) {
                        field.closest('.mb-3').addClass('has-error');
                        field.after(
                            '<div class="error-text text-danger small mt-1">This field is required</div>'
                        );
                        isValid = false;
                    }
                });

                if (!isValid) {
                    $('html, body').animate({
                        scrollTop: stepElement.find('.has-error').first().offset().top - 100
                    }, 500);
                }

                return isValid;
            }

            $('#has_health_issues').change(function() {
                const show = $(this).val() === '1';
                $('#health-details').toggle(show);
            });

            $('#has_disciplinary_issues').change(function() {
                const show = $(this).val() === '1';
                $('#discipline-details').toggle(show);
            });

            let academicIndex =
                {{ count(old('academic_history', [])) > 0 ? count(old('academic_history', [])) : 1 }};

            $('#add-academic').click(function() {
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

            $(document).on('click', '.remove-academic', function() {
                $(this).closest('.academic-block').remove();
            });

            $('#studentForm').on('submit', function(e) {
                e.preventDefault();

                let firstInvalid = null;
                for (let i = 1; i <= totalSteps; i++) {
                    if (!validateStep(i) && firstInvalid === null) {
                        firstInvalid = i;
                    }
                }


                if (firstInvalid !== null) {
                    showStep(firstInvalid);
                    $('html,body').animate({
                        scrollTop: $('.steps-form').offset().top - 50
                    }, 500);
                    return;
                }


                this.submit();
            });

        });
        /*$(document).ready(function() {

            $('input[name="user_option"]').change(function() {
                if ($(this).val() === 'existing') {
                    $('#existing-user-section').show();
                    $('#new-user-section').hide();
                    $('#new-user-section input').prop('required', false);
                    $('#existing-user-section select').prop('required', true);
                } else {
                    $('#existing-user-section').hide();
                    $('#new-user-section').show();
                    $('#existing-user-section select').prop('required', false);
                    $('#new-user-section input').prop('required', true);
                }
            });


            $('input[name="user_option"]:checked').trigger('change');
        });*/
    </script>
@endsection
