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
                <p>Medical, Discipline, Career, Misc </p>
            </div>
        </div>
    </div>
    <form role="form" action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data"
        id="studentForm">
        @csrf

        <!-- ===== STEP 1 – Core bio / admission ================================== -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h5 class="card-title mb-0">User Account</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="user_option" id="existing_user"
                                        value="existing" checked>
                                    <label class="form-check-label" for="existing_user">
                                        Use Existing User Account
                                    </label>
                                </div>

                                <div id="existing-user-section">
                                    <label class="required-field">Select User</label>
                                    <select name="user_id" class="form-select" id="user_id">
                                        <option value="">-- Select User --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Select an existing user account to link with this student
                                    </small>
                                </div>
                            </div> --}}

                            <div class="col-md-12">
                                <div class="form-check mb-3">
                                    {{-- <input class="form-check-input" type="radio" name="user_option" id="new_user"
                                        value="new"> --}}
                                    <label class="form-check-label" for="new_user">
                                        Create New User Account
                                    </label>
                                </div>

                                <div>
                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="col-md-12 mb-3">
                                                <label class="required-field">name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ old('name') }}">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="required-field">Select Role</label>
                                                <select name="role" class="form-select" id="role">
                                                    <option value="">-- Select Role --</option>
                                                    @foreach (array_column(\App\Helpers\UserRoles::cases(), 'value') as $role)
                                                        <option value="{{ $role }}"
                                                            {{ old('role') == $role ? 'selected' : '' }}>
                                                            {{ $role }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">

                                            <div class="col-md-12 mb-3">
                                                <label class="required-field">Email Address</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email') }}" placeholder="student@example.com">
                                            </div>
                                            <div class="row">

                                                <div class="col-md-6 mb-3">
                                                    <label class="required-field">Password</label>
                                                    <input type="password" name="password" class="form-control">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="required-field">Confirm Password</label>
                                                    <input type="password" name="password_confirmation"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="create_new_user" value="1">
                                    </div>
                                    <small class="form-text text-muted">
                                        A new user account will be created with student role
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
                            value="{{ old('admission_year') }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="required-field">Joining Class</label>
                        <input type="text" name="joining_class" value="{{ old('joining_class') }}"
                            class="form-control" placeholder="e.g. S.1" required>
                    </div>
                    <div class="col-md-3">
                        <label>A-Level Combination</label>
                        <input type="text" name="a_level_combination" value="{{ old('a_level_combination') }}"
                            class="form-control" placeholder="e.g. PCM">
                    </div>
                    <div class="col-md-3">
                        <label class="required-field">Applying Section</label>
                        <select name="applying_section" class="form-select" required>
                            <option value="">-- Select Section --</option>
                            @foreach (array_column(\App\Helpers\ApplyingSection::cases(), 'value') as $s)
                                <option value="{{ $s }}"
                                    {{ old('applying_section') == $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="required-field">Surname</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="required-field">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}"
                            class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Other Names</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                            class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="required-field">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="">-- Select Gender --</option>
                            @foreach (array_column(\App\Helpers\Gender::cases(), 'value') as $g)
                                <option value="{{ $g }}" {{ old('gender') == $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="required-field">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob') }}" class="form-control"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="required-field">Citizenship (multiple)</label>
                        <select name="citizenship[]" class="form-select" multiple required>
                            @foreach (countries() as $code => $name)
                                <option value="{{ $code }}" @if (in_array($code, old('citizenship', []))) selected @endif>
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
                                    {{ old('religious_affiliation') == $r ? 'selected' : '' }}>
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
                            @foreach (languages() as $code => $name)
                                <option value="{{ $code }}" @if (in_array($code, old('spoken_languages', []))) selected @endif>
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
                                <option value="{{ $t }}" {{ old('id_type') == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="required-field">ID No</label>
                        <input type="text" name="id_no" value="{{ old('id_no') }}" class="form-control"
                            required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="required-field">Upload ID Image (≤2 MB)</label>
                        <input type="file" name="id_image_path" class="form-control" accept="image/*" required>
                        <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF. Max
                            size: 2MB</small>
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
                    @if (count(old('academic_history', [])) > 0)
                        @foreach (old('academic_history', [[]]) as $index => $academic)
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
                                                        {{ isset($academic['academic_level']) && $academic['academic_level'] == $lvl ? 'selected' : '' }}>
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
                                                value="{{ $academic['school_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label>From Year</label>
                                            <input type="number" min="1900" max="2099"
                                                name="academic_history[{{ $index }}][from_year]"
                                                class="form-control" placeholder="From YYYY"
                                                value="{{ $academic['from_year'] ?? '' }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label>To Year</label>
                                            <input type="number" min="1900" max="2099"
                                                name="academic_history[{{ $index }}][to_year]"
                                                class="form-control" placeholder="To YYYY"
                                                value="{{ $academic['to_year'] ?? '' }}">
                                        </div>
                                        <div class="col-md-1">
                                            <label>Aggregate Score</label>
                                            <input type="text"
                                                name="academic_history[{{ $index }}][aggregate_score]"
                                                class="form-control" placeholder="Agg"
                                                value="{{ $academic['aggregate_score'] ?? '' }}">
                                        </div>
                                        <div class="col-md-1">
                                            <label>Grade</label>
                                            <input type="text" name="academic_history[{{ $index }}][grade]"
                                                class="form-control" placeholder="Grade"
                                                value="{{ $academic['grade'] ?? '' }}">
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
                    @else
                        <div class="card mb-3 academic-block">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Academic Level</label>
                                        <select name="academic_history[0][academic_level]" class="form-select">
                                            <option value="">-- Select --</option>
                                            @foreach (array_column(\App\Helpers\AcademicLevel::cases(), 'value') as $lvl)
                                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>School Name</label>
                                        <input type="text" name="academic_history[0][school_name]"
                                            class="form-control" placeholder="School">
                                    </div>
                                    <div class="col-md-2">
                                        <label>From Year</label>
                                        <input type="number" name="academic_history[0][from_year]"
                                            class="form-control" placeholder="From YYYY">
                                    </div>
                                    <div class="col-md-2">
                                        <label>To Year</label>
                                        <input type="number" name="academic_history[0][to_year]"
                                            class="form-control" placeholder="To YYYY">
                                    </div>
                                    <div class="col-md-1">
                                        <label>Aggregate Score</label>
                                        <input type="text" name="academic_history[0][aggregate_score]"
                                            class="form-control" placeholder="Agg">
                                    </div>
                                    <div class="col-md-1">
                                        <label>Grade</label>
                                        <input type="text" name="academic_history[0][grade]" class="form-control"
                                            placeholder="Grade">
                                    </div>
                                    <div class="col-md-2 align-self-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            id="add-academic">
                                            <i class="fas fa-plus me-1"></i> Add More
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
                        <div class="mb-3">
                            <label>Any Health Issues?</label>
                            <select name="has_health_issues" id="has_health_issues" class="form-select">
                                <option value="0" {{ old('has_health_issues') == '0' ? 'selected' : '' }}>No
                                </option>
                                <option value="1" {{ old('has_health_issues') == '1' ? 'selected' : '' }}>Yes
                                </option>
                            </select>
                        </div>
                        <div id="health-details" class="mt-2"
                            style="{{ old('has_health_issues') == '1' ? '' : 'display:none' }}">
                            <div class="mb-3">
                                <label>Health Condition Details</label>
                                <textarea name="health_issues" class="form-control" rows="3"
                                    placeholder="Describe condition / attach files below">{{ old('health_issues') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label>Upload Medical Reports</label>
                                <input type="file" name="medical_files[]" multiple class="form-control">
                                <small class="form-text text-muted">You can upload multiple files
                                    (PDF, JPG, PNG)</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Any Disciplinary Issues?</label>
                            <select name="has_disciplinary_issues" id="has_disciplinary_issues" class="form-select">
                                <option value="0" {{ old('has_disciplinary_issues') == '0' ? 'selected' : '' }}>
                                    No</option>
                                <option value="1" {{ old('has_disciplinary_issues') == '1' ? 'selected' : '' }}>
                                    Yes</option>
                            </select>
                        </div>
                        <div id="discipline-details" class="mt-2"
                            style="{{ old('has_disciplinary_issues') == '1' ? '' : 'display:none' }}">
                            <div class="mb-3">
                                <label>Action Taken</label>
                                <select name="disciplinary_issues" class="form-select">
                                    <option value="">-- Select Action --</option>
                                    @foreach (array_column(\App\Helpers\DisciplineAction::cases(), 'value') as $d)
                                        <option value="{{ $d }}"
                                            {{ old('disciplinary_issues') == $d ? 'selected' : '' }}>
                                            {{ $d }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Reason / Details</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Reason / details">{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="required-field">Career Aspiration</label>
                        <select name="aspiration" class="form-select">
                            <option value="">-- Select Aspiration --</option>
                            @foreach (array_column(\App\Helpers\CareerAspirations::cases(), 'value') as $c)
                                <option value="{{ $c }}" {{ old('aspiration') == $c ? 'selected' : '' }}>
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
                                    {{ old('best_done_subjects') == $s ? 'selected' : '' }}>
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
                            placeholder="Anything else you want the school to know">{{ old('additional_info') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-secondary prevBtn" type="button">
                        <i class="bi bi-arrow-left-circle-fill me-1"></i> Previous
                    </button>
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-paper-plane me-1"></i> Submit Application
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
<!-- Steps form -->
