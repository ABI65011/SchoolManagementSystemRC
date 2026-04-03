{{-- resources/views/exam-results/student-exam-results.blade.php --}}
@extends('layouts.main')

@section('title', 'Exam Results - ' . $exam->name . ' - ' . $student->full_name)
@section('header')
    <style>
        .grade-badge {
            font-size: 20px;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 8px;
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

        .aoi-badge {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .aoi-badge.excellent {
            background: #28a745;
            color: white;
        }

        .aoi-badge.good {
            background: #ffc107;
            color: #856404;
        }

        .aoi-badge.needs {
            background: #dc3545;
            color: white;
        }

        .result-card {
            transition: all 0.3s;
            border-left: 4px solid;
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-md-8">
                <h3 class="m-0">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Exam Results: {{ $exam->name }}
                </h3>
                <p class="text-muted">
                    Term {{ $exam->term }}, {{ $exam->year }} | {{ $exam->class->name }}
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('exam-results.index', ['exam_id' => $exam->id]) }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Results
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title">Student Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Name:</strong> {{ $student->full_name }}
                    </div>
                    {{-- <div class="col-md-3">
                        <strong>Admission No:</strong> {{ $student->admission_number }}
                    </div> --}}
                    <div class="col-md-3">
                        <strong>Class:</strong> {{ $student->current_class_name ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Gender:</strong> {{ ucfirst($student->gender ?? 'N/A') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $subjectCount }}</h3>
                        <p>Subjects Taken</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($averageMark, 2) }}%</h3>
                        <p>Average Score</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($totalMarks, 2) }}</h3>
                        <p>Total Marks</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $gradeInfo['grade'] }}</h3>
                        <p>Overall Grade</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Results Table -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Subject Results</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Raw Mark</th>
                                <th>Final Mark</th>
                                <th>Grade</th>
                                <th>Descriptor</th>
                                <th>Teacher Remark</th>
                        </thead>
                        <tbody>
                            @foreach ($results as $index => $result)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $result->subject->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $result->subject->code }}</small>
                                    </td>
                                    <td class="text-center">{{ number_format($result->raw_mark, 2) }}</td>
                                    <td class="text-center"><strong>{{ number_format($result->final_mark, 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge grade-{{ $result->grade }}">
                                            {{ $result->grade }}
                                        </span>
                                    </td>
                                    <td>{{ $result->getGradeDescriptorAttribute() ?? '-' }}</td>
                                    <td>{{ $result->teacher_remark ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="3" class="text-right"><strong>Average:</strong></td>
                                <td class="text-center"><strong>{{ number_format($averageMark, 2) }}</strong></td>
                                <td class="text-center"><strong>{{ $gradeInfo['grade'] }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- AoI Criteria Section -->
        @if (!empty($aoiSummary))
            <div class="card card-outline card-info mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Activity of Integration (AoI) - RACE Criteria
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @foreach ($aoiSummary as $code => $criterion)
                            @php
                                $avgScore = $criterion['average'];
                                $scoreClass = $avgScore >= 2.5 ? 'excellent' : ($avgScore >= 1.5 ? 'good' : 'needs');
                            @endphp
                            <div class="col-md-3">
                                <div class="p-3 border rounded">
                                    <div class="aoi-badge {{ $scoreClass }} mx-auto mb-2">
                                        {{ number_format($avgScore, 1) }}
                                    </div>
                                    <strong>{{ ucfirst($code) }}</strong>
                                    {{-- <div class="small text-muted mt-1">
                                        @foreach ($criterion['scores'] as $score)
                                            <span class="badge text-bg-secondary">{{ $score }}</span>
                                        @endforeach
                                    </div> --}}
                                    <div class="mt-2">
                                        <span
                                            class="badge text-bg-{{ $avgScore >= 2.5 ? 'success' : ($avgScore >= 1.5 ? 'warning' : 'danger') }}">
                                            {{ $avgScore >= 2.5 ? 'Excellent' : ($avgScore >= 1.5 ? 'Good' : 'Needs Improvement') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Scores: 1=Below, 2=Meets, 3=Exceeds Expectations</small>
                    </div>
                </div>
            </div>
        @endif

        <!-- Grading Scale Reference -->
@if($gradingScale)
<div class="card card-outline card-secondary mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-balance-scale mr-2"></i>
            Grading Scale Reference
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr class="bg-light">
                        <th class="text-center">Grade</th>
                        <th class="text-center">Range</th>
                        <th class="text-center">Descriptor</th>
                        <th class="text-center">Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gradingScale->items->sortByDesc('min_mark') as $item)
                        @php
                            $bgClass = match(true) {
                                in_array($item->grade_code, ['D1', 'A']) => 'table-success',
                                in_array($item->grade_code, ['D2', 'B']) => 'table-info',
                                in_array($item->grade_code, ['C3', 'C4', 'C5', 'C6', 'C']) => 'table-warning',
                                in_array($item->grade_code, ['P7', 'P8', 'D']) => 'table-primary',
                                default => 'table-danger'
                            };
                        @endphp
                        <tr class="{{ $bgClass }}">
                            <td class="text-center">
                                <strong>{{ $item->grade_code }}</strong>
                            </td>
                            <td class="text-center">
                                {{ $item->min_mark }} - {{ $item->max_mark }}
                            </td>
                            <td>
                                {{ $item->descriptor ?? '—' }}
                            </td>
                            <td class="text-center">
                                {{ $item->points ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
    </div>
@endsection
