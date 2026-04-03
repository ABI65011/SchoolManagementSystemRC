@extends('layouts.main')

@section('title', $exam->name)
@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="m-0">
                <i class="fas fa-file-alt mr-2"></i>
                {{ $exam->name }}
                <small class="text-muted">{{ $exam->code }}</small>
            </h3>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('exams.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
            <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('exam-results.create', $exam) }}" class="btn btn-success">
                <i class="fas fa-pen mr-2"></i>
                Enter Results
            </a>
        </div>
    </div>

    <!-- Exam Details -->
    <div class="row">
        <div class="col-md-8">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $statistics['total_students'] }}</h3>
                            <p>Total Students</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $statistics['submitted_count'] }}</h3>
                            <p>Results Submitted</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $statistics['pending_count'] }}</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($statistics['pass_rate'], 2) }}%</h3>
                            <p>Pass Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Student Results</h3>
                    <div class="card-tools">
                        <span class="badge text-bg-primary">{{ $resultsByStudent->count() }} students</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    {{-- <th>Admission No.</th> --}}
                                    @php $subjects = $exam->results->groupBy('subject_id'); @endphp
                                    @foreach($subjects->keys() as $subjectId)
                                        <th>{{ \App\Models\Subject::find($subjectId)?->code ?? 'Subj'.$subjectId }}</th>
                                    @endforeach
                                    <th>Average</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resultsByStudent as $studentId => $studentResults)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $studentResults->first()->student->full_name ?? 'N/A' }}</strong>
                                        </td>
                                        {{-- <td>{{ $studentResults->first()->student->admission_number ?? 'N/A' }}</td> --}}
                                        @php $total = 0; @endphp
                                        @foreach($subjects->keys() as $subjectId)
                                            @php
                                                $result = $studentResults->where('subject_id', $subjectId)->first();
                                                $total += $result?->final_mark ?? 0;
                                            @endphp
                                            <td>
                                                @if($result)
                                                    <span class="badge text-bg-{{ $result->final_mark >= 50 ? 'success' : 'danger' }}">
                                                        {{ number_format($result->final_mark, 2) }}
                                                    </span>
                                                    <br>
                                                    <small>{{ $result->grade }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            <strong>{{ number_format($total / max(1, $subjects->count()), 2) }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('exam-results.edit', $studentResults->first()) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 5 + $subjects->count() }}" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <br>
                                            No results entered yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pending Students -->
            @if($pendingStudents->isNotEmpty())
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Pending Students</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody>
                            @foreach($pendingStudents as $student)
                                <tr>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->admission_number }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('exam-results.create', $exam) }}" class="btn btn-xs btn-success">
                                            <i class="fas fa-plus"></i> Enter Results
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Exam Info Card -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Exam Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Category:</th>
                            <td>
                                <span class="badge text-bg-info">{{ $exam->category_path }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>
                                <span class="badge text-bg-{{ $exam->isExternal() ? 'warning' : 'success' }}">
                                    {{ $exam->isExternal() ? 'External' : 'Internal' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Class:</th>
                            <td>{{ $exam->class->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Term/Year:</th>
                            <td>Term {{ $exam->term }}, {{ $exam->year }}</td>
                        </tr>
                        <tr>
                            <th>Exam Date:</th>
                            <td>{{ $exam->exam_date?->format('d M Y') ?? 'TBD' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge text-bg-{{
                                    $exam->status === 'draft' ? 'secondary' :
                                    ($exam->status === 'published' ? 'primary' :
                                    ($exam->status === 'ongoing' ? 'warning' :
                                    ($exam->status === 'completed' ? 'info' : 'success')))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $exam->status->value)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Grading Scale:</th>
                            <td>{{ $exam->gradingScale?->name ?? 'Default' }}</td>
                        </tr>
                        <tr>
                            <th>Max Mark:</th>
                            <td>{{ $exam->max_mark }}</td>
                        </tr>
                        <tr>
                            <th>Weight:</th>
                            <td>{{ $exam->weight }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Average Mark:</th>
                            <td>{{ number_format($statistics['average_mark'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Highest Mark:</th>
                            <td>{{ number_format($statistics['highest_mark'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Lowest Mark:</th>
                            <td>{{ number_format($statistics['lowest_mark'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Submission Rate:</th>
                            <td>
                                @php $submissionRate = $statistics['total_students'] > 0 ? ($statistics['submitted_count'] / $statistics['total_students'] * 100) : 0; @endphp
                                {{ number_format($submissionRate, 2) }}%
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Actions Card -->
            {{-- <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('exam-results.create', $exam) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-pen mr-2"></i> Enter/Edit Results
                        </a>
                        <a href="{{ route('exam-results.upload', $exam) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-upload mr-2"></i> Bulk Upload Results
                        </a>
                        <a href="{{ route('exam-results.template', $exam) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-download mr-2"></i> Download Template
                        </a>
                        @if($exam->status === 'completed')
                        <form action="{{ route('exam-results.release', $exam) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action list-group-item-success"
                                    onclick="return confirm('Release results? Students will be able to view them.')">
                                <i class="fas fa-check-circle mr-2"></i> Release Results
                            </button>
                        </form>
                        @endif
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#statusModal">
                            <i class="fas fa-sync-alt mr-2"></i> Change Status
                        </a>
                    </div>
                </div>
            </div> --}}

            @if($exam->description)
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Description</h3>
                </div>
                <div class="card-body">
                    {{ $exam->description }}
                </div>
            </div>
            @endif

            @if($exam->instructions)
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Instructions</h3>
                </div>
                <div class="card-body">
                    {{ $exam->instructions }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Exam Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('exams.update-status', $exam) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>New Status</label>
                        <select name="status" class="form-control" required>
                            <option value="draft" {{ $exam->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $exam->status === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="ongoing" {{ $exam->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ $exam->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="results_released" {{ $exam->status === 'results_released' ? 'selected' : '' }}>Results Released</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
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
        $('.select2').select2({
            placeholder: "Select option",
            allowClear: true
        });
    });
</script>
@endsection
