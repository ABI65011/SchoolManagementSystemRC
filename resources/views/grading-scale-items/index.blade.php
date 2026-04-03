@extends('layouts.main')

@section('title', 'Grade Items - ' . $gradingScale->name->value ?? $gradingScale->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="m-0">
                <i class="fas fa-list mr-2"></i>
                Grade Items: {{ $gradingScale->name->value ?? $gradingScale->name }}
                <small class="text-muted">{{ str_replace('_', ' ', $gradingScale->type->value ?? $gradingScale->type) }}</small>
            </h3>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('grading-scales.show', $gradingScale) }}" class="btn btn-info">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Scale
            </a>
            <a href="{{ route('grading-scale-items.create', $gradingScale) }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>
                Add Grade Item
            </a>
        </div>
    </div>

    <!-- Scale Info Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-tag"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Scale Name</span>
                    <span class="info-box-number">{{ $gradingScale->name->value ?? $gradingScale->name }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Items</span>
                    <span class="info-box-number">{{ $gradingScale->items->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-star"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Status</span>
                    <span class="info-box-number">
                        @if($gradingScale->is_default)
                            <span class="badge text-bg-warning">Default</span>
                        @endif
                        @if($gradingScale->is_active)
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
                <span class="info-box-icon bg-danger"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Grade Range</span>
                    <span class="info-box-number">
                        {{ $gradingScale->items->min('min_mark') ?? 0 }} - {{ $gradingScale->items->max('max_mark') ?? 100 }}
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
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="60">Order</th>
                            <th>Grade Code</th>
                            <th>Min Mark</th>
                            <th>Max Mark</th>
                            <th>Range</th>
                            <th>Achievement Level</th>
                            <th>Descriptor</th>
                            <th>Points</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-items">
                        @forelse($gradingScale->items->sortBy('order') as $item)
                            <tr class="grade-item" data-id="{{ $item->id }}">
                                <td class="text-center">
                                    <span class="handle mr-1">
                                        <i class="fas fa-grip-vertical text-muted"></i>
                                    </span>
                                    <span class="badge text-bg-secondary">{{ $item->order }}</span>
                                </td>
                                <td><strong>{{ $item->grade_code }}</strong></td>
                                <td>{{ $item->min_mark }}</td>
                                <td>{{ $item->max_mark }}</td>
                                <td>
                                    <span class="badge text-bg-info">{{ $item->min_mark }} - {{ $item->max_mark }}</span>
                                </td>
                                <td>{{ $item->achievement_level ?? '—' }}</td>
                                <td>{{ $item->descriptor ?? '—' }}</td>
                                <td>{{ $item->points ?? '—' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('grading-scale-items.edit', $item) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('grading-scale-items.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this grade item?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-table fa-3x mb-3"></i>
                                    <br>
                                    No grade items found.
                                    <a href="{{ route('grading-scale-items.create', $gradingScale) }}">Add your first grade item</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        Drag the <i class="fas fa-grip-vertical"></i> handle to reorder grades. Lower order = higher grade.
                    </p>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-sm btn-success" id="saveOrderBtn">
                        <i class="fas fa-save mr-2"></i>
                        Save Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-eye mr-2"></i>
                Grade Preview
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="previewMark">Enter a mark to see the grade:</label>
                        <div class="input-group">
                            <input type="number" id="previewMark" class="form-control" min="0" max="100" step="0.5" placeholder="Enter mark (0-100)">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="previewBtn">
                                    <i class="fas fa-calculator"></i> Get Grade
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="previewResult" class="alert alert-secondary text-center" style="display: none;">
                        <h4 id="previewGrade" class="mb-0"></h4>
                        <p id="previewDescriptor" class="mb-0 text-muted"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Order Form -->
<form id="saveOrderForm" action="{{ route('grading-scale-items.reorder', $gradingScale) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="items" id="orderItems">
</form>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    $(document).ready(function() {

        var sortable = new Sortable(document.getElementById('sortable-items'), {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                $('#saveOrderBtn').removeClass('btn-success').addClass('btn-warning').html('<i class="fas fa-exclamation-triangle mr-2"></i> Save Order');
            }
        });


        $('#saveOrderBtn').click(function() {
            var items = [];
            $('#sortable-items tr').each(function(index) {
                items.push({
                    id: $(this).data('id'),
                    order: index
                });
            });

            $('#orderItems').val(JSON.stringify(items));

            $.ajax({
                url: $('#saveOrderForm').attr('action'),
                method: 'POST',
                data: $('#saveOrderForm').serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success('Order saved successfully');
                        $('#saveOrderBtn').removeClass('btn-warning').addClass('btn-success').html('<i class="fas fa-save mr-2"></i> Save Order');


                        $('#sortable-items tr').each(function(index) {
                            $(this).find('.text-bg-secondary').text(index);
                        });
                    } else {
                        toastr.error('Error saving order');
                    }
                },
                error: function() {
                    toastr.error('Error saving order');
                }
            });
        });


        $('#previewBtn').click(function() {
            var mark = parseFloat($('#previewMark').val());

            if (isNaN(mark) || mark < 0 || mark > 100) {
                toastr.warning('Please enter a valid mark between 0 and 100');
                return;
            }

            var grade = null;
            @foreach($gradingScale->items as $item)
                if (mark >= {{ $item->min_mark }} && mark <= {{ $item->max_mark }}) {
                    grade = {
                        code: '{{ $item->grade_code }}',
                        descriptor: '{{ $item->descriptor }}',
                        points: '{{ $item->points }}'
                    };
                }
            @endforeach

            if (grade) {
                var pointsText = grade.points ? ' (' + grade.points + ' points)' : '';
                $('#previewGrade').text('Grade: ' + grade.code + pointsText);
                $('#previewDescriptor').text(grade.descriptor);
                $('#previewResult').removeClass('alert-secondary').addClass('alert-success').show();
            } else {
                $('#previewGrade').text('No grade found');
                $('#previewDescriptor').text('Mark out of range');
                $('#previewResult').removeClass('alert-success').addClass('alert-secondary').show();
            }
        });

        
        $('#previewMark').keypress(function(e) {
            if (e.which === 13) {
                $('#previewBtn').click();
            }
        });
    });
</script>
@endsection
