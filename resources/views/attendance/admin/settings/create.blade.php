@extends('layouts.main')

@section('title', 'Add New Location')

@section('content')
    <div class="container-fluid">
        {{-- <div id="toastWarning" class="toast toast-warning" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-circle me-2"></i>
                <strong class="me-auto">Bootstrap</strong> <small>11 mins ago</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">Hello, world! This is a toast message.</div>
        </div> --}}
        <div class="toast toast-warning" "></div>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Add New Location
                        </h3>
                    </div>
                    <form action="{{ route('admin.attendance.location.store') }}" method="POST">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label for="location_name">Location Name <span class="text-danger">*</span></label>
                                <input type="text" name="location_name" id="location_name"
                                    class="form-control @error('location_name') is-invalid @enderror"
                                    placeholder="e.g., Main Campus, Branch Campus" value="{{ old('location_name') }}"
                                    required>
                                @error('location_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="school_latitude">Latitude <span class="text-danger">*</span></label>
                                        <input type="number" step="any" name="school_latitude" id="school_latitude"
                                            class="form-control @error('school_latitude') is-invalid @enderror"
                                            placeholder="e.g., 0.3136" value="{{ old('school_latitude') }}" required>
                                        @error('school_latitude')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Decimal degrees (e.g., 0.3136)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="school_longitude">Longitude <span class="text-danger">*</span></label>
                                        <input type="number" step="any" name="school_longitude" id="school_longitude"
                                            class="form-control @error('school_longitude') is-invalid @enderror"
                                            placeholder="e.g., 32.5811" value="{{ old('school_longitude') }}" required>
                                        @error('school_longitude')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Decimal degrees (e.g., 32.5811)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="geofence_radius">Geofence Radius (meters) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="geofence_radius" id="geofence_radius"
                                    class="form-control @error('geofence_radius') is-invalid @enderror"
                                    placeholder="e.g., 200" value="{{ old('geofence_radius', 200) }}" required
                                    min="10" max="5000">
                                @error('geofence_radius')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Staff must be within this distance from coordinates to
                                    check in (10-5000 meters)</small>
                            </div>

                            <hr>

                            <h5 class="mb-3 text-muted">
                                <i class="fas fa-clock mr-2"></i>
                                Working Hours Configuration
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="late_threshold">Late Threshold <span
                                                class="text-danger">*</span></label>
                                        <input type="time" name="late_threshold" id="late_threshold"
                                            class="form-control @error('late_threshold') is-invalid @enderror"
                                            value="{{ old('late_threshold', '08:00') }}" required>
                                        @error('late_threshold')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Check-ins after this time are marked
                                            late</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="full_day_hours">Full Day Hours <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.5" name="full_day_hours" id="full_day_hours"
                                            class="form-control @error('full_day_hours') is-invalid @enderror"
                                            placeholder="e.g., 9" value="{{ old('full_day_hours', 9) }}" required
                                            min="1" max="24">
                                        @error('full_day_hours')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Minimum hours for a full working day</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="working_day_start">Working Day Start <span
                                                class="text-danger">*</span></label>
                                        <input type="time" name="working_day_start" id="working_day_start"
                                            class="form-control @error('working_day_start') is-invalid @enderror"
                                            value="{{ old('working_day_start', '08:00') }}" required>
                                        @error('working_day_start')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="working_day_end">Working Day End <span
                                                class="text-danger">*</span></label>
                                        <input type="time" name="working_day_end" id="working_day_end"
                                            class="form-control @error('working_day_end') is-invalid @enderror"
                                            value="{{ old('working_day_end', '17:00') }}" required>
                                        @error('working_day_end')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_default"
                                        name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_default">Set as Default Location</label>
                                </div>
                                <small class="form-text text-muted">If checked, this will become the default location for
                                    all staff</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                        value="1" checked>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                                <small class="form-text text-muted">Inactive locations cannot be used for check-ins</small>
                            </div>

                            <!-- Map Preview Placeholder -->
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Tip:</strong> You can find coordinates using
                                <a href="https://www.google.com/maps" target="_blank">Google Maps</a>
                                (right-click → "What's here?").
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i>
                                Save Location
                            </button>
                            <a href="{{ route('admin.attendance.location.index') }}" class="btn btn-default ml-2">
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
