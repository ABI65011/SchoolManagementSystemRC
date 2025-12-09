@extends('layouts.main')
@section('title', 'Edit Student')
@section('plugins.Select2', true)
@section('content')
    <div class="container-fluid">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Edit Student</h3>
            </div>
            <form action="{{ route('students.update', $students) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name"
                                class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name', $students->first_name) }}">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name"
                                class="form-control @error('middle_name') is-invalid @enderror"
                                value="{{ old('middle_name', $students->middle_name) }}">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="last_name"
                                class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name', $students->last_name) }}">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                value="{{ old('dob', $students->dob->format('d, m, Y')) }}">
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- select --</option>
                                @foreach (['Male', 'Female'] as $g)
                                    <option value="{{ $g }}"
                                        {{ old('gender', $students->gender) == $g ? 'selected' : '' }}>{{ $g }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">ID Type *</label>
                            <select name="id_type" class="form-select @error('id_type') is-invalid @enderror">
                                <option value="">-- select --</option>
                                @foreach (['Birth Cert', 'National ID', 'Passport', 'Other'] as $t)
                                    <option value="{{ $t }}"
                                        {{ old('id_type', $students->id_type) == $t ? 'selected' : '' }}>{{ $t }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ID No *</label>
                            <input type="text" name="id_no" class="form-control @error('id_no') is-invalid @enderror"
                                value="{{ old('id_no', $students->id_no) }}">
                            @error('id_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">ID Image * (≤2 MB)</label>
                            <input type="file" name="id_image_path"
                                class="form-control @error('id_image_path') is-invalid @enderror">
                            @error('id_image_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($students->id_image_path)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $students->id_image_path ) }}"
                                        width="120" class="img-thumbnail">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Spoken Languages * (multi)</label>
                            <select name="spoken_languages[]"
                                class="select2-multiple form-control @error('spoken_languages') is-invalid @enderror"
                                multiple>
                                @foreach (languages() as $code => $name)
                                    <option value="{{ $code }}"
                                        @if (in_array($code, old('spoken_languages', $students->spoken_languages ?? []))) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('spoken_languages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(function() {
            $('.select2, .select2-multiple').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
@endpush
