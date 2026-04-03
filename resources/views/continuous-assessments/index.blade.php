@extends('layouts.main')

@section('title', 'Continuous Assessments')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="m-0">
                    <i class="fas fa-tasks mr-2"></i>
                    Continuous Assessments
                </h3>
                <p class="text-muted">20% Formative Assessment Component</p>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('continuous-assessments.create') }}" class="btn btn-success">
                    <i class="fas fa-plus mr-2"></i>
                    New Assessment
                </a>
                @if (request('class_id'))
                    <a href="{{ route('continuous-assessments.bulk-create', request('class_id')) }}"
                        class="btn btn-info ml-2">
                        <i class="fas fa-layer-group mr-2"></i>
                        Bulk Entry for {{ $classes->firstWhere('id', request('class_id'))?->name ?? 'Selected Class' }}
                    </a>
                @else
                    <a href="#" class="btn btn-secondary ml-2 disabled" data-toggle="tooltip"
                        title="Select a class first">
                        <i class="fas fa-layer-group mr-2"></i>
                        Bulk Entry
                    </a>
                @endif
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
                <form action="{{ route('continuous-assessments.index') }}" method="GET">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Subject</label>
                                <select name="subject_id" class="form-control select2">
                                    <option value="">All Subjects</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
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
                                    @foreach ($terms as $term)
                                        <option value="{{ $term->value }}"
                                            {{ request('term') == $term->value ? 'selected' : '' }}>
                                            Term {{ $term->value }}
                                        </option>
                                    @endforeach
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
                            <a href="{{ route('continuous-assessments.index') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-2"></i>
                                Reset Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Assessments Table -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Assessment Records</h3>
                <div class="card-tools">
                    <span class="badge text-bg-primary">{{ $assessments->total() }} records</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Assessment Type</th>
                                <th>Title</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Term/Year</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->id }}</td>
                                    <td>
                                        <strong>{{ $assessment->student->full_name ?? 'N/A' }}</strong>
                                        <br>
                                        <small
                                            class="text-muted">{{ $assessment->student->admission_number ?? '' }}</small>
                                    </td>
                                    <td>{{ $assessment->class->name ?? 'N/A' }}</td>
                                    <td>{{ $assessment->subject->name ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge text-bg-info">{{ $assessment->assessmentType?->name->value ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $assessment->title ?? '—' }}</td>
                                    <td>
                                        {{ number_format($assessment->raw_score, 2) }} /
                                        {{ number_format($assessment->max_score, 2) }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge text-bg-{{ $assessment->percentage >= 75 ? 'success' : ($assessment->percentage >= 50 ? 'warning' : 'danger') }}">
                                            {{ number_format($assessment->percentage, 2) }}%
                                        </span>
                                    </td>
                                    <td>Term {{ $assessment->term }}, {{ $assessment->year }}</td>
                                    <td>
                                        <small>{{ $assessment->recordedBy?->name }}</small>
                                        <br>
                                        <small class="text-muted">{{ $assessment->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('continuous-assessments.show', $assessment) }}"
                                                class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('continuous-assessments.edit', $assessment) }}"
                                                class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('continuous-assessments.destroy', $assessment) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this assessment?');">
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
                                    <td colspan="11" class="text-center py-4 text-muted">
                                        <i class="fas fa-tasks fa-3x mb-3"></i>
                                        <br>
                                        No continuous assessments found. Click "New Assessment" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $assessments->links() }}
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
