{{-- resources/views/exam-results/create.blade.php --}}
@extends('layouts.main')

@section('title', 'Enter Results - ' . $exam->name)
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        .mark-input {
            width: 80px;
            text-align: center;
        }

        .mark-input.passing {
            background-color: #d4edda;
        }

        .mark-input.failing {
            background-color: #f8d7da;
        }

        .aoi-select {
            width: 55px;
            text-align: center;
        }

        .aoi-select.score-1 {
            background-color: #f8d7da;
        }

        .aoi-select.score-2 {
            background-color: #fff3cd;
        }

        .aoi-select.score-3 {
            background-color: #d4edda;
        }

        .result-row.saved {
            border-left: 3px solid #28a745;
            background-color: #f0fff0;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }

        .table-container {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <h3 class="m-0">
                    <i class="fas fa-pen mr-2"></i>
                    Enter Results: {{ $exam->name }}
                </h3>
                <p class="text-muted">
                    Term {{ $exam->term }}, {{ $exam->year }} | {{ $exam->class->name }}
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('exams.show', $exam) }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Exam
                </a>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Info Alert -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Instructions:</strong> Enter marks for each student and subject.
            AoI scores: 1=Below, 2=Meets, 3=Exceeds.
        </div>

        <!-- Subject Filter -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Subject Filter</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Filter by Subject</label>
                            <select id="subjectFilter" class="form-control select2">
                                <option value="all">All Subjects</option>
                                @foreach ($subjects as $subject)
                                    <option value="subject-{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Show/Hide Columns</label>
                            <div class="btn-group-toggle">
                                @foreach ($subjects as $subject)
                                    <label class="btn btn-sm btn-outline-secondary subject-toggle"
                                        data-subject="{{ $subject->id }}">
                                        <input type="checkbox" checked> {{ $subject->code }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Entry Form -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Student Results</h3>
                <div class="card-tools">
                    <span class="badge text-bg-primary">{{ $exam->class->students->count() }} students</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <form action="{{ route('exam-results.store', $exam) }}" method="POST" id="resultsForm">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="sticky-header">
                                    <tr>
                                        <th width="50">#</th>
                                        <th width="180">Student</th>
                                        <th width="100">Adm No.</th>
                                        @foreach ($subjects as $subject)
                                            <th class="subject-col subject-{{ $subject->id }}"
                                                data-subject="{{ $subject->id }}" width="100">
                                                {{ $subject->code }}
                                                <br>
                                                <small>0-100</small>
                                            </th>
                                        @endforeach
                                        <th width="280">
                                            AoI Scores (RACE)
                                            <br>
                                            <small>1-3 per criterion</small>
                                        </th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($exam->class->students as $index => $student)
                                        @php
                                            $studentResults = $existingResults[$student->id] ?? collect();
                                            $hasResults = $studentResults->isNotEmpty();
                                        @endphp
                                        <tr class="result-row {{ $hasResults ? 'saved' : '' }}"
                                            data-student="{{ $student->id }}">
                                            <td class="text-center">{{ $loop->iteration }}<br>
                                                @if ($hasResults)
                                                    <small class="text-success">✓ Saved</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $student->full_name }}</strong>
                                            </td>
                                            <td class="text-center">{{ $student->admission_number }}</td>

                                            @foreach ($subjects as $subject)
                                                @php
                                                    $result = $studentResults
                                                        ->where('subject_id', $subject->id)
                                                        ->first();
                                                    $mark = $result->raw_mark ?? '';
                                                @endphp
                                                <td class="subject-col subject-{{ $subject->id }} text-center">
                                                    <input type="number"
                                                        name="results[{{ $student->id }}][{{ $subject->id }}][raw_mark]"
                                                        class="form-control form-control-sm mark-input"
                                                        value="{{ $mark }}" min="0" max="100"
                                                        step="0.5" style="width: 70px; margin: 0 auto;">
                                                    <input type="hidden"
                                                        name="results[{{ $student->id }}][{{ $subject->id }}][student_id]"
                                                        value="{{ $student->id }}">
                                                    <input type="hidden"
                                                        name="results[{{ $student->id }}][{{ $subject->id }}][subject_id]"
                                                        value="{{ $subject->id }}">
                                                </td>
                                            @endforeach

                                            <!-- AoI Criteria Section -->
                                            <td class="aoi-col">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @foreach ($aoiCriteria as $criterion)
                                                        @php
                                                            $firstResult = $studentResults->first();
                                                            $aoiScore = null;
                                                            if ($firstResult) {
                                                                
                                                                $aoi = $firstResult->aoiCriteria
                                                                    ->filter(function ($item) use ($criterion) {
                                                                        return $item->criterionDefinition?->code
                                                                            ->value === $criterion->value;
                                                                    })
                                                                    ->first();
                                                                $aoiScore = $aoi ? $aoi->score : null;
                                                            }
                                                        @endphp
                                                        <div class="text-center" style="width: 55px;">
                                                            <div class="small">{{ ucfirst($criterion->value) }}</div>
                                                            <select
                                                                name="results[{{ $student->id }}][aoi][{{ $criterion->value }}]"
                                                                class="form-control form-control-sm aoi-select">
                                                                <option value="">-</option>
                                                                @for ($i = 1; $i <= 3; $i++)
                                                                    <option value="{{ $i }}"
                                                                        {{ $aoiScore == $i ? 'selected' : '' }}>
                                                                        {{ $i }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="text-center mt-1">
                                                    <small class="text-muted" id="aoi-total-{{ $student->id }}">Total:
                                                        0/12</small>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <button type="submit" name="save_student" value="{{ $student->id }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-save"></i> Save
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <button type="submit" name="save_all" value="1" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i> Save All Changes
                            </button>
                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-default ml-2">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RACE Criteria Legend -->
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">RACE Criteria Guide (AoI)</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3"><strong>R - Relevance</strong><br><small>How relevant?</small></div>
                    <div class="col-md-3"><strong>A - Accuracy</strong><br><small>How accurate?</small></div>
                    <div class="col-md-3"><strong>C - Coherence</strong><br><small>How organized?</small></div>
                    <div class="col-md-3"><strong>E - Excellence</strong><br><small>Exceptional quality?</small></div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-md-4"><span class="badge bg-success">3 - Exceeds</span></div>
                    <div class="col-md-4"><span class="badge bg-warning">2 - Meets</span></div>
                    <div class="col-md-4"><span class="badge bg-danger">1 - Below</span></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select subject",
                allowClear: true
            });


            function updateAoiTotal() {
                $('tr[data-student]').each(function() {
                    var studentId = $(this).data('student');
                    var total = 0;
                    $(this).find('.aoi-select').each(function() {
                        var val = parseInt($(this).val());
                        if (!isNaN(val)) total += val;
                    });
                    $('#aoi-total-' + studentId).text('Total: ' + total + '/12');


                    $(this).find('.aoi-select').each(function() {
                        var val = parseInt($(this).val());
                        $(this).removeClass('score-1 score-2 score-3');
                        if (val === 1) $(this).addClass('score-1');
                        else if (val === 2) $(this).addClass('score-2');
                        else if (val === 3) $(this).addClass('score-3');
                    });
                });
            }


            function updateMarkStyling() {
                $('.mark-input').each(function() {
                    var val = parseFloat($(this).val());
                    if (!isNaN(val) && val !== '') {
                        if (val >= 50) {
                            $(this).removeClass('failing').addClass('passing');
                        } else {
                            $(this).removeClass('passing').addClass('failing');
                        }
                    }
                });
            }



            updateAoiTotal();
            updateMarkStyling();


            $(document).on('input change', '.mark-input, .aoi-select', function() {
                updateMarkStyling();
                updateAoiTotal();
            });


            $('#subjectFilter').change(function() {
                var subject = $(this).val();
                if (subject === 'all') {
                    $('.subject-col').show();
                } else {
                    $('.subject-col').hide();
                    $('.' + subject).show();
                }
            });


            $('.subject-toggle').click(function() {
                var subject = $(this).data('subject');
                var isChecked = $(this).find('input').is(':checked');
                if (isChecked) {
                    $('.subject-' + subject).show();
                } else {
                    $('.subject-' + subject).hide();
                }
            });
        });
    </script>
@endsection
