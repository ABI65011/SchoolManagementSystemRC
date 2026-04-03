@extends('layouts.main')

@section('title', $gradingScale->name->value . ' - Grading Scale')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <h3 class="m-0">
                    <i class="fas fa-balance-scale mr-2"></i>
                    {{ $gradingScale->name->value }}
                    <small class="text-muted">{{ str_replace('_', ' ', $gradingScale->type->value) }}</small>
                </h3>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('grading-scales.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
                <a href="{{ route('grading-scales.edit', $gradingScale) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Scale
                </a>
                <a href="{{ route('grading-scale-items.create', $gradingScale) }}" class="btn btn-success">
                    <i class="fas fa-plus mr-2"></i>
                    Add Grade
                </a>
            </div>
        </div>

        <!-- Scale Info -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Scale Name</span>
                        <span class="info-box-number">{{ $gradingScale->name->value }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Grade Items</span>
                        <span class="info-box-number">{{ $gradingScale->items->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-star"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Status</span>
                        <span class="info-box-number">
                            @if ($gradingScale->is_default)
                                <span class="badge text-bg-warning">Default Scale</span>
                            @endif
                            @if ($gradingScale->is_active)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-secondary">Inactive</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade Items Table -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-2"></i>
                    Grade Items
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="gradeItemsTable">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Grade Code</th>
                                <th>Min Mark</th>
                                <th>Max Mark</th>
                                <th>Range</th>
                                <th>Achievement Level</th>
                                <th>Descriptor</th>
                                <th>Points</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @forelse($gradingScale->items as $item)
                                <tr class="grade-item-row" data-id="{{ $item->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $item->grade_code }}</strong>
                                    </td>
                                    <td>{{ $item->min_mark }}</td>
                                    <td>{{ $item->max_mark }}</td>
                                    <td>
                                        <span class="badge text-bg-info">{{ $item->min_mark }} -
                                            {{ $item->max_mark }}</span>
                                    </td>
                                    <td>
                                        @if ($item->achievement_level)
                                            <span
                                                class="badge text-bg-{{ $item->achievement_level === 'Excellent'
                                                    ? 'success'
                                                    : ($item->achievement_level === 'Good'
                                                        ? 'primary'
                                                        : ($item->achievement_level === 'Average'
                                                            ? 'info'
                                                            : ($item->achievement_level === 'Pass'
                                                                ? 'warning'
                                                                : 'secondary'))) }}">
                                                {{ $item->achievement_level }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->descriptor ?? '—' }}</td>
                                    <td>{{ $item->points ?? '—' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('grading-scale-items.edit', $item) }}"
                                                class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('grading-scale-items.destroy', $item) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this grade item?');">
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
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="fas fa-table fa-3x mb-3"></i>
                                        <br>
                                        No grade items found. Click "Add Grade" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
    </form>
@endsection

@section('javascript')
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select option",
                allowClear: true
            });

            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
