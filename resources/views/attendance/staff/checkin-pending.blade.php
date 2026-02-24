@extends('layouts.main')

@section('title', 'Check Your Email')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card card-outline card-success">
                <div class="card-body text-center p-5">
                    <i class="fas fa-envelope-open-text fa-5x text-success mb-4"></i>
                    <h3 class="mb-3">Check Your Email</h3>
                    <p class="text-muted mb-4">
                        We've sent a magic link to<br>
                        <strong>{{ Auth::user()->email }}</strong>
                    </p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        The link will expire in <strong>15 minutes</strong>.
                    </div>
                    <p class="text-muted small">
                        Click the link in your email to complete your check-in.<br>
                        Make sure to allow location access when prompted.
                    </p>
                    <hr>
                    <p class="mb-0">
                        Didn't receive it?
                        <form action="{{ route('attendance.request-checkin') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0">Resend link</button>
                        </form>
                    </p>
                    <a href="{{ route('attendance.dashboard') }}" class="btn btn-outline-secondary mt-3">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
