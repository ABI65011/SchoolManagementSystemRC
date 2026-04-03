@extends('layouts.main')

@section('title', 'Create Grading Scale')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Create New Grading Scale
                    </h3>
                </div>
                <form action="{{ route('grading-scales.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <!-- Scale Name -->
                        <div class="form-group">
                            <label for="name">Scale Name <span class="text-danger">*</span></label>
                            <select name="name" id="name" class="form-control @error('name') is-invalid @enderror" required>
                                <option value="">Select Scale Name</option>
                                @foreach($names as $name)
                                    <option value="{{ $name->value }}" {{ old('name') == $name->value ? 'selected' : '' }}>
                                        {{ $name->value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Scale Type -->
                        <div class="form-group">
                            <label for="type">Scale Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select Scale Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', ucfirst($type->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                UNEB Traditional: D1-F9 (UCE), UACE: A-F with points, Competency-Based: A-E descriptors
                            </small>
                        </div>

                        <!-- Settings -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="is_default"
                                               name="is_default"
                                               value="1"
                                               {{ old('is_default') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_default">
                                            Set as Default Scale
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Default scale will be used when no specific scale is selected</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                            {{-- <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Note:</strong> After creating the scale, you'll be able to add grade items (D1, A, etc.) with their mark ranges.
                            </div> --}}
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Create Scale
                        </button>
                        <a href="{{ route('grading-scales.index') }}" class="btn btn-default ml-2">
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
