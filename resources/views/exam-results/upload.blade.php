@extends('layouts.main')

@section('title', 'Upload Results - ' . $exam->name)
@section('header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="m-0">
                <i class="fas fa-upload mr-2"></i>
                Upload Results: {{ $exam->name }}
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
            <a href="{{ route('exam-results.create', $exam) }}" class="btn btn-success">
                <i class="fas fa-pen mr-2"></i>
                Manual Entry
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Upload Results File</h3>
                </div>
                <form action="{{ route('exam-results.upload', $exam) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>File Requirements:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Accepted formats: CSV, Excel (.xlsx), Text (.txt)</li>
                                <li>Maximum file size: 10MB</li>
                                <li>First row should contain column headers</li>
                                <li>Required columns: student_id, subject_code, raw_mark</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label for="file">Select File <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input @error('file') is-invalid @enderror"
                                       id="file"
                                       name="file"
                                       accept=".csv,.xlsx,.txt"
                                       required>
                                <label class="custom-file-label" for="file">Choose file...</label>
                            </div>
                            @error('file')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <a href="{{ route('exam-results.template', $exam) }}" class="btn btn-info">
                                <i class="fas fa-download mr-2"></i>
                                Download Template
                            </a>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Note:</strong> Uploading a file will overwrite existing results for the same students and subjects.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Results
                        </button>
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-default ml-2">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@endsection
