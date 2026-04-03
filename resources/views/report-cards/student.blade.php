@extends('layouts.main')

@section('title', 'Report Cards - ' . $student->full_name)
@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<style>
    .timeline-item {
        transition: all 0.3s;
    }
    .timeline-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .grade-badge {
        font-size: 1.2em;
        padding: 5px 10px;
    }
    .summary-card {
        border-left: 4px solid;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Student Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="m-0">
                <i class="fas fa-address-card mr-2"></i>
                Report Cards: {{ $student->full_name }}
            </h3>
            <p class="text-muted">
                <i class="fas fa-id-card mr-1"></i> {{ $student->admission_number }} |
                <i class="fas fa-graduation-cap mr-1"></i> {{ $student->class->name ?? 'N/A' }}
            </p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('students.show', $student) }}" class="btn btn-info">
                <i class="fas fa-user mr-2"></i>
                Student Profile
            </a>
            <a href="{{ route('report-cards.create', ['student_id' => $student->id]) }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>
                New Report Card
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $reportCards->count() }}</h3>
                    <p>Total Report Cards</p>
                </div>
                <div class="icon">
                    <i class="fas fa-address-card"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    @php
                        $latest = $reportCards->sortByDesc('generated_at')->first();
                    @endphp
                    <h3>{{ $latest?->generated_at?->format('M Y') ?? 'N/A' }}</h3>
                    <p>Latest Generated</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    @php

                        $uniqueTerms = $reportCards->map(function($card) {
                            return $card->term . '-' . $card->year;
                        })->unique()->count();
                    @endphp
                    <h3>{{ $uniqueTerms }}</h3>
                    <p>Terms Covered</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    @php
                        $firstGenerated = $reportCards->sortBy('generated_at')->first();
                    @endphp
                    <h3>{{ $firstGenerated?->generated_at?->format('Y') ?? 'N/A' }}</h3>
                    <p>First Report</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Trend Chart -->
    @if($reportCards->count() > 1)
    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line mr-2"></i>
                Performance Trend
            </h3>
        </div>
        <div class="card-body">
            <canvas id="performanceChart" style="height: 300px;"></canvas>
        </div>
    </div>
    @endif

    <!-- Report Cards Timeline -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>
                        Report Card History
                    </h3>
                    <div class="card-tools">
                        <span class="badge text-bg-primary">{{ $reportCards->count() }} records</span>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($reportCards->groupBy(function($card) {
                        return $card->generated_at->format('Y');
                    }) as $year => $yearCards)
                        <div class="timeline">
                            <div class="time-label">
                                <span class="bg-primary">{{ $year }}</span>
                            </div>
                            @foreach($yearCards->sortByDesc('generated_at') as $reportCard)
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $reportCard->generated_at->format('d M Y H:i') }}
                                    </span>
                                    <h3 class="timeline-header">
                                        <a href="{{ route('report-cards.show', $reportCard) }}">
                                            Term {{ $reportCard->term }}, {{ $reportCard->year }} Report Card
                                        </a>
                                        <small class="ml-2">
                                            Generated by: {{ $reportCard->generatedBy?->name ?? 'System' }}
                                        </small>
                                    </h3>
                                    <div class="timeline-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <span class="badge text-bg-info mr-2">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $reportCard->generated_at->format('d/m/Y') }}
                                                </span>
                                                @if($reportCard->pdf_path)
                                                    <span class="badge text-bg-success">
                                                        <i class="fas fa-file-pdf mr-1"></i>
                                                        PDF Available
                                                    </span>
                                                @else
                                                    <span class="badge text-bg-secondary">
                                                        <i class="fas fa-file-pdf mr-1"></i>
                                                        No PDF
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <div class="btn-group">
                                                    <a href="{{ route('report-cards.show', $reportCard) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('report-cards.download', $reportCard) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i> PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-address-card fa-3x mb-3"></i>
                            <br>
                            No report cards found for this student.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/select2.full.min.js') }}"></script>
@if($reportCards->count() > 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        
        @if(isset($chartData))
        var labels = {!! json_encode($chartData['labels']) !!};
        var scores = {!! json_encode($chartData['scores']) !!};

        var ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Average Score (%)',
                    data: scores,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Average: ' + context.raw.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endif
@endsection
