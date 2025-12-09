@extends('layouts.main')
@section('title', 'View Student')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Student Details</h3></div>
        <div class="card-body">
            <table class="table table-sm">
                <tr><td width="200">Full Name</td><td>{{ $students->first_name.' '.$students->middle_name.' '.$students->last_name }}</td></tr>
                <tr><td>Date of Birth</td><td>{{ ($students->dob)->format('d, m, Y') }}</td></tr>
                <tr><td>Gender</td><td>{{ $students->gender }}</td></tr>
                <tr><td>Country</td><td>{{ $students->country }}</td></tr>
                <tr><td>Region</td><td>{{ $students->region }}</td></tr>
                <tr><td>District</td><td>{{ $students->district }}</td></tr>
                <tr><td>Sub-County</td><td>{{ $students->sub_county }}</td></tr>
                <tr><td>Parish</td><td>{{ $students->parish }}</td></tr>
                <tr><td>ID Type</td><td>{{ $students->id_type }}</td></tr>
                <tr><td>ID No</td><td>{{ $students->id_no }}</td></tr>
                <tr><td>ID Image</td><td><img src="{{ asset('storage/' . $students->id_image_path )}}" width="150" class="img-thumbnail"></td></tr>
                <tr><td>Spoken Languages</td><td>{{ implode(', ', $students->spoken_languages) }}</td></tr>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('students.edit',$students) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection

