@extends('layouts.main')

@section('page-title', 'Add Leave Application')


@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-plus me-2"></i>Leave Application Form
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('leave.applications.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-list me-1"></i>View List
                            </a>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('leave.application.store') }}">
                        @csrf

                        <div class="card-body">

                            {{-- Employee --}}
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Employee <span class="text-danger">*</span></label>

                                @hasanyrole('Admin|Super')
                                    <select id="employeeSelect" name="employee_id"
                                        class="form-select select2 @error('employee_id') is-invalid @enderror">
                                        <option value="">-- Select Employee --</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}"
                                                data-supervisor="{{ $emp->supervisor_id ?? '' }}"
                                                {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <select id="employeeSelect" name="employee_id" class="form-select" readonly
                                            onmousedown="return false;">
                                            <option value="{{ $loggedInStaff->id }}" selected
                                                data-supervisor="{{ $loggedInStaff->supervisor_id ?? '' }}">
                                                {{ $loggedInStaff->user->name }}
                                            </option>
                                        </select>
                                    </div>
                                @endhasanyrole
                                @error('employee_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Supervisor --}}
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Supervisor</label>
                                <select id="supervisorSelect" name="supervisor_id"
                                    class="form-select select2 @error('supervisor_id') is-invalid @enderror">
                                    <option value="">-- Select Supervisor --</option>
                                    @foreach ($supervisors as $sup)
                                        <option value="{{ $sup->id }}"
                                            {{ old('supervisor_id') == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Replacement Employee --}}
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Replacement Employee</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                    <select name="replacement_employee_id"
                                        class="form-select select2 @error('replacement_employee_id') is-invalid @enderror">
                                        <option value="">-- Select Replacement --</option>
                                        @foreach ($employees as $emp)
                                            @if ($emp->id != $loggedInStaff->id)
                                                <option value="{{ $emp->id }}"
                                                    {{ old('replacement_employee_id') == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->user->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                @error('replacement_employee_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Leave Type --}}
                            <div class="form-group mb-3">
                                <label for="type" class="form-label fw-bold">Leave Type <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <select name="type" id="type"
                                        class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">-- Select Leave Type --</option>
                                        @foreach ($leaveTypes as $type)
                                            <option value="{{ $type->value }}"
                                                {{ old('type') == $type->value ? 'selected' : '' }}>
                                                {{ $type->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('type')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Study Days Note --}}
                            <div id="studyNoteField" class="form-group mb-3" style="display: none;">
                                <label class="form-label fw-bold">Study Days Note</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-book"></i></span>
                                    <input type="text" name="study_days_note" class="form-control"
                                        value="{{ old('study_days_note') }}" placeholder="Enter study days details...">
                                </div>
                            </div>

                            {{-- Other Reason --}}
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Other Reason</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <input type="text" name="other_reason" class="form-control"
                                        value="{{ old('other_reason') }}" placeholder="Enter reason if applicable...">
                                </div>
                            </div>

                            {{-- Date Range --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Start Date <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" name="start_date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                value="{{ old('start_date') }}" required>
                                        </div>
                                        @error('start_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">End Date <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                            <input type="date" name="end_date"
                                                class="form-control @error('end_date') is-invalid @enderror"
                                                value="{{ old('end_date') }}" required>
                                        </div>
                                        @error('end_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <a href="{{ route('leave.applications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary float-end">
                                <i class="fas fa-paper-plane me-1"></i>Submit Application
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection


@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {

            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });


            $('#employeeSelect').on('change', function() {
                let supervisorId = $(this).find(':selected').data('supervisor');
                let supervisorSelect = $('#supervisorSelect');

                if (supervisorId) {
                    supervisorSelect.val(supervisorId).trigger('change');
                    supervisorSelect.prop('disabled', true);
                } else {
                    supervisorSelect.val('').trigger('change');
                    supervisorSelect.prop('disabled', false);
                }
            });

        });
    </script>
    <script>
        document.getElementById("type").addEventListener("change", function() {
            let studyField = document.getElementById("studyNoteField");

            if (this.value === "Study") {
                studyField.style.display = "block";
            } else {
                studyField.style.display = "none";
            }
        });
    </script>
@endsection
