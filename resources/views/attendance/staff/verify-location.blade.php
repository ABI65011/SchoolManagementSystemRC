@extends('layouts.main')

@section('title', 'Verify Location')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card card-outline card-primary" id="locationCard">
                    <div class="card-header text-center">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Location Verification
                        </h3>
                    </div>
                    <div class="card-body text-center p-5">
                        <div id="loadingState">
                            <i class="fas fa-crosshairs fa-4x text-primary mb-3 fa-spin-pulse"
                                style="animation: pulse 2s infinite;"></i>
                            <h4>Detecting your location...</h4>
                            <p class="text-muted">Please allow location access when prompted by your browser.</p>
                            <div class="progress mt-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%">
                                </div>
                            </div>
                        </div>

                        <div id="errorState" style="display: none;">
                            <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                            <h4 class="text-danger">Location Error</h4>
                            <p class="text-muted" id="errorMessage"></p>
                            <button onclick="location.reload()" class="btn btn-primary mt-3">
                                <i class="fas fa-redo mr-2"></i>
                                Try Again
                            </button>
                        </div>

                        <div id="successState" style="display: none;">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h4 class="text-success">Location Verified</h4>
                            <p class="text-muted">You are within the school premises.</p>
                            <div class="alert alert-light border mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-map-pin mr-1"></i>
                                    <span id="locationCoords"></span>
                                </small>
                            </div>
                            <form action="{{ route('attendance.verify-location', $token) }}" method="POST" id="verifyForm">
                                @csrf
                                <input type="hidden" name="latitude" id="latInput">
                                <input type="hidden" name="longitude" id="lngInput">
                                <button type="submit" class="btn btn-success btn-lg mt-3">
                                    <i class="fas fa-check mr-2"></i>
                                    Complete Check-In
                                </button>
                            </form>
                        </div>

                        <div id="outsideState" style="display: none;">
                            <i class="fas fa-ban fa-4x text-warning mb-3"></i>
                            <h4 class="text-warning">Outside School Premises</h4>
                            <p class="text-muted">
                                You appear to be <strong id="distanceAway"></strong> meters away from the school.
                                <br>Please move within <strong>{{ $location->geofence_radius }} meters</strong> of the
                                school to check in.
                            </p>
                            <div class="alert alert-light border mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-map-pin mr-1"></i>
                                    Your location: <span id="yourCoords"></span>
                                </small>
                            </div>
                            <button onclick="location.reload()" class="btn btn-primary mt-3">
                                <i class="fas fa-redo mr-2"></i>
                                Check Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        const schoolLat = {{ $location->school_latitude }};
        const schoolLng = {{ $location->school_longitude }};
        const radius = {{ $location->geofence_radius }};

        function calculateDistance(lat1, lng1, lat2, lng2) {
            const R = 6371000;
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lng2 - lng1) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        }

        function initLocation() {
            if (!navigator.geolocation) {
                showError("Geolocation is not supported by your browser.");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    const distance = calculateDistance(userLat, userLng, schoolLat, schoolLng);

                    document.getElementById('latInput').value = userLat;
                    document.getElementById('lngInput').value = userLng;

                    if (distance <= radius) {

                        document.getElementById('loadingState').style.display = 'none';
                        document.getElementById('successState').style.display = 'block';
                        document.getElementById('locationCoords').textContent =
                            `${userLat.toFixed(6)}, ${userLng.toFixed(6)}`;
                    } else {

                        document.getElementById('loadingState').style.display = 'none';
                        document.getElementById('outsideState').style.display = 'block';
                        document.getElementById('distanceAway').textContent = Math.round(distance);
                        document.getElementById('yourCoords').textContent =
                            `${userLat.toFixed(6)}, ${userLng.toFixed(6)}`;
                    }
                },
                function(error) {
                    let message = "Unable to retrieve your location.";
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            message =
                                "Location access denied. Please enable location permissions in your browser settings.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = "Location information is unavailable.";
                            break;
                        case error.TIMEOUT:
                            message = "The request to get user location timed out.";
                            break;
                    }
                    showError(message);
                }, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }

        function showError(message) {
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('errorState').style.display = 'block';
            document.getElementById('errorMessage').textContent = message;
        }

        
        window.onload = initLocation;
    </script>
@endsection
