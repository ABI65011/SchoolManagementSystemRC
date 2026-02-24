@extends('layouts.main')

@section('title', 'Attendance Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3 class="m-0">
                <i class="fas fa-map-marked-alt mr-2"></i>
                Location Settings
            </h3>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.attendance.location.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>
                Add New Location
            </a>
        </div>
    </div>

    <div class="row">
        @forelse($locations as $location)
            <div class="col-md-6 col-lg-4">
                <div class="card card-outline {{ $location->is_default ? 'card-success' : 'card-primary' }}">
                    <div class="card-header">
                        <h3 class="card-title">{{ $location->location_name }}</h3>
                        <div class="card-tools">
                            @if($location->is_default)
                                <span class="badge text-bg-success">DEFAULT</span>
                            @endif
                            @if(!$location->is_active)
                                <span class="badge text-bg-secondary">INACTIVE</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 text-center">
                                <i class="fas fa-school fa-3x text-primary mb-2"></i>
                                <h5>{{ $location->location_name }}</h5>
                            </div>
                        </div>

                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><i class="fas fa-map-marker-alt text-muted mr-2"></i>Coordinates:</td>
                                <td class="text-right">
                                    <code>{{ number_format($location->school_latitude, 6) }}, {{ number_format($location->school_longitude, 6) }}</code>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-bullseye text-muted mr-2"></i>Geofence Radius:</td>
                                <td class="text-right">{{ $location->geofence_radius }} meters</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-clock text-muted mr-2"></i>Late Threshold:</td>
                                <td class="text-right">{{ \Carbon\Carbon::parse($location->late_threshold)->format('h:i A') }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-hourglass-half text-muted mr-2"></i>Full Day Hours:</td>
                                <td class="text-right">{{ $location->full_day_hours }} hours</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-sun text-muted mr-2"></i>Work Day:</td>
                                <td class="text-right">
                                    {{ \Carbon\Carbon::parse($location->working_day_start)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($location->working_day_end)->format('h:i A') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.attendance.location.edit', $location) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>

                        @if(!$location->is_default)
                            <form action="{{ route('admin.attendance.location.set-default', $location) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-check mr-1"></i>
                                    Set as Default
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.attendance.location.destroy', $location) }}" method="POST" class="d-inline float-right" onsubmit="return confirm('Are you sure you want to delete this location?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                    <h4>No location Configured</h4>
                    <p>Add your first school location to get started with attendance tracking.</p>
                    <a href="{{ route('admin.attendance.location.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus mr-2"></i>
                        Add Location
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Help Card -->
    <div class="card card-outline card-info mt-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle mr-2"></i>
                About Location Settings
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-bullseye text-primary mr-2"></i>Geofencing</h5>
                    <p class="text-muted">
                        Staff must be within the specified radius (in meters) of the school coordinates to check in.
                        The system uses the browser's GPS to verify location.
                    </p>
                </div>
                <div class="col-md-6">
                    <h5><i class="fas fa-clock text-primary mr-2"></i>Late Threshold</h5>
                    <p class="text-muted">
                        Staff who check in after this time will be marked as "late" with the number of minutes calculated automatically.
                    </p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h5><i class="fas fa-hourglass-half text-primary mr-2"></i>Full Day Hours</h5>
                    <p class="text-muted">
                        The minimum working hours required for a full day. Overtime is calculated based on hours worked beyond this threshold.
                    </p>
                </div>
                <div class="col-md-6">
                    <h5><i class="fas fa-map-marker-alt text-primary mr-2"></i>Default Location</h5>
                    <p class="text-muted">
                        The default location is used when staff check in and no specific location is assigned to them.
                        Only one location can be default at a time.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
