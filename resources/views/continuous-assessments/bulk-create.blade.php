{{-- resources/views/continuous-assessments/bulk-create.blade.php --}}
@extends('layouts.main')

@section('title', 'Bulk Continuous Assessment Entry')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        .score-input {
            width: 80px;
            text-align: center;
            font-weight: bold;
        }

        .score-input.passing {
            background-color: #d4edda;
            border-color: #28a745;
        }

        .score-input.failing {
            background-color: #f8d7da;
            border-color: #dc3545;
        }

        .student-row:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }

        .student-row.selected {
            background-color: #e3f2fd;
            border-left: 3px solid #007bff;
        }

        .percentage-badge {
            display: inline-block;
            width: 60px;
            text-align: center;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .percentage-badge.high {
            background-color: #28a745;
            color: white;
        }

        .percentage-badge.medium {
            background-color: #ffc107;
            color: #856404;
        }

        .percentage-badge.low {
            background-color: #dc3545;
            color: white;
        }

        .stats-card {
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .quick-fill-buttons {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }

        .quick-fill-buttons .btn {
            padding: 2px 8px;
            font-size: 11px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <h3 class="m-0">
                    <i class="fas fa-layer-group mr-2"></i>
                    Bulk Continuous Assessment Entry
                </h3>
                <p class="text-muted">
                    Class: <strong>{{ $student->current_class_name ?? 'N/A' }}</strong> |
                    Total Students: <strong>{{ $students->count() }}</strong>
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('continuous-assessments.index', ['class_id' => $class->id]) }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-info stats-card">
                    <div class="inner">
                        <h3 id="totalStudents">{{ $students->count() }}</h3>
                        <p>Total Students</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success stats-card">
                    <div class="inner">
                        <h3 id="enteredCount">0</h3>
                        <p>Scores Entered</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning stats-card">
                    <div class="inner">
                        <h3 id="pendingCount">{{ $students->count() }}</h3>
                        <p>Pending</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger stats-card">
                    <div class="inner">
                        <h3 id="avgPercentage">0%</h3>
                        <p>Class Average</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>
                    Enter Scores for {{ $class->name }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <form action="{{ route('continuous-assessments.bulk-store', $class) }}" method="POST" id="bulkAssessmentForm">
                @csrf
                <div class="card-body">
                    <!-- Common Fields -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="subject_id">Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" id="subject_id"
                                    class="form-control select2 @error('subject_id') is-invalid @enderror" required>
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }} ({{ $subject->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="assessment_type_id">Assessment Type <span class="text-danger">*</span></label>
                                <select name="assessment_type_id" id="assessment_type_id"
                                    class="form-control select2 @error('assessment_type_id') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    @foreach ($assessmentTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('assessment_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name->value ?? $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assessment_type_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="title">Assessment Title</label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                    placeholder="e.g., End of Topic Test">
                                @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="max_score">Max Score <span class="text-danger">*</span></label>
                                <input type="number" name="max_score" id="max_score"
                                    class="form-control @error('max_score') is-invalid @enderror"
                                    value="{{ old('max_score', 100) }}" min="1" max="100" step="1"
                                    required>
                                @error('max_score')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="term">Term <span class="text-danger">*</span></label>
                                <select name="term" id="term"
                                    class="form-control @error('term') is-invalid @enderror" required>
                                    <option value="">Select Term</option>
                                    @foreach ($terms as $term)
                                        <option value="{{ $term->value }}"
                                            {{ old('term') == $term->value ? 'selected' : '' }}>
                                            Term {{ $term->value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('term')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year">Year <span class="text-danger">*</span></label>
                                <select name="year" id="year"
                                    class="form-control @error('year') is-invalid @enderror" required>
                                    <option value="">Select Year</option>
                                    @foreach (range(now()->year - 2, now()->year + 2) as $year)
                                        <option value="{{ $year }}"
                                            {{ old('year', now()->year) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('year')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quick Fill Options</label>
                                <div class="quick-fill-buttons">
                                    <button type="button" class="btn btn-sm btn-success" id="fillAllPass">
                                        <i class="fas fa-check-circle"></i> Pass (50%)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info" id="fillAllGood">
                                        <i class="fas fa-star"></i> Good (70%)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" id="fillAllExcellent">
                                        <i class="fas fa-trophy"></i> Excellent (85%)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" id="clearAll">
                                        <i class="fas fa-eraser"></i> Clear All
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Scores Table -->
                    <div class="card card-outline card-secondary mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Student Scores</h3>
                            <div class="card-tools">
                                <span class="badge text-bg-primary">{{ $students->count() }} students</span>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="scoresTable">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAllStudents" title="Select All">
                                            </th>
                                            <th>#</th>
                                            <th>Student</th>
                                            {{-- <th>Admission No.</th> --}}
                                            <th>Stream</th>
                                            <th width="120">Score</th>
                                            <th width="100">Percentage</th>
                                            <th>Comment</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $index => $student)
                                            <tr class="student-row" data-student-id="{{ $student->id }}"
                                                data-index="{{ $index }}">
                                                <td class="text-center">
                                                    <input type="checkbox" class="student-checkbox"
                                                        value="{{ $student->id }}">
                                                </td>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $student->full_name }}</strong>
                                                    <input type="hidden" name="scores[{{ $index }}][student_id]"
                                                        value="{{ $student->id }}">
                                                </td>
                                                {{-- <td>{{ $student->admission_number }}</td> --}}
                                                <td>
                                                    @if (isset($student->pivot->stream))
                                                        <span class="badge text-bg-info">Stream
                                                            {{ $student->pivot->stream }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number"
                                                            name="scores[{{ $index }}][raw_score]"
                                                            class="form-control form-control-sm score-input"
                                                            data-student="{{ $student->id }}"
                                                            data-index="{{ $index }}"
                                                            data-max="{{ old('max_score', 100) }}" min="0"
                                                            max="100" step="0.5" placeholder="Score"
                                                            value="{{ old('scores.' . $index . '.raw_score') }}">
                                                        <div class="input-group-append">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-secondary quick-score"
                                                                data-score="100" title="Max">
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="percentage-badge"
                                                        id="percentage-{{ $index }}">0%</span>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="scores[{{ $index }}][teacher_comment]"
                                                        class="form-control form-control-sm"
                                                        placeholder="Optional comment"
                                                        value="{{ old('scores.' . $index . '.teacher_comment') }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Only students with scores entered will be saved. Leave blank to skip.</li>
                            <li>Use the quick fill buttons to populate all scores at once.</li>
                            <li>Select individual students using checkboxes for targeted filling.</li>
                            <li>Press <kbd>Tab</kbd> to move between fields, <kbd>Enter</kbd> to submit.</li>
                        </ul>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>
                                    Save All Assessments
                                </button>
                                <button type="button" class="btn btn-success" id="saveSelectedBtn">
                                    <i class="fas fa-check-double mr-2"></i>
                                    Save Selected
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('continuous-assessments.index') }}" class="btn btn-default">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
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

            let maxScore = parseInt($('#max_score').val()) || 100;
            let enteredCount = 0;
            let totalStudents = {{ $students->count() }};


            function updatePercentage(index, score, max) {
                var percentage = (score / max) * 100;
                var badge = $('#percentage-' + index);
                badge.text(percentage.toFixed(1) + '%');


                badge.removeClass('high medium low');
                if (percentage >= 70) {
                    badge.addClass('high');
                } else if (percentage >= 50) {
                    badge.addClass('medium');
                } else if (percentage > 0) {
                    badge.addClass('low');
                }


                var input = $('input[name="scores[' + index + '][raw_score]"]');
                input.removeClass('passing failing');
                if (score >= 50) {
                    input.addClass('passing');
                } else if (score > 0) {
                    input.addClass('failing');
                }

                return percentage;
            }


            function updateStats() {
                var entered = 0;
                var totalPercentage = 0;
                var count = 0;

                $('.score-input').each(function() {
                    var val = parseFloat($(this).val());
                    if (!isNaN(val) && val !== '' && val !== null) {
                        entered++;
                        var max = $(this).data('max') || maxScore;
                        var percentage = (val / max) * 100;
                        totalPercentage += percentage;
                        count++;
                    }
                });

                enteredCount = entered;
                var pendingCount = totalStudents - enteredCount;
                var avgPercentage = count > 0 ? (totalPercentage / count).toFixed(1) : 0;

                $('#enteredCount').text(enteredCount);
                $('#pendingCount').text(pendingCount);
                $('#avgPercentage').text(avgPercentage + '%');
            }


            $('#max_score').on('change', function() {
                maxScore = parseInt($(this).val()) || 100;


                $('.score-input').each(function() {
                    $(this).data('max', maxScore);
                    var val = parseFloat($(this).val());
                    if (!isNaN(val) && val !== '') {
                        var index = $(this).data('index');
                        updatePercentage(index, val, maxScore);
                    }
                });
                updateStats();
            });


            $('.score-input').each(function() {
                var val = parseFloat($(this).val());
                var index = $(this).data('index');
                var max = $(this).data('max') || maxScore;
                if (!isNaN(val) && val !== '') {
                    updatePercentage(index, val, max);
                }
                $(this).on('input', function() {
                    var val = parseFloat($(this).val()) || 0;
                    var idx = $(this).data('index');
                    var maxVal = $(this).data('max') || maxScore;
                    updatePercentage(idx, val, maxVal);
                    updateStats();
                });
            });

            updateStats();


            $('#fillAllPass').click(function() {
                var max = maxScore;
                var targetScore = max * 0.5;
                $('.score-input').each(function() {
                    $(this).val(targetScore.toFixed(1)).trigger('input');
                });
            });

            $('#fillAllGood').click(function() {
                var max = maxScore;
                var targetScore = max * 0.7;
                $('.score-input').each(function() {
                    $(this).val(targetScore.toFixed(1)).trigger('input');
                });
            });

            $('#fillAllExcellent').click(function() {
                var max = maxScore;
                var targetScore = max * 0.85;
                $('.score-input').each(function() {
                    $(this).val(targetScore.toFixed(1)).trigger('input');
                });
            });

            $('#clearAll').click(function() {
                $('.score-input').val('').trigger('input');
                $('.teacher-comment').val('');
            });


            $('#selectAllStudents').change(function() {
                var isChecked = $(this).prop('checked');
                $('.student-checkbox').prop('checked', isChecked);
                $('.student-row').toggleClass('selected', isChecked);
            });

            $('.student-checkbox').change(function() {
                var studentId = $(this).val();
                var row = $('tr[data-student-id="' + studentId + '"]');
                row.toggleClass('selected', $(this).prop('checked'));

                var allChecked = $('.student-checkbox:checked').length === $('.student-checkbox').length;
                $('#selectAllStudents').prop('checked', allChecked);
            });

            $('.student-row').click(function(e) {
                if ($(e.target).is('input') || $(e.target).is('button')) return;
                var checkbox = $(this).find('.student-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked'));
                $(this).toggleClass('selected', checkbox.prop('checked'));

                var allChecked = $('.student-checkbox:checked').length === $('.student-checkbox').length;
                $('#selectAllStudents').prop('checked', allChecked);
            });


            $('.quick-score').click(function(e) {
                e.stopPropagation();
                var score = $(this).data('score');
                var input = $(this).closest('td').find('.score-input');
                input.val(score).trigger('input');
            });


            $('#saveSelectedBtn').click(function() {
                var selectedIds = [];
                $('.student-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    toastr.warning('Please select at least one student');
                    return;
                }

                var form = $('#bulkAssessmentForm');
                var originalAction = form.attr('action');
                var formData = new FormData(form[0]);


                var keysToKeep = [];
                for (var pair of formData.entries()) {
                    if (pair[0] === 'scores') {
                        var match = pair[1].match(/scores\[(\d+)\]\[/);
                        if (match) {
                            var index = parseInt(match[1]);
                            var studentId = $('input[name="scores[' + index + '][student_id]"]').val();
                            if (selectedIds.includes(studentId)) {
                                keysToKeep.push(pair[0]);
                            }
                        }
                    }
                }

                $('#loadingModal').modal('show');

                $.ajax({
                    url: originalAction,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#loadingModal').modal('hide');
                        toastr.success('Selected assessments saved successfully');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        $('#loadingModal').modal('hide');
                        toastr.error('Error: ' + (xhr.responseJSON?.message ||
                            'Unknown error'));
                    }
                });
            });

            
            $(document).keydown(function(e) {
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    $('#bulkAssessmentForm').submit();
                }
            });
        });
    </script>
@endsection
