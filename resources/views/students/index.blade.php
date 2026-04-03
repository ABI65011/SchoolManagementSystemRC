@extends('layouts.main')
@section('title', 'Students')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Student List</h3>
                <div class="float-end">
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#bulkSponsorshipModal">
                        <i class="bi bi-tags"></i> Bulk Update Sponsorship
                    </button>
                    <a href="{{ route('students.government-sponsored') }}" class="btn btn-success btn-sm">
            <i class="fas fa-graduation-cap"></i> UPE/USE Students
        </a>
                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm ">Add Student</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $s)
                            <tr>
                                <td><input type="checkbox" class="student-checkbox" value="{{ $s->id }}"></td>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $s->first_name . ' ' . $s->last_name }}</td>
                                <td>{{ $s->dob }}</td>
                                <td>{{ $s->gender }}</td>
                                <td>
                                    <a href="{{ route('students.show', $s) }}" class="btn btn-sm btn-info"><i
                                            class="bi bi-eye-fill"></i> </a>
                                    <a href="{{ route('students.edit', $s) }}" class="btn btn-sm btn-warning"><i
                                            class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('students.destroy', $s) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i
                                                class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Bulk Sponsorship Modal -->
    <div class="modal fade" id="bulkSponsorshipModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="bulkSponsorshipForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Update Sponsorship</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="student_ids" id="bulkStudentIds">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Selected students: <strong id="selectedCount">0</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sponsorship Type</label>
                            <select name="sponsorship_type" id="bulkSponsorshipType" class="form-select" required>
                                <option value="private">Private</option>
                                <option value="government">Government (UPE/USE)</option>
                            </select>
                        </div>
                        <div class="mb-3" id="bulkReferenceField" style="display: none;">
                            <label class="form-label">Reference Prefix (Optional)</label>
                            <input type="text" name="reference_prefix" class="form-control" placeholder="e.g., UPE-2026">
                            <small class="text-muted">Will auto-generate reference numbers like UPE-2026-123</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Sponsorship</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Toggle Sponsorship Modal (Single Student) -->
    <div class="modal fade" id="singleSponsorshipModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="singleSponsorshipForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Update Sponsorship</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="student_id" id="singleStudentId">
                        <div class="mb-3">
                            <label class="form-label">Sponsorship Type</label>
                            <select name="sponsorship_type" id="singleSponsorshipType" class="form-select" required>
                                <option value="private">Private</option>
                                <option value="government">Government (UPE/USE)</option>
                            </select>
                        </div>
                        <div class="mb-3" id="singleReferenceField" style="display: none;">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control"
                                placeholder="e.g., UPE-2026-00123">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@section('javascript')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            function updateSelected() {
                let selectedIds = [];
                $('.student-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                console.log("Selected IDs:", selectedIds);

                $('#selectedCount').text(selectedIds.length);
                $('#bulkStudentIds').val(selectedIds.join(','));
                $('#bulkSponsorshipForm button[type="submit"]').prop('disabled', selectedIds.length === 0);
            }

            $(document).on('change', '#selectAll', function() {
                $('.student-checkbox').prop('checked', $(this).prop('checked'));
                updateSelected();
            });

            $(document).on('change', '.student-checkbox', function() {
                updateSelected();
            });

            $('#bulkSponsorshipModal').on('show.bs.modal', function() {
                updateSelected();
            });

            $('#bulkSponsorshipType').on('change', function() {
                $('#bulkReferenceField').toggle($(this).val() === 'government');
            });
            
            $('#bulkSponsorshipForm').on('submit', function(e) {
                e.preventDefault();

                let ids = $('#bulkStudentIds').val();
                if (!ids) {
                    alert('Please select students first');
                    return;
                }

                $.ajax({
                    url: '{{ route('students.bulk-sponsorship') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#bulkSponsorshipModal').modal('hide');
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Server error. Check your Laravel logs.');
                    }
                });
            });
        });
    </script>
@endsection
