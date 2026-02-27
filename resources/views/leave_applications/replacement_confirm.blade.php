@extends('layouts.main')

@section('page-title', 'Respond to Replacement Request')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-clock me-2"></i>Replacement Request Details
                        </h3>
                    </div>
                    <div class="card-body">

                        <div class="row mb-4">
                            <div class="col-md-4 text-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                                     style="width: 80px; height: 80px; font-size: 2rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5 class="mb-0">{{ $application->employee->user->name }}</h5>
                                <small class="text-muted">Applicant</small>
                            </div>
                            <div class="col-md-4 text-center d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <i class="fas fa-arrow-right fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">
                                        <span class="badge bg-info fs-6">{{ $application->type }}</span>
                                    </p>
                                    <small class="text-muted">
                                        {{ $application->start_date?->format('d-M-Y') }} → {{ $application->end_date?->format('d-M-Y') }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                                     style="width: 80px; height: 80px; font-size: 2rem;">
                                    <i class="fas fa-question"></i>
                                </div>
                                <h5 class="mb-0">Your Response</h5>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>

                        <hr>

                        <form action="{{ Route::has('leave.replacement.response') ? route('leave.replacement.response') : url('/leave/replacement/response') }}" method="POST">
                            @csrf
                            <input type="hidden" name="application_id" value="{{ $application->id }}">

                            <div class="form-group mb-4">
                                <label class="form-label fw-bold fs-5">Do you agree to cover the duties?</label>
                                <select name="replacement_status" class="form-select form-select-lg @error('replacement_status') is-invalid @enderror" required>
                                    <option value="">-- Select your response --</option>
                                    <option value="{{ \App\Helpers\ReplacementStatus::Accepted->value }}" class="text-success">
                                        ✓ Accept - I agree to cover
                                    </option>
                                    <option value="{{ \App\Helpers\ReplacementStatus::Rejected->value }}" class="text-danger">
                                        ✖ Reject - I cannot cover
                                    </option>
                                </select>
                                @error('replacement_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label fw-bold">Comment (Optional)</label>
                                <textarea name="replacement_comment" rows="4" class="form-control"
                                          placeholder="Add any comments or notes regarding your decision..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('leave.applications.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-1"></i>Back
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-1"></i>Submit Response
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
