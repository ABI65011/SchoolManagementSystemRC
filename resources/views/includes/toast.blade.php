{{-- Add this temporarily at the top of toast.blade.php to debug --}}
@php
    $userStaffId = auth()->user()->staff?->id;
    $replacementKey = 'replacement_notification_' . $userStaffId;
    $allSession = session()->all();

    // Log to see what's happening
\Illuminate\Support\Facades\Log::info('Toast Debug', [
    'user_staff_id' => $userStaffId,
    'replacement_key' => $replacementKey,
    'session_has_key' => session()->has($replacementKey),
    'all_session_keys' => array_keys($allSession),
    'replacement_toast_data' => session($replacementKey),
    ]);
@endphp

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    @if (session('success'))
        <div id="toastSuccess" class="toast toast-success align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="toastError" class="toast toast-error align-items-center text-white bg-danger border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    @endif


    @if (session('warning'))
        <div id="toastWarning" class="toast toast-warning align-items-center text-white bg-warning border-0"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    @endif


    @php
        $pendingReplacement = \App\Models\leave_application::where('replacement_employee_id',auth()->user()->staff?->id,)->where('replacement_status', \App\Helpers\ReplacementStatus::Pending->value)->with('employee.user')->latest()->first();
    @endphp

    @if ($pendingReplacement && !session('replacement_toast_dismissed_' . $pendingReplacement->id))
        <div id="toastReplacement" class="toast align-items-center text-dark bg-warning border-0" role="alert"
            aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-person-workspace me-2"></i>
                    <strong>Replacement Request</strong><br>
                    <small>Cover for {{ $pendingReplacement->employee->user->name }}
                        ({{ $pendingReplacement->start_date?->format('d-M-Y') }} to
                        {{ $pendingReplacement->end_date?->format('d-M-Y') }})</small>
                    <div class="mt-2">
                        <a href="{{ route('leave.applications.index') }}" class="btn btn-sm btn-dark">
                            Respond Now
                        </a>
                        <button type="button" class="btn btn-sm btn-secondary"
                            onclick="dismissReplacementToast({{ $pendingReplacement->id }})">
                            Dismiss
                        </button>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-dark me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    @endif

</div>
