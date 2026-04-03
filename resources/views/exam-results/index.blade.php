{{-- resources/views/exam-results/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Exam Results')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        .student-group {
            background-color: #f8f9fa;
            transition: all 0.3s;
        }

        .student-group:hover {
            background-color: #e9ecef;
        }

        .student-group td {
            border-top: 2px solid #dee2e6;
        }

        .student-group:first-child td {
            border-top: none;
        }

        .student-header {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .subject-row {
            background-color: #fff;
        }

        .subject-row:hover {
            background-color: #f5f5f5;
        }

        .subject-code {
            font-weight: bold;
            color: #007bff;
        }

        .grade-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .grade-A,
        .grade-D1 {
            background: #28a745;
            color: white;
        }

        .grade-B,
        .grade-D2 {
            background: #5cb85c;
            color: white;
        }

        .grade-C,
        .grade-C3,
        .grade-C4 {
            background: #f0ad4e;
            color: white;
        }

        .grade-D,
        .grade-P7,
        .grade-P8 {
            background: #17a2b8;
            color: white;
        }

        .grade-E,
        .grade-F9 {
            background: #dc3545;
            color: white;
        }

        .grade-U {
            background: #6c757d;
            color: white;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }

        .expand-icon {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .expand-icon.expanded {
            transform: rotate(90deg);
        }

        .subject-details {
            display: none;
        }

        .subject-details.show {
            display: table-row;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="m-0">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Exam Results
                </h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('exams.index') }}" class="btn btn-info">
                    <i class="fas fa-file-alt mr-2"></i>
                    Exams
                </a>
            </div>
        </div>

         @if (!request('exam_id'))
            <div class="alert alert-warning mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Please select an exam from the filters above to view student results details.
            </div>
        @endif

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
                <form action="{{ route('exam-results.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Exam</label>
                                <select name="exam_id" class="form-control select2">
                                    <option value="">All Exams</option>
                                    @foreach ($exams ?? [] as $exam)
                                        <option value="{{ $exam->id }}"
                                            {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->name }} ({{ $exam->class->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Year</label>
                                <select name="year" class="form-control">
                                    <option value="">All Years</option>
                                    @foreach (range(now()->year - 2, now()->year) as $year)
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
                            <a href="{{ route('exam-results.index') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-2"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Table - Grouped by Student -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Exam Results (Grouped by Student)</h3>
                <div class="card-tools">
                    {{-- ✅ FIX: Change $results to $groupedResults --}}
                    <span class="badge text-bg-primary">{{ $groupedResults->count() }} students</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th width="50"></th>
                                <th>Student</th>
                                {{-- <th>Admission No.</th> --}}
                                <th>Subjects</th>
                                <th>Average</th>
                                <th>Total Score</th>
                                <th>Actions</th>
                        </thead>
                        <tbody>
                            {{-- ✅ FIX: Use $groupedResults instead of $results --}}
                            @forelse($groupedResults as $index => $studentData)
                                @php
                                    $student = $studentData['student'];
                                    $subjects = $studentData['subjects'];
                                    $average = $studentData['average'];
                                    $totalScore = $studentData['total_score'];
                                    $subjectCount = count($subjects);
                                    $studentInitials = strtoupper(
                                        substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1),
                                    );
                                @endphp
                                <tr class="student-group" data-student-id="{{ $student->id }}">
                                    <td class="text-center">{{ $loop->iteration }}<br>
                                        <small class="text-muted">{{ $subjectCount }} subjects</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="student-avatar">
                                            {{ $studentInitials }}
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $student->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            @if (isset($student->pivot->stream))
                                                Stream {{ $student->pivot->stream }} |
                                            @endif
                                            Class: {{ $student->current_class_name ?? 'N/A' }}
                                        </small>
                                    </td>
                                    {{-- <td>{{ $student->admission_number }}</td> --}}
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($subjects as $subject)
                                                <span class="badge text-bg-secondary subject-code"
                                                    data-subject-id="{{ $subject['subject']->id }}"
                                                    data-grade="{{ $subject['grade'] }}"
                                                    data-mark="{{ $subject['final_mark'] }}">
                                                    {{ $subject['subject']->code }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <span
                                                class="badge text-bg-success">
                                                {{ number_format($average['mark'], 2) }}%
                                            </span>
                                            <br>
                                            <small class="text-muted">Grade: {{ $average['grade'] }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($totalScore, 2) }}</strong>
                                        <br>
                                        <small class="text-muted">/ {{ $subjectCount * 100 }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @php
                                                $examId = request('exam_id') ?? ($subjects->first()['exam_id'] ?? null);
                                            @endphp

                                            @if ($examId)
                                                <a href="{{ route('exam-results.student-exam-results', ['exam' => $examId, 'student' => $student->id]) }}"
                                                    class="btn btn-sm btn-primary" title="View All Results">
                                                    <i class="fas fa-eye"></i> Details
                                                </a>
                                            @else

                                            @endif
                                            <a href="{{ route('students.show', $student) }}"
                                                class="btn btn-sm btn-secondary" title="View Profile">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Hidden subject details row -->
                                <tr class="subject-details" data-student-id="{{ $student->id }}"
                                    style="display: none;">
                                    <td colspan="8" class="p-0">
                                        <div class="card card-outline card-info m-2">
                                            <div class="card-header">
                                                <h4 class="card-title">Subject Details: {{ $student->full_name }}</h4>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool close-details"
                                                        data-student-id="{{ $student->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Subject</th>
                                                            <th>Raw Mark</th>
                                                            <th>Final Mark</th>
                                                            <th>Grade</th>
                                                            <th>Descriptor</th>
                                                            <th>Teacher Remark</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($subjects as $subject)
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $subject['subject']->name }}</strong>
                                                                    <br>
                                                                    <small
                                                                        class="text-muted">{{ $subject['subject']->code }}</small>
                                                                </td>
                                                                <td class="text-center">
                                                                    {{ number_format($subject['raw_mark'], 2) }}</td>
                                                                <td class="text-center">
                                                                    <strong>{{ number_format($subject['final_mark'], 2) }}</strong>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge grade-{{ $subject['grade_class'] ?? 'average' }}">
                                                                        {{ $subject['grade'] }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $subject['descriptor'] ?? '-' }}</td>
                                                                <td>{{ $subject['teacher_remark'] ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bg-light">
                                                            <td colspan="2" class="text-right">
                                                                <strong>Average:</strong>
                                                            </td>
                                                            <td class="text-center">
                                                                <strong>{{ number_format($average['mark'], 2) }}</strong>
                                                            </td>
                                                            <td class="text-center">
                                                                <strong>{{ $average['grade'] }}</strong>
                                                            </td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                        <br>
                                        No exam results found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{-- ✅ FIX: Remove pagination if you're not using it --}}
                {{-- {{ $groupedResults->links() }} --}}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select exam",
                allowClear: true
            });

            $('.expand-student').click(function() {
                var studentId = $(this).data('student-id');
                var detailsRow = $('tr.subject-details[data-student-id="' + studentId + '"]');
                var icon = $(this).find('i');

                if (detailsRow.is(':visible')) {
                    detailsRow.slideUp(300);
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                    $(this).removeClass('btn-warning').addClass('btn-info');
                } else {

                    $('.subject-details:visible').slideUp(300);
                    $('.expand-student').find('i').removeClass('fa-chevron-down').addClass(
                        'fa-chevron-right');
                    $('.expand-student').removeClass('btn-warning').addClass('btn-info');

                    detailsRow.slideDown(300);
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                    $(this).removeClass('btn-info').addClass('btn-warning');
                }
            });


            $('.close-details').click(function() {
                var studentId = $(this).data('student-id');
                var detailsRow = $('tr.subject-details[data-student-id="' + studentId + '"]');
                var expandBtn = $('.expand-student[data-student-id="' + studentId + '"]');

                detailsRow.slideUp(300);
                expandBtn.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
                expandBtn.removeClass('btn-warning').addClass('btn-info');
            });

            
            $('.subject-code').hover(function() {
                var grade = $(this).data('grade');
                var mark = $(this).data('mark');
                $(this).attr('title', 'Grade: ' + grade + ' | Mark: ' + mark);
            });
        });
    </script>
@endsection
