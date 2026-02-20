@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit me-2"></i>
                            Edit Holiday
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('holiday-calendars.index') }}" class="btn btn-tool">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('holiday-calendars.update', $holidayCalendar->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="required-field">Holiday Name</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $holidayCalendar->name) }}" placeholder="e.g. Christmas Day"
                                        required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="required-field">Date</label>
                                    <input type="date" name="date"
                                        class="form-control @error('date') is-invalid @enderror"
                                        value="{{ old('date', $holidayCalendar->date->format('Y-m-d')) }}" required>
                                    @error('date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="required-field">Holiday Type</label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach (array_column(\App\Helpers\HolidayType::cases(), 'value') as $type)
                                            <option value="{{ $type }}"
                                                {{ old('type', $holidayCalendar->type) == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="required-field">Is Recurring</label>
                                    <select name="is_recurring"
                                        class="form-select @error('is_recurring') is-invalid @enderror" required>
                                        <option value="0"
                                            {{ old('is_recurring', $holidayCalendar->is_recurring) == 0 ? 'selected' : '' }}>
                                            No</option>
                                        <option value="1"
                                            {{ old('is_recurring', $holidayCalendar->is_recurring) == 1 ? 'selected' : '' }}>
                                            Yes</option>
                                    </select>
                                    @error('is_recurring')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label>Description (Optional)</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="3" placeholder="Additional details about the holiday">{{ old('description', $holidayCalendar->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('holiday-calendars.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i> Update Holiday
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
