@extends('layouts.main')

@section('title', 'Attendance Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Status Card -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-2"></i>
                        Today's Attendance
                    </h3>
                    <div class="card-tools">
                        <span class="badge text-bg-info">{{ now()->format('l, F j, Y') }}</span>
                    </div>
                </div>
                <div class="card-body text-center">
                    @if(!$todayAttendance)
                        <!-- No check-in yet -->
                        <div class="mb-4">
                            <i class="fas fa-user-clock fa-4x text-muted mb-3"></i>
                            <h4>Not Checked In</h4>
                            <p class="text-muted">You haven't checked in today yet.</p>
                        </div>

                        <form action="{{ route('attendance.request-checkin') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-envelope mr-2"></i>
                                Send Check-In Link to Email
                            </button>
                        </form>
                        <small class="text-muted d-block mt-2">
                            A magic link will be sent to {{ Auth::user()->email }}
                        </small>
                    @elseif(!$todayAttendance->check_out)
                        <!-- Checked in, not checked out -->
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h4 class="text-success">Checked In</h4>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-sign-in-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Check In</span>
                                            <span class="info-box-number">{{ $todayAttendance->check_in->format('h:i A') }}</span>
                                            <small class="text-muted">{{ $todayAttendance->check_in_location }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-secondary"><i class="fas fa-sign-out-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Check Out</span>
                                            <span class="info-box-number">--:--</span>
                                            <small class="text-muted">Not checked out</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($todayAttendance->late_minutes > 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    You were {{ $todayAttendance->late_minutes }} minutes late today.
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('attendance.checkout') }}" method="POST" id="checkoutForm">
                            @csrf
                            <input type="hidden" name="latitude" id="checkoutLat">
                            <input type="hidden" name="longitude" id="checkoutLng">
                            <button type="submit" class="btn btn-warning btn-lg" id="checkoutBtn">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Check Out Now
                            </button>
                        </form>
                        <div id="locationStatus" class="mt-2 text-muted small"></div>
                    @else
                        <!-- Checked out -->
                        <div class="mb-4">
                            <i class="fas fa-check-double fa-4x text-success mb-3"></i>
                            <h4 class="text-success">Attendance Complete</h4>
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-sign-in-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Check In</span>
                                            <span class="info-box-number">{{ $todayAttendance->check_in->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-sign-out-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Check Out</span>
                                            <span class="info-box-number">{{ $todayAttendance->check_out->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-hourglass-half"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Working Hours</span>
                                            <span class="info-box-number">{{ number_format($todayAttendance->working_hours, 2) }} hrs</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <span class="badge text-bg-{{ $todayAttendance->status === 'present' ? 'success' : ($todayAttendance->status === 'late' ? 'warning' : 'secondary') }} badge-lg p-2">
                                    Status: {{ ucfirst($todayAttendance->status->value) }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent History -->
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>
                        Recent Attendance History
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendance as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date->format('M d, Y') }}</td>
                                    <td>{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '-' }}</td>
                                    <td>{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '-' }}</td>
                                    <td>{{ $attendance->working_hours ? number_format($attendance->working_hours, 2) : '-' }}</td>
                                    <td>
                                        <span class="badge text-bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : ($attendance->status === 'early_departure' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $attendance->status->value)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No attendance records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    
    document.getElementById('checkoutBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        const statusDiv = document.getElementById('locationStatus');
        const btn = this;

        btn.disabled = true;
        statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting your location...';

        if (!navigator.geolocation) {
            statusDiv.innerHTML = '<span class="text-danger">Geolocation is not supported by your browser</span>';
            btn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('checkoutLat').value = position.coords.latitude;
                document.getElementById('checkoutLng').value = position.coords.longitude;
                statusDiv.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Location captured</span>';
                document.getElementById('checkoutForm').submit();
            },
            function(error) {
                let message = 'Unable to retrieve your location';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = "Location access denied. Please enable location permissions.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = "Location information unavailable.";
                        break;
                    case error.TIMEOUT:
                        message = "Location request timed out.";
                        break;
                }
                statusDiv.innerHTML = '<span class="text-danger">' + message + '</span>';
                btn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
</script>
@endsection
