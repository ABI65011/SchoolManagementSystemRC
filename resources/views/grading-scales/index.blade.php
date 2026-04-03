@extends('layouts.main')

@section('title', 'Grading Scales Management')
@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3 class="m-0">
                <i class="fas fa-balance-scale mr-2"></i>
                Grading Scales
            </h3>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('grading-scales.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>
                Create New Scale
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
            <form action="{{ route('grading-scales.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                @foreach(\App\Helpers\GradingScaleType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $type->value) }}
                                    </option>
                                @endforeach
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
                        <a href="{{ route('grading-scales.index') }}" class="btn btn-default">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scales Table -->
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title">Grading Scales</h3>
            <div class="card-tools">
                <span class="badge text-bg-primary">{{ $scales->count() }} records</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Scale Name</th>
                            <th>Type</th>
                            <th>Grade Items</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scales as $scale)
                            <tr>
                                <td>{{ $scale->id }}</td>
                                <td>
                                    <strong>{{ $scale->name->value }}</strong>
                                </td>
                                <td>
                                    <span class="badge text-bg-info">{{ str_replace('_', ' ', $scale->type->value) }}</span>
                                </td>
                                <td>
                                    <span class="badge text-bg-primary">{{ $scale->items_count }} grades</span>
                                    <a href="{{ route('grading-scales.show', $scale) }}" class="btn btn-sm btn-info ml-2">
                                        <i class="fas fa-list"></i>
                                    </a>
                                </td>
                                <td>
                                    @if($scale->is_active)
                                        <span class="badge text-bg-success">Active</span>
                                    @else
                                        <span class="badge text-bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($scale->is_default)
                                        <span class="badge text-bg-warning">
                                            <i class="fas fa-star"></i> Default
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('grading-scales.show', $scale) }}" class="btn btn-sm btn-info" title="View Grades">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <a href="{{ route('grading-scales.edit', $scale) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$scale->is_default)
                                            <a href="{{ route('grading-scales.set-default', $scale) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Set as Default"
                                               onclick="return confirm('Set this as the default grading scale?')">
                                                <i class="fas fa-star"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('grading-scales.destroy', $scale) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this grading scale?');">
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
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-balance-scale fa-3x mb-3"></i>
                                    <br>
                                    No grading scales found. Click "Create New Scale" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Only add pagination if using paginate() --}}
            @if(method_exists($scales, 'links'))
                <div class="mt-3">
                    {{ $scales->links() }}
                </div>
            @endif
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
