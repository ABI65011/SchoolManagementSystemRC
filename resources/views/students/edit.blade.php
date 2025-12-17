@extends('layouts.main')
@section('title', 'Edit Student')
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

            <!-- Steps form -->
            <div class="card-body mb-4">
                <!-- Stepper -->
                <div class="steps-form">
                    <div class="steps-row setup-panel">
                        <div class="steps-step">
                            <button type="button" class="btn btn-indigo btn-circle" data-step="step-1">
                                1
                            </button>
                            <p>Admission & Personal Data</p>
                        </div>
                        <div class="steps-step">
                            <button type="button" class="btn btn-secondary btn-circle" disabled data-step="step-2">
                                2
                            </button>
                            <p>Academic History</p>
                        </div>
                        <div class="steps-step">
                            <button type="button" class="btn btn-secondary btn-circle" disabled data-step="step-3">
                                3
                            </button>
                            <p>Medical, Discipline, Career</p>
                        </div>
                    </div>
                </div>

                <form role="form" action="{{ route('students.update', $student->id) }}" method="POST"
                    enctype="multipart/form-data" id="studentForm">
                    @csrf
                    @method('PUT')

                    <!-- ===== STEP 1 – Core bio / admission ================================== -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">User Account</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="user_option"
                                                    id="existing_user" value="existing" checked>
                                                <label class="form-check-label" for="existing_user">
                                                    Use Existing User Account
                                                </label>
                                            </div>

                                            <div id="existing-user-section">
                                                <label class="required-field">Select User</label>
                                                <select name="user_id" class="form-select" id="user_id">
                                                    <option value="hidden {{ old('user_id', $student->user_id) ? '' : 'selected' }}">-- Select User --</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ old('user_id', $student->user_id) == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">
                                                    Select an existing user account to link with this student
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row setup-content" id="step-1">
                        <div class="col-md-12">
                            <h3 class="step-title"><strong>Admission & Personal Data</strong></h3>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="required-field">Admission Year</label>
                                    <input type="number" min="1900" max="2099" name="admission_year"
                                        value="{{ old('admission_year', $student->admission_year) }}"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="required-field">Joining Class</label>
                                    <input type="text" name="joining_class"
                                        value="{{ old('joining_class', $student->joining_class) }}" class="form-control"
                                        placeholder="e.g. S.1" required>
                                </div>
                                <div class="col-md-3">
                                    <label>A-Level Combination</label>
                                    <input type="text" name="a_level_combination"
                                        value="{{ old('a_level_combination', $student->a_level_combination) }}"
                                        class="form-control" placeholder="e.g. PCM">
                                </div>
                                <div class="col-md-3">
                                    <label class="required-field">Applying Section</label>
                                    <select name="applying_section" class="form-select" required>
                                        <option value="">-- Select Section --</option>
                                        @foreach (array_column(\App\Helpers\ApplyingSection::cases(), 'value') as $s)
                                            <option value="{{ $s }}"
                                                {{ old('applying_section', $student->applying_section) == $s ? 'selected' : '' }}>
                                                {{ $s }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="required-field">Surname</label>
                                    <input type="text" name="last_name"
                                        value="{{ old('last_name', $student->last_name) }}" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="required-field">First Name</label>
                                    <input type="text" name="first_name"
                                        value="{{ old('first_name', $student->first_name) }}" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label>Other Names</label>
                                    <input type="text" name="middle_name"
                                        value="{{ old('middle_name', $student->middle_name) }}" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="required-field">Gender</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">-- Select Gender --</option>
                                        @foreach (array_column(\App\Helpers\Gender::cases(), 'value') as $g)
                                            <option value="{{ $g }}"
                                                {{ old('gender', $student->gender) == $g ? 'selected' : '' }}>
                                                {{ $g }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="required-field">Date of Birth</label>
                                    <input type="date" name="dob"
                                        value="{{ old('dob', $student->dob) }}"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="required-field">Citizenship (multiple)</label>
                                    <select name="citizenship[]" class="form-select" multiple required>
                                        @php
                                            $studentCitizenship =
                                                json_decode($student->citizenship ?? '[]', true) ?: [];
                                            $oldCitizenship = old('citizenship', $studentCitizenship);
                                        @endphp
                                        @foreach (countries() as $code => $name)
                                            <option value="{{ $code }}"
                                                {{ in_array($code, $oldCitizenship) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="required-field">Religious Affiliation</label>
                                    <select name="religious_affiliation" class="form-select" required>
                                        <option value="">-- Select Religion --</option>
                                        @foreach (array_column(\App\Helpers\ReligiousAffiliation::cases(), 'value') as $r)
                                            <option value="{{ $r }}"
                                                {{ old('religious_affiliation', $student->religious_affiliation) == $r ? 'selected' : '' }}>
                                                {{ $r }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="required-field">Spoken Languages</label>
                                    <select name="spoken_languages[]" class="form-select" multiple required>
                                        @php
                                            $studentLanguages =
                                                json_decode($student->spoken_languages ?? '[]', true) ?: [];
                                            $oldLanguages = old('spoken_languages', $studentLanguages);
                                        @endphp
                                        @foreach (languages() as $code => $name)
                                            <option value="{{ $code }}"
                                                {{ in_array($code, $oldLanguages) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="required-field">ID Type</label>
                                    <select name="id_type" class="form-select" required>
                                        <option value="">-- Select ID Type --</option>
                                        @foreach (array_column(\App\Helpers\IDType::cases(), 'value') as $t)
                                            <option value="{{ $t }}"
                                                {{ old('id_type', $student->id_type) == $t ? 'selected' : '' }}>
                                                {{ $t }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="required-field">ID No</label>
                                    <input type="text" name="id_no" value="{{ old('id_no', $student->id_no) }}"
                                        class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label>Upload ID Image (≤2 MB)</label>
                                    <input type="file" name="id_image_path" class="form-control" accept="image/*">
                                    @if ($student->id_image_path)
                                        <div class="mt-2">
                                            <p class="file-preview">Current ID Image:</p>
                                            <img src="{{ asset('storage/identity' . $student->id_image_path) }}"
                                                alt="Current ID Image" class="current-id-image">
                                            <p class="file-preview">Leave empty to keep current image</p>
                                        </div>
                                    @endif
                                    <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF. Max size:
                                        2MB</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button class="btn btn-indigo nextBtn" type="button">
                                    Next <i class="bi bi-arrow-right-circle-fill ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ===== STEP 2 – Academic History ======================================= -->
                    <div class="row setup-content" id="step-2" style="display: none;">
                        <div class="col-md-12">
                            <h3 class="step-title"><strong>Academic History</strong></h3>

                            <div id="academic-history-wrapper">
                                @php
                                    $academicHistories = old(
                                        'academic_history',
                                        $student->academicHistories->toArray(),
                                    );
                                    if (empty($academicHistories)) {
                                        $academicHistories = [['' => '']];
                                    }
                                @endphp

                                @foreach ($academicHistories as $index => $academic)
                                    <div class="card mb-3 academic-block">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label>Academic Level</label>
                                                    <select name="academic_history[{{ $index }}][academic_level]"
                                                        class="form-select">
                                                        <option value="">-- Select --</option>
                                                        @foreach (array_column(\App\Helpers\AcademicLevel::cases(), 'value') as $lvl)
                                                            <option value="{{ $lvl }}"
                                                                {{ old("academic_history.{$index}.academic_level", $academic['academic_level'] ?? '') == $lvl ? 'selected' : '' }}>
                                                                {{ $lvl }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>School Name</label>
                                                    <input type="text"
                                                        name="academic_history[{{ $index }}][school_name]"
                                                        class="form-control" placeholder="School"
                                                        value="{{ old("academic_history.{$index}.school_name", $academic['school_name'] ?? '') }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label>From Year</label>
                                                    <input type="number"
                                                        name="academic_history[{{ $index }}][from_year]"
                                                        class="form-control" placeholder="From YYYY"
                                                        value="{{ old("academic_history.{$index}.from_year", $academic['from_year'] ?? '') }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label>To Year</label>
                                                    <input type="number"
                                                        name="academic_history[{{ $index }}][to_year]"
                                                        class="form-control" placeholder="To YYYY"
                                                        value="{{ old("academic_history.{$index}.to_year", $academic['to_year'] ?? '') }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <label>Aggregate Score</label>
                                                    <input type="text"
                                                        name="academic_history[{{ $index }}][aggregate_score]"
                                                        class="form-control" placeholder="Agg"
                                                        value="{{ old("academic_history.{$index}.aggregate_score", $academic['aggregate_score'] ?? '') }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <label>Grade</label>
                                                    <input type="text"
                                                        name="academic_history[{{ $index }}][grade]"
                                                        class="form-control" placeholder="Grade"
                                                        value="{{ old("academic_history.{$index}.grade", $academic['grade'] ?? '') }}">
                                                </div>
                                                <div class="col-md-2 align-self-end">
                                                    @if ($loop->first)
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            id="add-academic">
                                                            <i class="fas fa-plus me-1"></i> Add More
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger remove-academic">
                                                            <i class="fas fa-times me-1"></i> Remove
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-4">
                                                    <label>PLE File</label>
                                                    <input type="file"
                                                        name="academic_history[{{ $index }}][ple_file]"
                                                        class="form-control">
                                                    @if (!empty($academic['ple_file']))
                                                        <p class="file-preview">Current:
                                                            {{ basename($academic['ple_file']) }}</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-4">
                                                    <label>O-Level File</label>
                                                    <input type="file"
                                                        name="academic_history[{{ $index }}][o_level_file]"
                                                        class="form-control">
                                                    @if (!empty($academic['o_level_file']))
                                                        <p class="file-preview">Current:
                                                            {{ basename($academic['o_level_file']) }}</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Other File</label>
                                                    <input type="file"
                                                        name="academic_history[{{ $index }}][other_file]"
                                                        class="form-control">
                                                    @if (!empty($academic['other_file']))
                                                        <p class="file-preview">Current:
                                                            {{ basename($academic['other_file']) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn btn-secondary prevBtn" type="button">
                                    <i class="bi bi-arrow-left-circle-fill me-1"></i> Previous
                                </button>
                                <button class="btn btn-indigo nextBtn" type="button">
                                    Next <i class="bi bi-arrow-right-circle-fill ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ===== STEP 3 – Medical, Discipline, Career, Misc ===================== -->
                    <div class="row setup-content" id="step-3" style="display: none;">
                        <div class="col-md-12">
                            <h3 class="step-title"><strong>Health, Conduct & Career</strong></h3>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    @php
                                        $medicalHistory = $student->medicalHistory ?? null;
                                    @endphp
                                    <div class="mb-3">
                                        <label>Any Health Issues?</label>
                                        <select name="has_health_issues" id="has_health_issues" class="form-select">
                                            <option value="0"
                                                {{ old('has_health_issues', $medicalHistory->has_health_issues ?? 0) == '0' ? 'selected' : '' }}>
                                                No</option>
                                            <option value="1"
                                                {{ old('has_health_issues', $medicalHistory->has_health_issues ?? 0) == '1' ? 'selected' : '' }}>
                                                Yes</option>
                                        </select>
                                    </div>
                                    <div id="health-details" class="mt-2"
                                        style="{{ old('has_health_issues', $medicalHistory->has_health_issues ?? 0) == '1' ? '' : 'display:none' }}">
                                        <div class="mb-3">
                                            <label>Health Condition Details</label>
                                            <textarea name="health_issues" class="form-control" rows="3"
                                                placeholder="Describe condition / attach files below">{{ old('health_issues', $medicalHistory->health_issues ?? '') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label>Upload Medical Reports</label>
                                            <input type="file" name="medical_files[]" multiple class="form-control">
                                            <small class="form-text text-muted">You can upload multiple files (PDF, JPG,
                                                PNG)</small>
                                            @if ($medicalHistory && $medicalHistory->files)
                                                @php
                                                    $files = json_decode($medicalHistory->files, true) ?: [];
                                                @endphp
                                                @if (!empty($files))
                                                    <p class="file-preview">Current files: {{ count($files) }} file(s)</p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    @php
                                        $disciplineHistory = $student->disciplineHistory ?? null;
                                    @endphp
                                    <div class="mb-3">
                                        <label>Any Disciplinary Issues?</label>
                                        <select name="has_disciplinary_issues" id="has_disciplinary_issues"
                                            class="form-select">
                                            <option value="0"
                                                {{ old('has_disciplinary_issues', $disciplineHistory->has_disciplinary_issues ?? 0) == '0' ? 'selected' : '' }}>
                                                No</option>
                                            <option value="1"
                                                {{ old('has_disciplinary_issues', $disciplineHistory->has_disciplinary_issues ?? 0) == '1' ? 'selected' : '' }}>
                                                Yes</option>
                                        </select>
                                    </div>
                                    <div id="discipline-details" class="mt-2"
                                        style="{{ old('has_disciplinary_issues', $disciplineHistory->has_disciplinary_issues ?? 0) == '1' ? '' : 'display:none' }}">
                                        <div class="mb-3">
                                            <label>Action Taken</label>
                                            <select name="disciplinary_issues" class="form-select">
                                                <option value="">-- Select Action --</option>
                                                @foreach (array_column(\App\Helpers\DisciplineAction::cases(), 'value') as $d)
                                                    <option value="{{ $d }}"
                                                        {{ old('disciplinary_issues', $disciplineHistory->disciplinary_issues ?? '') == $d ? 'selected' : '' }}>
                                                        {{ $d }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Reason / Details</label>
                                            <textarea name="reason" class="form-control" rows="3" placeholder="Reason / details">{{ old('reason', $disciplineHistory->reason ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                @php
                                    $careerAspiration = $student->careerAspiration ?? null;
                                @endphp
                                <div class="col-md-6">
                                    <label>Career Aspiration</label>
                                    <select name="aspiration" class="form-select">
                                        <option value="">-- Select Aspiration --</option>
                                        @foreach (array_column(\App\Helpers\CareerAspirations::cases(), 'value') as $c)
                                            <option value="{{ $c }}"
                                                {{ old('aspiration', $careerAspiration->aspiration ?? '') == $c ? 'selected' : '' }}>
                                                {{ $c }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Best Done Subjects</label>
                                    <select name="best_done_subjects" class="form-select">
                                        <option value="">-- Select Subject --</option>
                                        @foreach (array_column(\App\Helpers\Subjects::cases(), 'value') as $s)
                                            <option value="{{ $s }}"
                                                {{ old('best_done_subjects', $careerAspiration->best_done_subjects ?? '') == $s ? 'selected' : '' }}>
                                                {{ $s }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label>Additional Information</label>
                                    <textarea name="additional_info" class="form-control" rows="4"
                                        placeholder="Anything else you want the school to know">{{ old('additional_info', $student->additional_info) }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button class="btn btn-secondary prevBtn" type="button">
                                    <i class="bi bi-arrow-left-circle-fill me-1"></i> Previous
                                </button>
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-save me-1"></i> Update Student
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Steps form -->
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

            let academicIndex = {{ count(old('academic_history', $student->academicHistories)) }};

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
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>PLE File</label>
                                <input type="file" name="academic_history[${academicIndex}][ple_file]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>O-Level File</label>
                                <input type="file" name="academic_history[${academicIndex}][o_level_file]" class="form-control">
                            </div>
                            <div class="col-md-4">
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

            $('#has_health_issues').trigger('change');
            $('#has_disciplinary_issues').trigger('change');
        });
    </script>
@endsection
