@extends('layouts.main')

@section('title', 'Government Sponsored Students (UPE/USE)')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <h3 class="m-0">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    Government Sponsored Students
                </h3>
                <p class="text-muted">UPE/USE Beneficiaries</p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('students.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-2"></i> All Students
                </a>
                {{-- <a href="{{ route('students.sponsorship.export') }}" class="btn btn-success">
                <i class="fas fa-download mr-2"></i> Export List
            </a> --}}
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $students->count() }}</h3>
                        <p>Total Government Sponsored</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $students->where('class_id', $currentClass?->id)->count() }}</h3>
                        <p>In {{ $currentClass?->name ?? 'Current Class' }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $students->groupBy('class_id')->count() }}</h3>
                        <p>Classes Represented</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $students->where('gender', 'male')->count() }} /
                            {{ $students->where('gender', 'female')->count() }}</h3>
                        <p>Male / Female</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('students.government-sponsored') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="class_id" class="form-control select2">
                                    <option value="">All Classes</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">All</option>
                                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male
                                    </option>
                                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Name or Admission No." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fas fa-filter mr-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('students.government-sponsored') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-2"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Government Sponsored Students (UPE/USE)</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ $students->count() }} students</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Gender</th>
                                <th>Reference Number</th>
                                <th>Date Assigned</th>
                                <th>Actions</th>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }} </td>
                                    <td>
                                        <strong>{{ $student->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $student->first_name }}
                                            {{ $student->last_name }}</small>
                                    </td>
                                    <td class="text-center">{{ $student->class->name ?? ($student->joining_class ?? 'N/A') }}
                                    </td>
                                    <td class="text-center">{{ ucfirst($student->gender) }}</td>
                                    <td class="text-center">
                                        @if ($student->sponsorship_reference)
                                            <span class="badge badge-info">
                                                <i class="fas fa-hashtag"></i> {{ $student->sponsorship_reference }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $student->currentSponsorship?->start_date?->format('d/m/Y') ?? 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-primary"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <br>
                                        No government-sponsored students found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $students->links() }}
            </div>
        </div>


    </div>

@endsection

@section('javascript')
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select class",
                allowClear: true
            });
        });
    </script>
@endsection
