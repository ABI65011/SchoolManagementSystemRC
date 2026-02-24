@extends('layouts.main')

@section('title', 'Edit Location')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Location: {{ $location->location_name }}
                    </h3>
                </div>
                <form action="{{ route('admin.attendance.location.update', $location) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="form-group">
                            <label for="location_name">Location Name <span class="text-danger">*</span></label>
                            <input type="text" name="location_name" id="location_name"
                                class="form-control @error('location_name') is-invalid @enderror"
                                value="{{ old('location_name', $location->location_name) }}" required>
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
                                        value="{{ old('school_latitude', $location->school_latitude) }}" required>
                                    @error('school_latitude')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="school_longitude">Longitude <span class="text-danger">*</span></label>
                                    <input type="number" step="any" name="school_longitude" id="school_longitude"
                                        class="form-control @error('school_longitude') is-invalid @enderror"
                                        value="{{ old('school_longitude', $location->school_longitude) }}" required>
                                    @error('school_longitude')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="geofence_radius">Geofence Radius (meters) <span class="text-danger">*</span></label>
                            <input type="number" name="geofence_radius" id="geofence_radius"
                                class="form-control @error('geofence_radius') is-invalid @enderror"
                                value="{{ old('geofence_radius', $location->geofence_radius) }}" required min="10" max="5000">
                            @error('geofence_radius')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr>

                        <h5 class="mb-3 text-muted">
                            <i class="fas fa-clock mr-2"></i>
                            Working Hours Configuration
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="late_threshold">Late Threshold <span class="text-danger">*</span></label>
                                    <input type="time" name="late_threshold" id="late_threshold"
                                        class="form-control @error('late_threshold') is-invalid @enderror"
                                        value="{{ old('late_threshold', \Carbon\Carbon::parse($location->late_threshold)->format('H:i')) }}" required>
                                    @error('late_threshold')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_day_hours">Full Day Hours <span class="text-danger">*</span></label>
                                    <input type="number" step="0.5" name="full_day_hours" id="full_day_hours"
                                        class="form-control @error('full_day_hours') is-invalid @enderror"
                                        value="{{ old('full_day_hours', $location->full_day_hours) }}" required min="1" max="24">
                                    @error('full_day_hours')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="working_day_start">Working Day Start <span class="text-danger">*</span></label>
                                    <input type="time" name="working_day_start" id="working_day_start"
                                        class="form-control @error('working_day_start') is-invalid @enderror"
                                        value="{{ old('working_day_start', \Carbon\Carbon::parse($location->working_day_start)->format('H:i')) }}" required>
                                    @error('working_day_start')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="working_day_end">Working Day End <span class="text-danger">*</span></label>
                                    <input type="time" name="working_day_end" id="working_day_end"
                                        class="form-control @error('working_day_end') is-invalid @enderror"
                                        value="{{ old('working_day_end', \Carbon\Carbon::parse($location->working_day_end)->format('H:i')) }}" required>
                                    @error('working_day_end')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1"
                                    {{ old('is_default', $location->is_default) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_default">Set as Default Location</label>
                            </div>
                            @if($location->is_default)
                                <small class="form-text text-success">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    This is currently the default location
                                </small>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                            @if(!$location->is_active)
                                <small class="form-text text-warning">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    This location is currently inactive
                                </small>
                            @endif
                        </div>

                        <!-- Current Stats -->
                        <div class="alert alert-light border mt-4">
                            <h6 class="font-weight-bold">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Usage Statistics
                            </h6>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <small class="text-muted">Created:</small>
                                    <br>
                                    {{ $location->created_at->format('M d, Y') }}
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Last Updated:</small>
                                    <br>
                                    {{ $location->updated_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Update Location
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
