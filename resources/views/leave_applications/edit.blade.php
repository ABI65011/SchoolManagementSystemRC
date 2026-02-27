@extends('layouts.main')

@section('page-title', 'Edit Leave Application')

@section('content')
    @php
        $isAdmin = Auth::user()->hasAnyRole(['Admin', 'Super']);
        $isApplicant = Auth::user()->staff?->id === $leave_application->employee_id;
        $canEditReplacement =
            $isAdmin ||
            ($isApplicant &&
                $leave_application->replacement_status === \App\Helpers\ReplacementStatus::Rejected->value);
    @endphp

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">

                {{-- Replacement Rejected Alert --}}
                @if ($isApplicant && $leave_application->replacement_status === \App\Helpers\ReplacementStatus::Rejected->value)
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Action Required</h5>
                        The replacement employee has rejected your request. Please select a different replacement employee.
                    </div>
                @endif


                <div class="card {{ $isAdmin ? 'card-warning' : 'card-info' }} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit me-2"></i>
                            @if ($isAdmin)
                                Edit Application (Admin Mode)
                            @elseif($canEditReplacement)
                                Update Replacement Only
                            @else
                                View Application
                            @endif
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('leave.applications.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-list me-1"></i>View List
                            </a>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('leave.application.update', $leave_application->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            {{-- Employee --}}
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Employee</label>
                                @if ($isAdmin)
                                    <select id="employeeSelect" name="employee_id" class="form-select select2">
                                        <option value="">-- Select Employee --</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}"
                                                data-supervisor="{{ $emp->supervisor_id ?? '' }}"
                                                {{ old('employee_id', $leave_application->employee_id) == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="employee_id" value="{{ $leave_application->employee_id }}">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $leave_application->employee->user->name }}">
                                    </div>
                                @endif
                            </div>

                            {{-- Supervisor --}}
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Supervisor</label>
                                @if ($isAdmin)
                                    <select id="supervisorSelect" name="supervisor_id" class="form-select select2">
                                        <option value="">-- Select Supervisor --</option>
                                        @foreach ($supervisors as $sup)
                                            <option value="{{ $sup->id }}"
                                                {{ old('supervisor_id', $leave_application->supervisor_id) == $sup->id ? 'selected' : '' }}>
                                                {{ $sup->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="supervisor_id"
                                        value="{{ $leave_application->supervisor_id }}">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $leave_application->supervisor?->user->name ?? 'No Supervisor Assigned' }}">
                                    </div>
                                @endif
                            </div>

                            {{-- Replacement Employee --}}
                            <div
                                class="form-group mb-3 {{ $canEditReplacement ? 'border-start border-4 border-warning ps-3' : '' }}">
                                <label class="form-label fw-bold">
                                    Replacement Employee
                                    @if ($canEditReplacement && !$isAdmin)
                                        <span class="badge bg-warning text-dark ms-2">Editable</span>
                                    @endif
                                </label>
                                @if ($canEditReplacement)
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                        <select name="replacement_employee_id" class="form-select select2">
                                            <option value="">-- Select Replacement --</option>
                                            @foreach ($replacements as $rep)
                                                <option value="{{ $rep->id }}"
                                                    {{ old('replacement_employee_id', $leave_application->replacement_employee_id) == $rep->id ? 'selected' : '' }}>
                                                    {{ $rep->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $leave_application->replacement?->user->name ?? 'Not Assigned' }}">
                                    </div>
                                @endif
                            </div>

                            {{-- Leave Type --}}
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Leave Type</label>
                                @if ($isAdmin)
                                    <select name="type" id="type" class="form-select">
                                        <option value="">-- Select Leave Type --</option>
                                        @foreach ($leaveTypes as $type)
                                            <option value="{{ $type->value }}"
                                                {{ old('type', $leave_application->type) == $type->value ? 'selected' : '' }}>
                                                {{ $type->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="type" value="{{ $leave_application->type }}">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $leave_application->type }}">
                                    </div>
                                @endif
                            </div>

                            {{-- Other Reason --}}
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Other Reason</label>
                                @if ($isAdmin)
                                    <input type="text" name="other_reason" class="form-control"
                                        value="{{ old('other_reason', $leave_application->other_reason) }}">
                                @else
                                    <input type="hidden" name="other_reason"
                                        value="{{ $leave_application->other_reason }}">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $leave_application->other_reason }}">
                                    </div>
                                @endif
                            </div>

                            {{-- Date Range --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Start Date</label>
                                        @if ($isAdmin)
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ old('start_date', $leave_application->start_date?->format('Y-m-d')) }}">
                                        @else
                                            <input type="hidden" name="start_date"
                                                value="{{ $leave_application->start_date }}">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="date" class="form-control" readonly
                                                    value="{{ $leave_application->start_date?->format('Y-m-d') }}">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">End Date</label>
                                        @if ($isAdmin)
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ old('end_date', $leave_application->end_date?->format('Y-m-d')) }}">
                                        @else
                                            <input type="hidden" name="end_date"
                                                value="{{ $leave_application->end_date }}">
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="fas fa-calendar-check"></i></span>
                                                <input type="date" class="form-control" readonly
                                                    value="{{ $leave_application->end_date?->format('Y-m-d') }}">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Study Days Note --}}
                            {{-- <div id="studyNoteField" class="form-group mb-3" style="display: none;">
                                <label class="form-label fw-bold">Study Days Note</label>
                                @if ($isAdmin)
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-book"></i></span>
                                        <input type="text" name="study_days_note" class="form-control"
                                            value="{{ old('study_days_note', $leave_application->study_days_note) }}">
                                    </div>
                                @else
                                    <input type="hidden" name="study_days_note"
                                        value="{{ $leave_application->study_days_note }}">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-book"></i></span>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $leave_application->study_days_note }}">
                                    </div>
                                @endif
                            </div> --}}
                            <div id="studyNoteField" class="form-group mb-3" style="display: none;">
                                <label class="form-label fw-bold">Study Days Note</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-book"></i></span>
                                    <input type="text" name="study_days_note" class="form-control"
                                        value="{{ old('study_days_note') }}" placeholder="Enter study days details...">
                                </div>
                            </div>

                        </div>

                        @if ($isAdmin || $canEditReplacement)
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Application
                                </button>
                                <a href="{{ route('leave.applications.index') }}" class="btn btn-secondary float-end">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                        @endif
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
                $('#supervisorSelect').val(supervisorId ?? '').trigger('change');
            });

            
            @if ($leave_application->type == 'Study')
                $('#studyNoteField').show();
            @endif
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
