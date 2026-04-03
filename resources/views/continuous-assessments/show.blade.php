@extends('layouts.main')

@section('title', 'Continuous Assessment Details')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <h3 class="m-0">
                    <i class="fas fa-tasks mr-2"></i>
                    Continuous Assessment Details
                </h3>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('continuous-assessments.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
                <a href="{{ route('continuous-assessments.edit', $continuousAssessment) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Main Info Card -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Assessment Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Student</th>
                                <td>
                                    <strong>{{ $continuousAssessment->student->full_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $continuousAssessment->student->admission_number }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Class</th>
                                <td>{{ $continuousAssessment->class->name }}</td>
                            </tr>
                            <tr>
                                <th>Subject</th>
                                <td>{{ $continuousAssessment->subject->name }} ({{ $continuousAssessment->subject->code }})
                                </td>
                            </tr>
                            <tr>
                                <th>Assessment Type</th>
                                <td>
                                    <span
                                        class="badge text-bg-info">{{ $continuousAssessment->assessmentType?->name->value ?? 'N/A' }}</span>
                                    <br>
                                    <small>Weight:
                                        {{ $continuousAssessment->assessmentType?->default_weight ?? 0 }}%</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td>{{ $continuousAssessment->title ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>Term/Year</th>
                                <td>Term {{ $continuousAssessment->term }}, {{ $continuousAssessment->year }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Teacher Comment Card -->
                @if ($continuousAssessment->teacher_comment)
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Teacher's Comment</h3>
                        </div>
                        <div class="card-body">
                            {{ $continuousAssessment->teacher_comment }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <!-- Score Card -->
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">Score Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h1 class="display-4">{{ number_format($continuousAssessment->raw_score, 2) }}</h1>
                            <p class="lead">out of {{ number_format($continuousAssessment->max_score, 2) }}</p>
                        </div>

                        @php $percentage = ($continuousAssessment->raw_score / $continuousAssessment->max_score) * 100; @endphp

                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar bg-{{ $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}"
                                style="width: {{ $percentage }}%;">
                                {{ number_format($percentage, 2) }}%
                            </div>
                        </div>

                        <table class="table table-sm">
                            <tr>
                                <th>Weighted Score:</th>
                                <td class="text-right">{{ number_format($continuousAssessment->weighted_score, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td class="text-right">
                                    @if ($percentage >= 50)
                                        <span class="badge text-bg-success">Passing</span>
                                    @else
                                        <span class="badge text-bg-danger">Failing</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Metadata Card -->
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Record Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Recorded By:</th>
                                <td>{{ $continuousAssessment->recordedBy?->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <th>Date Recorded:</th>
                                <td>{{ $continuousAssessment->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $continuousAssessment->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
