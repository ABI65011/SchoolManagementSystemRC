@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-indigo">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Create Holiday
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('holiday-calendars.index') }}" class="btn btn-tool">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('holiday-calendars.store') }}" method="POST">
                        @csrf

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="required-field">Holiday Name</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        placeholder="e.g. Christmas Day" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="required-field">Date</label>
                                    <input type="date" name="date"
                                        class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}"
                                        required>
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
                                                {{ old('type') == $type ? 'selected' : '' }}>
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
                                        <option value="">-- Select Option --</option>
                                        <option value="1" {{ old('is_recurring') == '1' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="0" {{ old('is_recurring') == '0' ? 'selected' : '' }}>No
                                        </option>
                                    </select>
                                    @error('is_recurring')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="required-field">Recurring Pattern (If Recurring)</label>
                                    <select name="recurring_pattern"
                                        class="form-select @error('recurring_pattern') is-invalid @enderror" required>
                                        <option value="">-- Select Pattern --</option>
                                        @foreach (array_column(\App\Helpers\RecurringPattern::cases(), 'value') as $pattern)
                                            <option value="{{ $pattern }}"
                                                {{ old('recurring_pattern') == $pattern ? 'selected' : '' }}>
                                                {{ $pattern }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('recurring_pattern')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for=""> Recurring Rules</label>
                                    <input type="text" name="recurring_rules"
                                        class="form-control @error('recurring_rules') is-invalid @enderror"
                                        value="{{ old('recurring_rules') }}"
                                        placeholder="e.g. Every year on December 25">
                                </div>

                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Description (Optional)</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                    placeholder="Additional details about the holiday...">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('holiday-calendars.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Save Holiday
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
