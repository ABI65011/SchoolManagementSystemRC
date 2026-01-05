@extends('layouts.main')
@section('title', 'Students')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Student List</h3>
                <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm float-end">Add Student</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $s)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $s->first_name . ' ' . $s->last_name }}</td>
                                <td>{{ $s->dob }}</td>
                                <td>{{ $s->gender }}</td>
                                <td>
                                    <a href="{{ route('students.show', $s) }}" class="btn btn-sm btn-info"><i
                                            class="bi bi-eye-fill"></i> </a>
                                    <a href="{{ route('students.edit', $s) }}" class="btn btn-sm btn-warning"><i
                                            class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('students.destroy', $s) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i
                                                class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
