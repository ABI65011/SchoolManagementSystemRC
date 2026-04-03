@extends('layouts.main')
@section('header')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
            }

            .badge {
                border: 1px solid #888;
            }
        }
    </style>
@endsection
@section('content')

    <div class="container-fluid py-5" id="print-area">
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="{{ asset('storage/' . $student->identification_image) }}" alt="avatar"
                            class="rounded-circle img-fluid" style="width: 150px; height: 150px;">
                        <h5 class="my-3">
                            {{ $student->last_name . ' ' . $student->first_name . ' ' . ($student->middle_name ?? '') }}
                        </h5>

                        <p class="text-muted mb-1">{{ $student->user->role ?? 'Student' }}</p>
                        <div class="d-flex justify-content-center mb-2 no-print">
                            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-success"><a
                                    href="{{ route('students.edit', $student) }}"
                                    class="text-white text-decoration-none">Edit Profile</a></button>
                            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-warning ms-3"
                                onclick="printProfile()">Print Profile</button>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 mb-lg-0">
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush rounded-3">
                            @if ($student->spoken_languages)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="bi bi-globe h5 text-primary"></i>
                                    <div class="text-end">
                                        <p class="mb-0"><strong>Spoken Languages:</strong></p>
                                        @php
                                            $languages = is_array($student->spoken_languages)
                                                ? $student->spoken_languages
                                                : json_decode($student->spoken_languages, true);
                                            $langNames = [];
                                            if (is_array($languages)) {
                                                foreach ($languages as $code) {
                                                    $langNames[] = languages()[$code] ?? $code;
                                                }
                                            }
                                        @endphp
                                        <p class="mb-0">{{ implode(', ', $langNames) }}</p>
                                    </div>
                                </li>
                            @endif

                            @if ($student->religious_affiliation)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fas fa-pray fa-lg text-warning"></i>
                                    <div class="text-end">
                                        <p class="mb-0"><strong>Religious Affiliation:</strong></p>
                                        <p class="mb-0">{{ $student->religious_affiliation }}</p>
                                    </div>
                                </li>
                            @endif

                            @if ($student->citizenship)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fa-solid fa-flag fa-lg text-info"></i>
                                    <div class="text-end">
                                        <p class="mb-0"><strong>Citizenship :</strong></p>
                                        @php
                                            $citizenships = is_array($student->citizenship)
                                                ? $student->citizenship
                                                : json_decode($student->citizenship, true);
                                        @endphp
                                        @if (is_array($citizenships))
                                            {{ implode(
                                                ', ',
                                                array_map(function ($code) {
                                                    $countries = countries();
                                                    return $countries[$code] ?? $code;
                                                }, $citizenships),
                                            ) }}
                                        @else
                                            {{ $student->citizenship }}
                                        @endif
                                    </div>
                                </li>
                            @endif
                            @if ($student->careerAspiration && $student->careerAspiration->aspiration)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fa-solid fa-bullseye fa-lg text-warning"></i>
                                    <div class="text-end">
                                        <p class="mb-0"><strong>Career Aspiration:</strong></p>
                                        <div>{{ $student->careerAspiration->aspiration }}</div>
                                    </div>
                                </li>
                            @endif
                            @if ($student->careerAspiration && $student->careerAspiration->best_done_subjects)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fas fa-graduation-cap fa-lg text-success"></i>
                                    <div class="text-end">
                                        <p class="mb-0"><strong>Best Subjects:</strong></p>
                                        @php
                                            $bestSubjects = is_array($student->careerAspiration->best_done_subjects)
                                                ? $student->careerAspiration->best_done_subjects
                                                : json_decode($student->careerAspiration->best_done_subjects, true);
                                        @endphp
                                        @if (is_array($bestSubjects))
                                            <small>{{ implode(', ', $bestSubjects) }}</small>
                                        @endif
                                    </div>
                                </li>
                            @endif

                            @if ($student->careerAspiration && $student->careerAspiration->worst_done_subjects)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fas fa-book fa-lg text-danger"></i>
                                    <div class="text-end">
                                        <p class="mb-0"><strong>Challenging Subjects:</strong></p>
                                        @php
                                            $worstSubjects = is_array($student->careerAspiration->worst_done_subjects)
                                                ? $student->careerAspiration->worst_done_subjects
                                                : json_decode($student->careerAspiration->worst_done_subjects, true);
                                        @endphp
                                        @if (is_array($worstSubjects))
                                            <small>{{ implode(', ', $worstSubjects) }}</small>
                                        @endif
                                    </div>
                                </li>
                            @endif

                            @if ($student->careerAspiration && $student->careerAspiration->favorite_subjects)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fas fa-heart fa-lg text-danger"></i>
                                    <div class="text-end">
                                        <p class="mb-0"><strong>Favorite Subjects:</strong></p>
                                        @php
                                            $favoriteSubjects = is_array($student->careerAspiration->favorite_subjects)
                                                ? $student->careerAspiration->favorite_subjects
                                                : json_decode($student->careerAspiration->favorite_subjects, true);
                                        @endphp
                                        @if (is_array($favoriteSubjects))
                                            <small>{{ implode(', ', $favoriteSubjects) }}</small>
                                        @endif
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                {{-- Student info card --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent py-3 d-flex align-items-center">

                        <div>
                            <h5 class="mb-0 fw-bold ">
                                {{ $student->last_name . ' ' . $student->first_name . ' ' . ($student->middle_name ?? '') }}
                            </h5>
                            <small class="text-muted">{{ $student->user->email ?? '—' }}</small>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-6 col-lg-4">
                                <div class="text-uppercase fw-semibold small text-muted mb-1">Student ID</div>
                                <div>{{ $student->id_no ?? '—' }} <span
                                        class="text-muted">({{ $student->id_type ?? '—' }})</span></div>
                            </div>

                            <div class="col-6 col-lg-4">
                                <div class="text-uppercase fw-semibold small text-muted mb-1">Gender</div>
                                <div>{{ $student->gender ?? '—' }}</div>
                            </div>

                            <div class="col-6 col-lg-4">
                                <div class="text-uppercase fw-semibold small text-muted mb-1">Date of Birth</div>
                                <div>
                                    {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('F j, Y') : '—' }}
                                </div>
                            </div>

                            <div class="col-6 col-lg-4">
                                <div class="text-uppercase fw-semibold small text-muted mb-1">Admission Year</div>
                                <div>{{ $student->admission_year ?? '—' }}</div>
                            </div>

                            <div class="col-6 col-lg-4">
                                <div class="text-uppercase fw-semibold small text-muted mb-1">Joining Class</div>
                                <div>{{ $student->joining_class ?? '—' }}</div>
                            </div>

                            @if ($student->a_level_combination)
                                <div class="col-6 col-lg-4">
                                    <div class="text-uppercase fw-semibold small text-muted mb-1">A-Level Combination
                                    </div>
                                    <div><span class="badge bg-primary">{{ $student->a_level_combination }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="col-6 col-lg-4">
                                <div class="text-uppercase fw-semibold small text-muted mb-1">Section</div>
                                <div>{{ $student->applying_section ?? '—' }}</div>
                            </div>

                            @if ($student->additional_info)
                                <div class="col-12">
                                    <div class="text-uppercase fw-semibold small text-muted mb-1">Additional Info</div>
                                    <div>{{ $student->additional_info }}</div>
                                </div>
                            @endif

                        </div>{{-- row --}}

                        @if ($student->id_image_path)
                            <div class="d-flex justify-content-end mt-3 no-print">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#idModal">
                                    <i class="bi bi-eye me-1"></i> View ID
                                </button>
                            </div>
                        @endif
                    </div>{{-- card-body --}}
                </div>{{-- card --}}

                <div class="row">
                    <div class="row">
                        {{-- Academic History Card --}}
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-transparent py-3">
                                    <h6 class="mb-0 fw-semibold text-primary">
                                        <i class="bi bi-journal-text me-2 text-primary-emphasis"></i>Academic History
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    @forelse ($student->academicHistories as $ac)
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <div class="fw-semibold">{{ $ac['academic_level'] }}
                                                    @if ($ac['school_name'])
                                                        <span class="text-muted">- {{ $ac['school_name'] }}</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">
                                                    {{ $ac['from_year'] }}@if ($ac['to_year'])
                                                        - {{ $ac['to_year'] }}
                                                    @endif
                                                    @if ($ac['grade'])
                                                        · Grade: <span
                                                            class="badge bg-secondary">{{ $ac['grade'] }}</span>
                                                    @endif
                                                    @if ($ac['aggregate_score'])
                                                        · Score: <span
                                                            class="badge bg-info">{{ $ac['aggregate_score'] }}</span>
                                                    @endif
                                                </small>
                                            </div>

                                            {{-- file preview button --}}
                                            @php
                                                $files = [
                                                    'ple' => $ac['ple_file'] ?? null,
                                                    'o' => $ac['o_level_file'] ?? null,
                                                    'other' => $ac['other_file'] ?? null,
                                                ];
                                            @endphp
                                            @if (array_filter($files))
                                                <div class="dropstart no-print">
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-paperclip"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @foreach ($files as $label => $path)
                                                            @if ($path)
                                                                @php
                                                                    $fileUrl = Storage::url($path);
                                                                    $fileExt = pathinfo($path, PATHINFO_EXTENSION);
                                                                @endphp
                                                                <li>
                                                                    <a class="dropdown-item view-document" href="#"
                                                                        data-bs-toggle="modal" data-bs-target="#fileModal"
                                                                        data-src="{{ $fileUrl }}"
                                                                        data-ext="{{ $fileExt }}"
                                                                        data-title="{{ strtoupper($label) }} File - {{ basename($path) }}">
                                                                        <i
                                                                            class="bi bi-file-earmark-{{ $fileExt === 'pdf' ? 'pdf' : 'text' }} me-2"></i>
                                                                        View {{ strtoupper($label) }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>

                                        @if (!$loop->last)
                                            <hr class="my-2">
                                        @endif
                                        @empty
                                            <p class="text-muted mb-0">No academic history recorded</p>
                                        @endforelse
                                    </div>

                                </div>
                            </div>


                            {{-- Health & Conduct Card --}}
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header bg-transparent py-3">
                                        <h6 class="mb-0 fw-semibold text-primary">
                                            <i class="bi bi-heart-pulse me-2 text-primary-emphasis"></i>Health & Conduct
                                        </h6>
                                    </div>
                                    <div class="card-body p-3">
                                        {{-- Health --}}
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="me-3 mt-1">
                                                @if ($student->medicalHistory?->has_health_issues)
                                                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                                @else
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @endif
                                            </div>
                                            <div class="grow">
                                                <div class="fw-semibold">Health Status</div>
                                                @if ($student->medicalHistory?->has_health_issues)
                                                    <p class="mb-1 small text-danger">
                                                        {{ $student->medicalHistory->health_issues }}
                                                    </p>

                                                    {{-- Handle JSON array of medical files --}}
                                                    @php
                                                        $medicalFiles = $student->medicalHistory->files ?? [];
                                                        // Decode if it's a JSON string
                                                        if (is_string($medicalFiles)) {
                                                            $decoded = json_decode($medicalFiles, true);
                                                            $medicalFiles = is_array($decoded)
                                                                ? $decoded
                                                                : [$medicalFiles];
                                                        }
                                                        $medicalFiles = is_array($medicalFiles) ? $medicalFiles : [];
                                                    @endphp

                                                    @if (count($medicalFiles) > 0)
                                                        <div class="mt-2">
                                                            <small class="text-muted">Medical Reports:</small>
                                                            @foreach ($medicalFiles as $index => $filePath)
                                                                @php
                                                                    $medFileUrl = Storage::url($filePath);
                                                                    $medFileExt = pathinfo(
                                                                        $filePath,
                                                                        PATHINFO_EXTENSION,
                                                                    );
                                                                @endphp
                                                                <a href="#"
                                                                    class="small text-decoration-none d-block view-document"
                                                                    data-bs-toggle="modal" data-bs-target="#fileModal"
                                                                    data-src="{{ $medFileUrl }}"
                                                                    data-ext="{{ $medFileExt }}"
                                                                    data-title="Medical Report {{ count($medicalFiles) > 1 ? $index + 1 : '' }} - {{ basename($filePath) }}">
                                                                    <i
                                                                        class="bi bi-file-earmark-{{ $medFileExt === 'pdf' ? 'pdf' : 'text' }}"></i>
                                                                    View report
                                                                    {{ count($medicalFiles) > 1 ? $index + 1 : '' }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="small text-success">No issues</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Discipline --}}
                                        <div class="d-flex align-items-start">
                                            <div class="me-3 mt-1">
                                                @if ($student->disciplineHistory?->has_disciplinary_issues)
                                                    <i class="bi bi-exclamation-octagon-fill text-warning"></i>
                                                @else
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @endif
                                            </div>
                                            <div class="grow">
                                                <div class="fw-semibold">Disciplinary Record</div>
                                                @if ($student->disciplineHistory?->has_disciplinary_issues)
                                                    <p class="mb-1 small text-warning">
                                                        Action: {{ $student->disciplineHistory->disciplinary_issues }}
                                                    </p>
                                                    <p class="small mb-0">{{ $student->disciplineHistory->reason }}</p>
                                                @else
                                                    <span class="small text-success">Clean record</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 justify-content-center">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-transparent py-3">
                                    <h6 class="mb-0 fw-semibold text-warning">
                                        <i class="fa-solid fa-hand-holding-heart me-2 text-warning-emphasis"></i>Sponsorship & Support
                                    </h6>
                                </div>
                                <div class="card-body p-3">

                                    <div class="d-flex align-items-start mb-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ID full-size modal --}}
        @if ($student->id_image_path)
            <div class="modal fade" id="idModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <h6 class="modal-title">
                                @if ($student->id_type == 'National ID')
                                    National ID
                                @elseif ($student->id_type == 'Passport')
                                    Passport
                                @endif
                                – {{ $student->id_no }}
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <img src="{{ Storage::url($student->id_image_path) }}" class="img-fluid" height="400">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Single reusable file viewer modal --}}
        <div class="modal fade" id="fileModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h6 class="modal-title">Document</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-3" style="min-height: 300px;">
                        <div class="document-viewer-container" style="width: 100%; height: 100%;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('javascript')
        {{-- modal helper --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fileModal = document.getElementById('fileModal');

                if (fileModal) {
                    fileModal.addEventListener('show.bs.modal', function(e) {
                        const trigger = e.relatedTarget;
                        const src = trigger.dataset.src;
                        const ext = (trigger.dataset.ext || src.split('.').pop()).toLowerCase();
                        const titleEl = fileModal.querySelector('.modal-title');
                        const bodyEl = fileModal.querySelector('.modal-body');

                        titleEl.textContent = trigger.dataset.title || 'Document';

                        let html = ``;

                        /* 1. Images – display directly */
                        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'].includes(ext)) {
                            html =
                                `<img src="${src}" class="img-fluid" alt="document" style="max-height: 70vh;">`;
                        }
                        /* 2. Office – MS Office online viewer */
                        else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(ext)) {
                            const msUrl =
                                `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(src)}`;
                            html =
                                `<iframe src="${msUrl}" width="100%" height="600px" frameborder="0"></iframe>`;
                        }
                        /* 3. PDF – browser inline */
                        else if (ext === 'pdf') {
                            html = `<iframe src="${src}" width="100%" height="600px" frameborder="0"></iframe>`;
                        }
                        /* 4. Plain text / code */
                        else if (['txt', 'csv', 'md', 'json', 'xml', 'py', 'js', 'php', 'css', 'html'].includes(
                                ext)) {
                            fetch(src)
                                .then(r => r.text())
                                .then(t => {
                                    bodyEl.innerHTML =
                                        `<pre class="p-3 mb-0" style="max-height:70vh;overflow:auto;background:#f6f8fa">${escapeHtml(t)}</pre>`;
                                })
                                .catch(err => {
                                    bodyEl.innerHTML =
                                        `<div class="alert alert-danger">Error loading file: ${err.message}</div>`;
                                });
                            return;
                        } else {
                            const gView =
                                `https://docs.google.com/gviewer?url=${encodeURIComponent(src)}&embedded=true`;
                            html =
                                `<iframe src="${gView}" width="100%" height="600px" frameborder="0"></iframe>`;
                        }

                        bodyEl.innerHTML = html;
                    });
                }

                /* helper to escape HTML */
                function escapeHtml(str) {
                    return str.replace(/[&<>"']/g, m => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    } [m]));
                }
            });
        </script>
        <script>
            function printProfile() {
                let printContents = document.getElementById("print-area").innerHTML;
                let originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }
        </script>
    @endsection
