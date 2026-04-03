@extends('layouts.main')

@section('title', 'Exam Categories')
@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3 class="m-0">
                <i class="fas fa-tags mr-2"></i>
                Exam Categories
            </h3>
            <p class="text-muted">Manage exam categories and sub-categories</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('exam-categories.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>
                New Category
            </a>
            <a href="{{ route('exams.index') }}" class="btn btn-info ml-2">
                <i class="fas fa-file-alt mr-2"></i>
                Exams
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
            <form action="{{ route('exam-categories.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Exam Type</label>
                            <select name="exam_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="internal" {{ request('exam_type') == 'internal' ? 'selected' : '' }}>Internal</option>
                                <option value="external" {{ request('exam_type') == 'external' ? 'selected' : '' }}>External</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="">All</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">
                                <i class="fas fa-filter mr-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('exam-categories.index') }}" class="btn btn-default">
                            <i class="fas fa-undo mr-2"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title">All Categories</h3>
            <div class="card-tools">
                <span class="badge text-bg-primary">{{ $categories->total() }} records</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Path</th>
                            <th>Type</th>
                            <th>CA Required</th>
                            <th>Weight</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                    @if(!$category->main_category_id)
                                        <span class="badge text-bg-primary">Main</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge text-bg-info">{{ $category->full_path }}</span>
                                </td>
                                <td>
                                    <span class="badge text-bg-{{ $category->exam_type === 'internal' ? 'success' : 'warning' }}">
                                        {{ ucfirst($category->exam_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($category->requires_continuous_assessment)
                                        <span class="badge text-bg-success">Yes</span>
                                    @else
                                        <span class="badge text-bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>{{ $category->weight }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge text-bg-success">Active</span>
                                    @else
                                        <span class="badge text-bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('exam-categories.show', $category) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('exam-categories.edit', $category) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('exam-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" {{ $category->subCategories->count() > 0 || $category->exams->count() > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-tags fa-3x mb-3"></i>
                                    <br>
                                    No exam categories found. Click "New Category" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            {{ $categories->links() }}
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
