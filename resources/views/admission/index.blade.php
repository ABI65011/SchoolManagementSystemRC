@extends('layouts.main')

@section('title', 'Admission Management')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="m-0">
                    <i class="fas fa-door-open mr-2"></i>
                    Admissions
                </h3>
            </div>
        </div>

        <!-- Filters -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admissions.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    @foreach (\App\Helpers\AdmissionStatus::cases() as $status)
                                        <option value="{{ $status->value }}"
                                            {{ request('status') == $status->value ? 'selected' : '' }}>
                                            {{ str_replace('_', ' ', $status->value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fas fa-filter mr-2"></i>
                                    Apply Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('admissions.index') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-2"></i>
                                Reset Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Admission Table -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Admissions</h3>
                <div class="card-tools">
                    <span class="badge text-bg-primary">{{ $admissions->count() }} records</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Comments</th>
                                <th>Admitted By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admissions as $a)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ ($a->student->first_name ?? '') . ' ' . ($a->student->middle_name ?? '') . ' ' . ($a->student->last_name ?? '') }}</strong>
                                    </td>
                                    <td>
                                        @if ($a->status->value === \App\Helpers\AdmissionStatus::Pending->value)
                                            <span class="badge text-bg-warning">
                                                {{ str_replace('_', ' ', $a->status->value) }}
                                            </span>
                                        @elseif ($a->status->value === \App\Helpers\AdmissionStatus::Admitted->value)
                                            <span class="badge text-bg-success">
                                                {{ str_replace('_', ' ', $a->status->value) }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-danger">
                                                {{ str_replace('_', ' ', $a->status->value) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $a->comments ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $a->user->name ?? '-' }}
                                    </td>
                                    <td>
                                        @if ($a->status === \App\Helpers\AdmissionStatus::Rejected)
                                            No actions Required
                                        @endif
                                        @if ($a->status === \App\Helpers\AdmissionStatus::Pending)
                                            <button type="button" class="btn btn-sm btn-warning"
                                                onclick="openActionModal({{ $a->id }})">
                                                <i class="fas fa-check"></i> Process
                                            </button>
                                            <a href="{{ route('students.show', $a->student) }}" class="btn btn-sm btn-success">View Profile</a>
                                        @endif
                                        @if ($a->status === \App\Helpers\AdmissionStatus::Admitted)
                                            <a href="{{ route('students.show', $a->student) }}" class="btn btn-sm btn-success">View Profile</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-door-open fa-3x mb-3"></i>
                                        <br>
                                        No Admissions have been made yet. Add Students to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- @if (method_exists($a, 'links'))
                <div class="mt-3">
                    {{ $a->links() }}
                </div>
            @endif --}}
        </div>
    </div>

    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="admissionActionForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="status" id="statusField">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="actionModalLabel">Update Admission Status</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="commentSection" style="display: none;" class="mb-3">
                            <label for="comments" class="form-label">Reason for Rejection</label>
                            <textarea name="comments" id="comments" class="form-control" rows="3"
                                placeholder="Explain why the application was rejected..."></textarea>
                        </div>

                        <div class="d-flex justify-content-around">
                            <button type="button" onclick="submitAdmit()" class="btn btn-success">
                                <i class="fas fa-check mr-2"></i> Confirm Admission
                            </button>
                            <button type="button" id="rejectBtn" onclick="handleReject()" class="btn btn-danger">
                                <i class="fas fa-times mr-2"></i> Reject
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Admit Confirmation Modal -->
<div class="modal fade" id="bulkAdmitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Bulk Admission</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>You are about to admit <span id="modalSelectedCount">0</span> students.</p>
                <p class="text-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    This action will:
                </p>
                <ul>
                    <li>Change their status to "Admitted"</li>
                    <li>Assign them to appropriate classes based on joining class</li>
                    <li>Assign them to random streams within their class</li>
                </ul>
                <p>Do you want to continue?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmBulkAdmit">
                    <i class="fas fa-check-double mr-2"></i>
                    Confirm Bulk Admit
                </button>
            </div>
        </div>
    </div>
</div>




@endsection

@section('javascript')
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select option",
                allowClear: true
            });
        });
    </script>

    <script>
        let currentForm = document.getElementById('admissionActionForm');
        let isRejectMode = false;

        function openActionModal(admissionId) {
            currentForm.action = '/admissions/' + admissionId;


            isRejectMode = false;
            document.getElementById('commentSection').style.display = 'none';
            document.getElementById('comments').value = '';
            document.getElementById('statusField').value = '';
            document.getElementById('actionModalLabel').innerText = 'Update Admission Status';


            const rejectBtn = document.getElementById('rejectBtn');
            rejectBtn.innerHTML = '<i class="fas fa-times mr-2"></i> Reject';
            rejectBtn.classList.remove('btn-warning');
            rejectBtn.classList.add('btn-danger');


            var myModal = new bootstrap.Modal(document.getElementById('actionModal'));
            myModal.show();
        }

        function submitAdmit() {
            document.getElementById('statusField').value = 'admitted';
            console.log('Submitting Admission...');
            currentForm.submit();
        }

        function handleReject() {
            if (!isRejectMode) {

                document.getElementById('commentSection').style.display = 'block';
                document.getElementById('statusField').value = 'rejected';
                document.getElementById('actionModalLabel').innerText = 'Rejecting Admission...';


                const rejectBtn = document.getElementById('rejectBtn');
                rejectBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Confirm Rejection';
                rejectBtn.classList.remove('btn-danger');
                rejectBtn.classList.add('btn-warning');

                isRejectMode = true;
            } else {

                const comments = document.getElementById('comments').value.trim();
                if (!comments) {
                    alert('Please provide a reason for rejection');
                    return;
                }

                console.log('Submitting Rejection with comments...'); 
                currentForm.submit();
            }
        }
    </script>
@endsection
