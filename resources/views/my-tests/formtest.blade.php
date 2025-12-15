<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student - AdminLTE</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .form-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #343a40;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
        }

        .form-section h3 {
            color: #495057;
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .form-control, .form-select {
            border-radius: 6px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 6px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Student Registration Form</h1>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('validation'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('validation') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form method="post" action="{{ route('students.store') }}" enctype="multipart/form-data" id="studentForm">
            @csrf

            <!-- User Account Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">User Account</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="user_option" id="existing_user"
                                    value="existing" {{ old('user_option', 'existing') == 'existing' ? 'checked' : '' }}>
                                <label class="form-check-label" for="existing_user">
                                    Use Existing User Account
                                </label>
                            </div>
                            @php
                                $users = App\Models\User::whereDoesntHave('student')->get();
                            @endphp

                            <div id="existing-user-section" style="{{ old('user_option', 'existing') == 'existing' ? '' : 'display: none;' }}">
                                <label class="required">Select User</label>
                                <select name="user_id" class="form-select {{ $errors->has('user_id') ? 'is-invalid' : '' }}" id="user_id">
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('user_id'))
                                    <div class="error-message">{{ $errors->first('user_id') }}</div>
                                @endif
                                <small class="form-text text-muted">
                                    Select an existing user account to link with this student
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="user_option" id="new_user"
                                    value="new" {{ old('user_option') == 'new' ? 'checked' : '' }}>
                                <label class="form-check-label" for="new_user">
                                    Create New User Account
                                </label>
                            </div>

                            <div id="new-user-section" style="{{ old('user_option') == 'new' ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="required">Email Address</label>
                                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                            value="{{ old('email') }}" placeholder="student@example.com">
                                        @if($errors->has('email'))
                                            <div class="error-message">{{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="required">Password</label>
                                        <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                            placeholder="••••••••" value="{{ old('password') }}">
                                        @if($errors->has('password'))
                                            <div class="error-message">{{ $errors->first('password') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="required">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            placeholder="••••••••" value="{{ old('password_confirmation') }}">
                                    </div>
                                </div>
                                <input type="hidden" name="create_new_user" value="1">
                                <small class="form-text text-muted">
                                    A new user account will be created with student role
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section">
                <h3>Personal Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="required">Admission Year</label>
                        <input type="number" name="admission_year" class="form-control {{ $errors->has('admission_year') ? 'is-invalid' : '' }}"
                            value="{{ old('admission_year', date('Y')) }}" min="2000" max="2050" required>
                        @if($errors->has('admission_year'))
                            <div class="error-message">{{ $errors->first('admission_year') }}</div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="required">Joining Class</label>
                        <input type="text" name="joining_class" class="form-control {{ $errors->has('joining_class') ? 'is-invalid' : '' }}"
                            value="{{ old('joining_class') }}" placeholder="e.g. S.1" required>
                        @if($errors->has('joining_class'))
                            <div class="error-message">{{ $errors->first('joining_class') }}</div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="required">First Name</label>
                        <input type="text" name="first_name" class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}"
                            value="{{ old('first_name') }}" required>
                        @if($errors->has('first_name'))
                            <div class="error-message">{{ $errors->first('first_name') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" class="form-control {{ $errors->has('middle_name') ? 'is-invalid' : '' }}"
                            value="{{ old('middle_name') }}">
                        @if($errors->has('middle_name'))
                            <div class="error-message">{{ $errors->first('middle_name') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="required">Last Name</label>
                        <input type="text" name="last_name" class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}"
                            value="{{ old('last_name') }}" required>
                        @if($errors->has('last_name'))
                            <div class="error-message">{{ $errors->first('last_name') }}</div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="required">Gender</label>
                        <select name="gender" class="form-select {{ $errors->has('gender') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Select Gender --</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @if($errors->has('gender'))
                            <div class="error-message">{{ $errors->first('gender') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="required">Date of Birth</label>
                        <input type="date" name="dob" class="form-control {{ $errors->has('dob') ? 'is-invalid' : '' }}"
                            value="{{ old('dob') }}" required>
                        @if($errors->has('dob'))
                            <div class="error-message">{{ $errors->first('dob') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="required">Applying Section</label>
                        <select name="applying_section" class="form-select {{ $errors->has('applying_section') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Select Section --</option>
                            @foreach(array_column(\App\Helpers\ApplyingSection::cases(), 'value') as $s)
                                <option value="{{ $s }}" {{ old('applying_section') == $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('applying_section'))
                            <div class="error-message">{{ $errors->first('applying_section') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="required">Religious Affiliation</label>
                        <select name="religious_affiliation" class="form-select {{ $errors->has('religious_affiliation') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Select Religion --</option>
                            @foreach(array_column(\App\Helpers\ReligiousAffiliation::cases(), 'value') as $r)
                                <option value="{{ $r }}" {{ old('religious_affiliation') == $r ? 'selected' : '' }}>
                                    {{ $r }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('religious_affiliation'))
                            <div class="error-message">{{ $errors->first('religious_affiliation') }}</div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>A-Level Combination</label>
                        <input type="text" name="a_level_combination" class="form-control {{ $errors->has('a_level_combination') ? 'is-invalid' : '' }}"
                            value="{{ old('a_level_combination') }}" placeholder="e.g. PCM">
                        @if($errors->has('a_level_combination'))
                            <div class="error-message">{{ $errors->first('a_level_combination') }}</div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="required">Citizenship (Multiple)</label>
                        <select name="citizenship[]" class="form-select {{ $errors->has('citizenship') ? 'is-invalid' : '' }}" multiple required>
                            <option value="UG" {{ in_array('UG', old('citizenship', [])) ? 'selected' : '' }}>Uganda</option>
                            <option value="KE" {{ in_array('KE', old('citizenship', [])) ? 'selected' : '' }}>Kenya</option>
                            <option value="TZ" {{ in_array('TZ', old('citizenship', [])) ? 'selected' : '' }}>Tanzania</option>
                            <option value="RW" {{ in_array('RW', old('citizenship', [])) ? 'selected' : '' }}>Rwanda</option>
                            <option value="BI" {{ in_array('BI', old('citizenship', [])) ? 'selected' : '' }}>Burundi</option>
                            <option value="SS" {{ in_array('SS', old('citizenship', [])) ? 'selected' : '' }}>South Sudan</option>
                        </select>
                        @if($errors->has('citizenship'))
                            <div class="error-message">{{ $errors->first('citizenship') }}</div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="required">Spoken Languages (Multiple)</label>
                        <select name="spoken_languages[]" class="form-select {{ $errors->has('spoken_languages') ? 'is-invalid' : '' }}" multiple required>
                            <option value="en" {{ in_array('en', old('spoken_languages', [])) ? 'selected' : '' }}>English</option>
                            <option value="sw" {{ in_array('sw', old('spoken_languages', [])) ? 'selected' : '' }}>Swahili</option>
                            <option value="lg" {{ in_array('lg', old('spoken_languages', [])) ? 'selected' : '' }}>Luganda</option>
                            <option value="fr" {{ in_array('fr', old('spoken_languages', [])) ? 'selected' : '' }}>French</option>
                            <option value="ar" {{ in_array('ar', old('spoken_languages', [])) ? 'selected' : '' }}>Arabic</option>
                        </select>
                        @if($errors->has('spoken_languages'))
                            <div class="error-message">{{ $errors->first('spoken_languages') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="required">ID Type</label>
                        <select name="id_type" class="form-select {{ $errors->has('id_type') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Select ID Type --</option>
                            @foreach(array_column(\App\Helpers\IDType::cases(), 'value') as $t)
                                <option value="{{ $t }}" {{ old('id_type') == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('id_type'))
                            <div class="error-message">{{ $errors->first('id_type') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="required">ID Number</label>
                        <input type="text" name="id_no" class="form-control {{ $errors->has('id_no') ? 'is-invalid' : '' }}"
                            value="{{ old('id_no') }}" required>
                        @if($errors->has('id_no'))
                            <div class="error-message">{{ $errors->first('id_no') }}</div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="required">ID Image (≤2 MB)</label>
                        <input type="file" name="id_image_path" class="form-control {{ $errors->has('id_image_path') ? 'is-invalid' : '' }}"
                            accept="image/*" required>
                        @if($errors->has('id_image_path'))
                            <div class="error-message">{{ $errors->first('id_image_path') }}</div>
                        @endif
                        <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Other Religious Affiliation</label>
                        <input type="text" name="other_religious_affiliation" class="form-control {{ $errors->has('other_religious_affiliation') ? 'is-invalid' : '' }}"
                            value="{{ old('other_religious_affiliation') }}">
                        @if($errors->has('other_religious_affiliation'))
                            <div class="error-message">{{ $errors->first('other_religious_affiliation') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Academic History Section -->
            <div class="form-section">
                <h3>Academic History</h3>
                <div id="academic-history-wrapper">
                    @php
                        $academicHistory = old('academic_history', [['' => '']]);
                    @endphp

                    @foreach($academicHistory as $index => $academic)
                    <div class="academic-block mb-4 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="required">Academic Level</label>
                                <select name="academic_history[{{ $index }}][academic_level]"
                                    class="form-select {{ $errors->has("academic_history.{$index}.academic_level") ? 'is-invalid' : '' }}"
                                    required>
                                    <option value="">-- Select Level --</option>
                                    <option value="PLE" {{ old("academic_history.{$index}.academic_level") == 'PLE' ? 'selected' : '' }}>PLE</option>
                                    <option value="UCE" {{ old("academic_history.{$index}.academic_level") == 'UCE' ? 'selected' : '' }}>UCE</option>
                                    <option value="UACE" {{ old("academic_history.{$index}.academic_level") == 'UACE' ? 'selected' : '' }}>UACE</option>
                                    <option value="Other" {{ old("academic_history.{$index}.academic_level") == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @if($errors->has("academic_history.{$index}.academic_level"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.academic_level") }}</div>
                                @endif
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="required">School Name</label>
                                <input type="text" name="academic_history[{{ $index }}][school_name]"
                                    class="form-control {{ $errors->has("academic_history.{$index}.school_name") ? 'is-invalid' : '' }}"
                                    value="{{ old("academic_history.{$index}.school_name") }}"
                                    placeholder="School Name" required>
                                @if($errors->has("academic_history.{$index}.school_name"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.school_name") }}</div>
                                @endif
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="required">From Year</label>
                                <input type="number" name="academic_history[{{ $index }}][from_year]"
                                    class="form-control {{ $errors->has("academic_history.{$index}.from_year") ? 'is-invalid' : '' }}"
                                    value="{{ old("academic_history.{$index}.from_year") }}"
                                    placeholder="YYYY" min="1900" max="{{ date('Y') }}" required>
                                @if($errors->has("academic_history.{$index}.from_year"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.from_year") }}</div>
                                @endif
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="required">To Year</label>
                                <input type="number" name="academic_history[{{ $index }}][to_year]"
                                    class="form-control {{ $errors->has("academic_history.{$index}.to_year") ? 'is-invalid' : '' }}"
                                    value="{{ old("academic_history.{$index}.to_year") }}"
                                    placeholder="YYYY" min="1900" max="{{ date('Y') }}" required>
                                @if($errors->has("academic_history.{$index}.to_year"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.to_year") }}</div>
                                @endif
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="required">Aggregate Score</label>
                                <input type="text" name="academic_history[{{ $index }}][aggregate_score]"
                                    class="form-control {{ $errors->has("academic_history.{$index}.aggregate_score") ? 'is-invalid' : '' }}"
                                    value="{{ old("academic_history.{$index}.aggregate_score") }}" required>
                                @if($errors->has("academic_history.{$index}.aggregate_score"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.aggregate_score") }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>Grade</label>
                                <input type="text" name="academic_history[{{ $index }}][grade]"
                                    class="form-control {{ $errors->has("academic_history.{$index}.grade") ? 'is-invalid' : '' }}"
                                    value="{{ old("academic_history.{$index}.grade") }}">
                                @if($errors->has("academic_history.{$index}.grade"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.grade") }}</div>
                                @endif
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>PLE File</label>
                                <input type="file" name="academic_history[{{ $index }}][ple_file]"
                                    class="form-control {{ $errors->has("academic_history.{$index}.ple_file") ? 'is-invalid' : '' }}">
                                @if($errors->has("academic_history.{$index}.ple_file"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.ple_file") }}</div>
                                @endif
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>O-Level File</label>
                                <input type="file" name="academic_history[{{ $index }}][o_level_file]"
                                    class="form-control {{ $errors->has("academic_history.{$index}.o_level_file") ? 'is-invalid' : '' }}">
                                @if($errors->has("academic_history.{$index}.o_level_file"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.o_level_file") }}</div>
                                @endif
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Other File</label>
                                <input type="file" name="academic_history[{{ $index }}][other_file]"
                                    class="form-control {{ $errors->has("academic_history.{$index}.other_file") ? 'is-invalid' : '' }}">
                                @if($errors->has("academic_history.{$index}.other_file"))
                                    <div class="error-message">{{ $errors->first("academic_history.{$index}.other_file") }}</div>
                                @endif
                            </div>
                        </div>

                        @if($index > 0)
                        <div class="text-end">
                            <button type="button" class="btn btn-danger btn-sm remove-academic" data-index="{{ $index }}">
                                <i class="fas fa-times me-1"></i> Remove
                            </button>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="text-center mt-3">
                    <button type="button" id="add-academic" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Add Another Academic Record
                    </button>
                </div>
            </div>

            <!-- Health & Discipline Section -->
            <div class="form-section">
                <h3>Health & Discipline Information</h3>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="required">Has Health Issues?</label>
                        <select name="has_health_issues" id="has_health_issues"
                            class="form-select {{ $errors->has('has_health_issues') ? 'is-invalid' : '' }}" required>
                            <option value="0" {{ old('has_health_issues', '0') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('has_health_issues') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @if($errors->has('has_health_issues'))
                            <div class="error-message">{{ $errors->first('has_health_issues') }}</div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="required">Has Disciplinary Issues?</label>
                        <select name="has_disciplinary_issues" id="has_disciplinary_issues"
                            class="form-select {{ $errors->has('has_disciplinary_issues') ? 'is-invalid' : '' }}" required>
                            <option value="0" {{ old('has_disciplinary_issues', '0') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('has_disciplinary_issues') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @if($errors->has('has_disciplinary_issues'))
                            <div class="error-message">{{ $errors->first('has_disciplinary_issues') }}</div>
                        @endif
                    </div>
                </div>

                <!-- Health Details (Conditional) -->
                <div id="health-details" style="{{ old('has_health_issues') == '1' ? '' : 'display: none;' }}">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label>Health Condition Details</label>
                            <textarea name="health_issues" class="form-control {{ $errors->has('health_issues') ? 'is-invalid' : '' }}"
                                rows="3">{{ old('health_issues') }}</textarea>
                            @if($errors->has('health_issues'))
                                <div class="error-message">{{ $errors->first('health_issues') }}</div>
                            @endif
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Upload Medical Reports</label>
                            <input type="file" name="medical_files[]"
                                class="form-control {{ $errors->has('medical_files') ? 'is-invalid' : '' }}" multiple>
                            @if($errors->has('medical_files'))
                                <div class="error-message">{{ $errors->first('medical_files') }}</div>
                            @endif
                            <small class="form-text text-muted">You can upload multiple files</small>
                        </div>
                    </div>
                </div>

                <!-- Discipline Details (Conditional) -->
                <div id="discipline-details" style="{{ old('has_disciplinary_issues') == '1' ? '' : 'display: none;' }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Disciplinary Action</label>
                            <select name="disciplinary_issues"
                                class="form-select {{ $errors->has('disciplinary_issues') ? 'is-invalid' : '' }}">
                                <option value="">-- Select Action --</option>
                                <option value="Warning" {{ old('disciplinary_issues') == 'Warning' ? 'selected' : '' }}>Warning</option>
                                <option value="Suspension" {{ old('disciplinary_issues') == 'Suspension' ? 'selected' : '' }}>Suspension</option>
                                <option value="Expulsion" {{ old('disciplinary_issues') == 'Expulsion' ? 'selected' : '' }}>Expulsion</option>
                                @foreach(array_column(\App\Helpers\DisciplineAction::cases(), 'value') as $d)
                                    <option value="{{ $d }}" {{ old('disciplinary_issues') == $d ? 'selected' : '' }}>
                                        {{ $d }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('disciplinary_issues'))
                                <div class="error-message">{{ $errors->first('disciplinary_issues') }}</div>
                            @endif
                        </div>

                        <div class="col-md-8 mb-3">
                            <label>Reason / Details</label>
                            <textarea name="reason" class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}"
                                rows="3">{{ old('reason') }}</textarea>
                            @if($errors->has('reason'))
                                <div class="error-message">{{ $errors->first('reason') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Career & Additional Information -->
            <div class="form-section">
                <h3>Career & Additional Information</h3>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Career Aspiration</label>
                        <select name="aspiration" class="form-select {{ $errors->has('aspiration') ? 'is-invalid' : '' }}">
                            <option value="">-- Select Aspiration --</option>
                            @foreach(array_column(\App\Helpers\CareerAspirations::cases(), 'value') as $c)
                                <option value="{{ $c }}" {{ old('aspiration') == $c ? 'selected' : '' }}>
                                    {{ $c }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('aspiration'))
                            <div class="error-message">{{ $errors->first('aspiration') }}</div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Best Done Subjects</label>
                        <select name="best_done_subjects" class="form-select {{ $errors->has('best_done_subjects') ? 'is-invalid' : '' }}">
                            <option value="">-- Select Subject --</option>
                            @foreach(array_column(\App\Helpers\Subjects::cases(), 'value') as $s)
                                <option value="{{ $s }}" {{ old('best_done_subjects') == $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('best_done_subjects'))
                            <div class="error-message">{{ $errors->first('best_done_subjects') }}</div>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label>Additional Information</label>
                    <textarea name="additional_info" class="form-control {{ $errors->has('additional_info') ? 'is-invalid' : '' }}"
                        rows="4">{{ old('additional_info') }}</textarea>
                    @if($errors->has('additional_info'))
                        <div class="error-message">{{ $errors->first('additional_info') }}</div>
                    @endif
                    <small class="form-text text-muted">Anything else you want the school to know</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-2"></i> Submit Application
                </button>
            </div>
        </form>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle between existing and new user
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

            // Toggle health issues details
            $('#has_health_issues').change(function() {
                $('#health-details').toggle($(this).val() === '1');
            });

            // Toggle disciplinary issues details
            $('#has_disciplinary_issues').change(function() {
                $('#discipline-details').toggle($(this).val() === '1');
            });

            // Dynamic academic block management
            let academicIndex = {{ count(old('academic_history', [['' => '']])) }};

            $('#add-academic').click(function() {
                const html = `
                <div class="academic-block mb-4 p-3 border rounded">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="required">Academic Level</label>
                            <select name="academic_history[${academicIndex}][academic_level]" class="form-select" required>
                                <option value="">-- Select Level --</option>
                                <option value="PLE">PLE</option>
                                <option value="UCE">UCE</option>
                                <option value="UACE">UACE</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="required">School Name</label>
                            <input type="text" name="academic_history[${academicIndex}][school_name]"
                                class="form-control" placeholder="School Name" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="required">From Year</label>
                            <input type="number" name="academic_history[${academicIndex}][from_year]"
                                class="form-control" placeholder="YYYY" min="1900" max="{{ date('Y') }}" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="required">To Year</label>
                            <input type="number" name="academic_history[${academicIndex}][to_year]"
                                class="form-control" placeholder="YYYY" min="1900" max="{{ date('Y') }}" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="required">Aggregate Score</label>
                            <input type="text" name="academic_history[${academicIndex}][aggregate_score]"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Grade</label>
                            <input type="text" name="academic_history[${academicIndex}][grade]" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>PLE File</label>
                            <input type="file" name="academic_history[${academicIndex}][ple_file]" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>O-Level File</label>
                            <input type="file" name="academic_history[${academicIndex}][o_level_file]" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Other File</label>
                            <input type="file" name="academic_history[${academicIndex}][other_file]" class="form-control">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-danger btn-sm remove-academic" data-index="${academicIndex}">
                            <i class="fas fa-times me-1"></i> Remove
                        </button>
                    </div>
                </div>`;

                $('#academic-history-wrapper').append(html);
                academicIndex++;
            });

            // Remove academic block
            $(document).on('click', '.remove-academic', function() {
                $(this).closest('.academic-block').remove();
            });

            // Initialize toggles based on current values
            $('input[name="user_option"]:checked').trigger('change');
            $('#has_health_issues').trigger('change');
            $('#has_disciplinary_issues').trigger('change');

            // Auto-validate date inputs
            $('input[type="date"]').on('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();
                if (selectedDate >= today) {
                    alert('Date of birth must be in the past.');
                    this.value = '';
                }
            });

            // Auto-validate year inputs
            $('input[placeholder="YYYY"]').on('change', function() {
                const year = parseInt(this.value);
                if (year < 1900 || year > new Date().getFullYear()) {
                    alert('Year must be between 1900 and current year.');
                    this.value = '';
                }
            });
        });
    </script>
</body>
</html>
