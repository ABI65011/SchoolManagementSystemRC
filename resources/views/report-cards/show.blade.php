{{-- resources/views/report-cards/show.blade.php --}}
@extends('layouts.main')

@section('title', 'Report Card - ' . $student->full_name)

@section('header')
<style>
    /* Grade Color Classes */
    .grade-A, .grade-D1 {
        background-color: #28a745;
        color: white;
        font-weight: bold;
        padding: 5px 12px;
        border-radius: 4px;
        display: inline-block;
        min-width: 50px;
        text-align: center;
    }
    .grade-B, .grade-D2 {
        background-color: #5cb85c;
        color: white;
        font-weight: bold;
        padding: 5px 12px;
        border-radius: 4px;
        display: inline-block;
        min-width: 50px;
        text-align: center;
    }
    .grade-C, .grade-C3, .grade-C4, .grade-C5, .grade-C6 {
        background-color: #f0ad4e;
        color: white;
        font-weight: bold;
        padding: 5px 12px;
        border-radius: 4px;
        display: inline-block;
        min-width: 50px;
        text-align: center;
    }
    .grade-D, .grade-P7, .grade-P8 {
        background-color: #17a2b8;
        color: white;
        font-weight: bold;
        padding: 5px 12px;
        border-radius: 4px;
        display: inline-block;
        min-width: 50px;
        text-align: center;
    }
    .grade-E, .grade-F9, .grade-U, .grade-UNG {
        background-color: #dc3545;
        color: white;
        font-weight: bold;
        padding: 5px 12px;
        border-radius: 4px;
        display: inline-block;
        min-width: 50px;
        text-align: center;
    }
    .grade-default {
        background-color: #6c757d;
        color: white;
        font-weight: bold;
        padding: 5px 12px;
        border-radius: 4px;
        display: inline-block;
        min-width: 50px;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="m-0">
                <i class="fas fa-address-card mr-2"></i>
                Report Card: {{ $student->full_name }}
            </h3>
            <p class="text-muted">
                Term {{ $term }}, {{ $year }} | {{ $student->current_class_name ?? 'N/A' }}
            </p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('report-cards.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print mr-2"></i> Print
            </button>
        </div>
    </div>

    <!-- Report Card Content -->
    <div class="card">
        <div class="card-body">
            <!-- Student Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Student Name:</strong> {{ $student->full_name }}</p>
                    {{-- <p><strong>Admission No:</strong> {{ $student->admission_number }}</p> --}}
                    <p><strong>Term:</strong> {{ $term }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Class:</strong> {{ $student->current_class_name ?? 'N/A' }}</p>
                    <p><strong>Year:</strong> {{ $year }}</p>
                </div>
            </div>

            <!-- Results Table -->
            <table class="table table-bordered">
                <thead>
                    <tr class="bg-light">
                        <th>Subject</th>
                        <th>Exam Mark (80%)</th>
                        <th>CA Mark (20%)</th>
                        <th>Final Mark</th>
                        <th>Grade</th>
                        <th>Descriptor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                    <tr>
                        <td><strong>{{ $subject['subject']->name }}</strong></td>
                        <td class="text-center">{{ number_format($subject['exam_mark'], 2) }}</td>
                        <td class="text-center">{{ number_format($subject['ca_mark'], 2) }}</td>
                        <td class="text-center"><strong>{{ number_format($subject['final_mark'], 2) }}</strong></td>
                        <td class="text-center">
                            @php
                                $grade = $subject['grade'];
                                $gradeClass = match(true) {
                                    in_array($grade, ['A', 'D1']) => 'grade-A',
                                    in_array($grade, ['B', 'D2']) => 'grade-B',
                                    in_array($grade, ['C', 'C3', 'C4', 'C5', 'C6']) => 'grade-C',
                                    in_array($grade, ['D', 'P7', 'P8']) => 'grade-D',
                                    in_array($grade, ['E', 'F9', 'U', 'UNG']) => 'grade-E',
                                    default => 'grade-default'
                                };
                            @endphp
                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                        </td>
                        <td>{{ $subject['descriptor'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <td colspan="3" class="text-right"><strong>Average:</strong></td>
                        <td class="text-center"><strong>{{ number_format(collect($subjects)->avg('final_mark'), 2) }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
