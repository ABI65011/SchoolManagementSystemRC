@extends('layouts.main')

@section('title', 'Exam Management')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="m-0">
                    <i class="fas fa-file-alt mr-2"></i>
                    Exam Management
                </h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('exams.create') }}" class="btn btn-success">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Exam
                </a>
                <a href="{{ route('exam-categories.index') }}" class="btn btn-info ml-2">
                    <i class="fas fa-tags mr-2"></i>
                    Manage Categories
                </a>
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
                <form action="{{ route('exams.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="class_id" class="form-control select2">
                                    <option value="">All Classes</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Term</label>
                                <select name="term" class="form-control">
                                    <option value="">All Terms</option>
                                    <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>Term 1</option>
                                    <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>Term 2</option>
                                    <option value="3" {{ request('term') == '3' ? 'selected' : '' }}>Term 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Year</label>
                                <select name="year" class="form-control">
                                    <option value="">All Years</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                    </option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                        Published</option>
                                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing
                                    </option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="results_released"
                                        {{ request('status') == 'results_released' ? 'selected' : '' }}>Results Released
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fas fa-filter mr-2"></i>
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('exams.index') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-2"></i>
                                Reset Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Exams Table -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Exams</h3>
                <div class="card-tools">
                    <span class="badge text-bg-primary">{{ $exams->total() }} records</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Exam Name</th>
                                <th>Code</th>
                                <th>Class</th>
                                <th>Term/Year</th>
                                <th>Category</th>
                                <th>Exam Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                                <tr>
                                    <td>{{ $exam->id }}</td>
                                    <td>
                                        <strong>{{ $exam->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $exam->code }}</small>
                                    </td>
                                    <td>{{ $exam->code }}</td>
                                    <td>{{ $exam->class->name ?? 'N/A' }}</td>
                                    <td>
                                        Term {{ $exam->term }}, {{ $exam->year }}
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $exam->isExternal() ? 'warning' : 'info' }}">
                                            {{ $exam->category_path }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($exam->exam_date)
                                            {{ $exam->exam_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">TBD</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge text-bg-{{ $exam->status === 'draft'
                                                ? 'secondary'
                                                : ($exam->status === 'published'
                                                    ? 'primary'
                                                    : ($exam->status === 'ongoing'
                                                        ? 'warning'
                                                        : ($exam->status === 'completed'
                                                            ? 'info'
                                                            : 'success'))) }}">
                                            {{ ucfirst(str_replace('_', ' ', $exam->status->value)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-primary"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('exam-results.create', $exam) }}"
                                                class="btn btn-sm btn-success" title="Enter Results">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning change-status-btn"
                                                data-id="{{ $exam->id }}" data-status="{{ $exam->status->value }}"
                                                title="Change Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <form action="{{ route('exams.destroy', $exam) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this exam?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <br>
                                        No exams found. Click "Create New Exam" to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $exams->links() }}
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Exam Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="statusForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status">Select New Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="results_released">Results Released</option>
                            </select>
                        </div>
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Changing status to "Completed" will allow results to be entered.
                            <br>
                            Changing to "Results Released" will make results visible to students.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            console.log('Document ready, jQuery loaded');

            $('.select2').select2({
                placeholder: "Select option",
                allowClear: true
            });


            var buttonCount = $('.change-status-btn').length;
            console.log('Found ' + buttonCount + ' status buttons');

            if (buttonCount === 0) {
                console.log('No buttons found! Check if elements have class "change-status-btn"');
            }


            $('.change-status-btn').click(function() {
                alert('Button clicked!');

                var examId = $(this).data('id');
                var currentStatus = $(this).data('status');
                var form = $('#statusForm');

                console.log('Exam ID:', examId, 'Current Status:', currentStatus);


                form.attr('action', '/exams/' + examId + '/update-status');


                form.find('select[name="status"]').val(currentStatus);

                
                $('#statusModal').modal('show');
            });
        });
    </script>
@endsection
