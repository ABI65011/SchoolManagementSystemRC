@extends('layouts.main')

@section('title', $examCategory->name . ' - Exam Category')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="m-0">
                <i class="fas fa-tags mr-2"></i>
                {{ $examCategory->name }}
                @if(!$examCategory->main_category_id)
                    <span class="badge text-bg-primary ml-2">Main Category</span>
                @endif
            </h3>
            <p class="text-muted">{{ $examCategory->full_path }}</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('exam-categories.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
            <a href="{{ route('exam-categories.edit', $examCategory) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('exams.create', ['exam_category_id' => $examCategory->id]) }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>
                New Exam
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-tag"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Category Type</span>
                    <span class="info-box-number">
                        <span class="badge text-bg-{{ $examCategory->exam_type === 'internal' ? 'success' : 'warning' }}">
                            {{ ucfirst($examCategory->exam_type) }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-file-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Exams</span>
                    <span class="info-box-number">{{ $examCategory->exams->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-star"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Status</span>
                    <span class="info-box-number">
                        @if($examCategory->is_active)
                            <span class="badge text-bg-success">Active</span>
                        @else
                            <span class="badge text-bg-secondary">Inactive</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-weight-hanging"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Weight</span>
                    <span class="info-box-number">{{ $examCategory->weight }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Category Details -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Category Details</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">ID</th>
                            <td>{{ $examCategory->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $examCategory->name }}</td>
                        </tr>
                        <tr>
                            <th>Full Path</th>
                            <td><span class="badge text-bg-info">{{ $examCategory->full_path }}</span></td>
                        </tr>
                        <tr>
                            <th>Main Category</th>
                            <td>
                                @if($examCategory->mainCategory)
                                    <a href="{{ route('exam-categories.show', $examCategory->mainCategory) }}">
                                        {{ $examCategory->mainCategory->name }}
                                    </a>
                                @else
                                    <span class="text-muted">— Top Level —</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Exam Type</th>
                            <td>
                                <span class="badge text-bg-{{ $examCategory->exam_type === 'internal' ? 'success' : 'warning' }}">
                                    {{ ucfirst($examCategory->exam_type) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Requires CA</th>
                            <td>
                                @if($examCategory->requires_continuous_assessment)
                                    <span class="badge text-bg-success">Yes (20% component)</span>
                                @else
                                    <span class="badge text-bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Weight</th>
                            <td>{{ $examCategory->weight }}</td>
                        </tr>
                        <tr>
                            <th>Sort Order</th>
                            <td>{{ $examCategory->sort_order }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $examCategory->description ?? 'No description' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $examCategory->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $examCategory->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Sub Categories -->
            @if($examCategory->subCategories->isNotEmpty())
            <div class="card card-outline card-success mb-3">
                <div class="card-header">
                    <h3 class="card-title">Sub-Categories</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examCategory->subCategories as $sub)
                                <tr>
                                    <td>
                                        <a href="{{ route('exam-categories.show', $sub) }}">
                                            {{ $sub->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $sub->exam_type === 'internal' ? 'success' : 'warning' }}">
                                            {{ ucfirst($sub->exam_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($sub->is_active)
                                            <span class="badge text-bg-success">Active</span>
                                        @else
                                            <span class="badge text-bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('exam-categories.edit', $sub) }}" class="btn btn-xs btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Recent Exams -->
            @if($examCategory->exams->isNotEmpty())
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Recent Exams</h3>
                    <div class="card-tools">
                        <a href="{{ route('exams.index', ['exam_category_id' => $examCategory->id]) }}" class="btn btn-xs btn-info">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Class</th>
                                <th>Term/Year</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examCategory->exams as $exam)
                                <tr>
                                    <td>
                                        <a href="{{ route('exams.show', $exam) }}">
                                            {{ $exam->name }}
                                        </a>
                                    </td>
                                    <td>{{ $exam->class->name ?? 'N/A' }}</td>
                                    <td>Term {{ $exam->term }}, {{ $exam->year }}</td>
                                    <td>
                                        <span class="badge text-bg-{{ $exam->status === 'published' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
