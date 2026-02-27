@extends('layouts.main')

@section('page-title', 'Leave Applications')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-alt me-2"></i>All Applications
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('leave.application.create') }}" class="btn btn-sm btn-success me-2">
                                <i class="fas fa-plus me-1"></i>Add Application
                            </a>

                            @php
                                $user = Auth::user();
                                $canViewAll =
                                    $user->hasAnyRole(['Admin', 'Super', 'HR', 'BOD', 'Supervisor', 'Management']) ||
                                    in_array(optional($user->staff->position)->name, ['General Manager']);
                            @endphp

                            @if ($canViewAll)
                                @if (!$viewAll)
                                    <a href="{{ route('leave.applications.index', ['view' => 'all']) }}"
                                        class="btn btn-sm btn-secondary me-2">
                                        <i class="fas fa-globe me-1"></i>View All
                                    </a>
                                @else
                                    <a href="{{ route('leave.applications.index') }}"
                                        class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="fas fa-user me-1"></i>My View
                                    </a>
                                @endif
                            @endif

                            @hasanyrole('Admin|Super|HR')
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fas fa-chart-bar me-1"></i>Reports
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('leave.reports.index') }}"><i
                                                    class="fas fa-list me-2"></i>All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('leave.reports.pending') }}"><i
                                                    class="fas fa-clock me-2"></i>Pending</a></li>
                                        <li><a class="dropdown-item" href="{{ route('leave.reports.approved') }}"><i
                                                    class="fas fa-check me-2"></i>Approved</a></li>
                                        <li><a class="dropdown-item" href="{{ route('leave.reports.rejected') }}"><i
                                                    class="fas fa-times me-2"></i>Rejected</a></li>
                                    </ul>
                                </div>
                            @endhasanyrole
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 15%">Employee</th>
                                    <th style="width: 15%">Supervisor</th>
                                    <th style="width: 10%">Type</th>
                                    <th style="width: 15%">Dates</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 15%">Replacement</th>
                                    <th style="width: 15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allApplications as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px; font-size: 12px;">
                                                    {{ strtoupper(substr($item->employee->user->name, 0, 2)) }}
                                                </div>
                                                {{ $item->employee->user->name }}
                                            </div>
                                        </td>
                                        <td>{{ optional($item->supervisor)->user->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $item->type }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $item->start_date?->format('d-M-Y') }}
                                                <i class="fas fa-arrow-right mx-1"></i>
                                                {{ $item->end_date?->format('d-M-Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge =
                                                    [
                                                        'Pending' => 'warning',
                                                        'Approved' => 'success',
                                                        'Rejected' => 'danger',
                                                    ][$item->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusBadge }}">{{ $item->status }}</span>
                                        </td>
                                        <td>
                                            @if ($item->replacement)
                                                <div class="d-flex flex-column">
                                                    <small>{{ $item->replacement->user->name }}</small>
                                                    @php
                                                        $repBadge =
                                                            [
                                                                \App\Helpers\ReplacementStatus::Pending->value =>
                                                                    'warning',
                                                                \App\Helpers\ReplacementStatus::Accepted->value =>
                                                                    'success',
                                                                \App\Helpers\ReplacementStatus::Rejected->value =>
                                                                    'danger',
                                                            ][$item->replacement_status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $repBadge }} mt-1"
                                                        style="font-size: 0.7em;">
                                                        {{ $item->replacement_status }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @php
                                                    $allRoles = Auth::user()->hasAllRoles(
                                                        'Admin|Super|User|Staff|Parent|Student',
                                                    );
                                                @endphp
                                                {{-- @hasanyrole('Staff|HR')
                                                    <a href="{{ route('leave.application.show', $item->id) }}"
                                                        class="btn btn-success" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endhasanyrole --}}
                                                @if (!$allRoles || (Auth::user()->hasAnyRole('Staff|HR')))
                                                    <a href="{{ route('leave.application.show', $item->id) }}"
                                                        class="btn btn-success" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif

                                                @hasanyrole('Admin|Super')
                                                    <a href="{{ route('leave.application.edit', $item->id) }}"
                                                        class="btn btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('leave.application.destroy', $item->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this application?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endhasanyrole

                                                @hasanyrole(['Admin', 'Super', 'HR'])
                                                    @php
                                                        $user = Auth::user();
                                                        $needsApproval =
                                                            $item->employee_id != $user->id &&
                                                            $item->status == 'Pending' &&
                                                            $item->approvals->where('signed_at', null)->first();
                                                    @endphp
                                                    @if ($needsApproval)
                                                        <a href="{{ route('leave.approvals.form.new', ['application' => $item]) }}"
                                                            class="btn btn-primary" title="Review Application">
                                                            <i class="fas fa-gavel me-1"></i>Review
                                                        </a>
                                                    @endif
                                                @endhasanyrole

                                                {{-- Applicant edit for rejected replacement --}}
                                                @php
                                                    $isApplicant = $item->employee_id == Auth::user()->staff?->id;
                                                @endphp
                                                @if ($isApplicant && $item->replacement_status === \App\Helpers\ReplacementStatus::Rejected->value)
                                                    <a href="{{ route('leave.application.edit', $item->id) }}"
                                                        class="btn btn-warning" title="Change Replacement">
                                                        <i class="fas fa-user-clock"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No leave applications found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{--
                    @if ($allApplications->hasPages())
                        <div class="card-footer clearfix">
                            {{ $allApplications->links('pagination::bootstrap-5') }}
                        </div>
                    @endif --}}
                </div>

            </div>
        </div>
    </div>
@endsection
