@extends('layouts.main')

@section('header')
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="m-0 text-dark">
                            <i class="fas fa-calendar-alt mr-2 text-indigo"></i>
                            Holiday Calendar
                        </h2>
                        <p class="text-muted mb-0">Manage and view all school holidays</p>
                    </div>
                    <div>
                        <a href="{{ route('holiday-calendars.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus mr-2"></i> Add Holiday
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <div class="small-box text-bg-primary">
                    <div class="inner py-3">
                        <h3
                        id="stat-total"
                        >{{ $total }}</h3>
                        <p>Total Holidays</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-800 dark:text-white small-box-icon position-absolute top-0 end-0 m-3"
                        style="width: 60px; height: 60px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <div class="small-box text-bg-warning">
                    <div class="inner py-3">
                        <h3
                        id="stat-public"
                        >{{ $publicHolidays }}</h3>
                        <p>Public Holidays</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-800 dark:text-white small-box-icon position-absolute top-0 end-0 m-3"
                        style="width: 60px; height: 60px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z" />
                    </svg>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <div class="small-box text-bg-danger">
                    <div class="inner py-3">
                        <h3
                        id="stat-school"
                        >{{ $schoolHolidays }}</h3>
                        <p>School Holidays</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-800 dark:text-white small-box-icon position-absolute top-0 end-0 m-3"
                        style="width: 60px; height: 60px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 20v-9l-4 1.125V20h4Zm0 0h8m-8 0V6.66667M16 20v-9l4 1.125V20h-4Zm0 0V6.66667M18 8l-6-4-6 4m5 1h2m-2 3h2" />
                    </svg>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <div class="small-box text-bg-success">
                    <div class="inner py-3">
                        <h3
                        id="stat-national"
                        >{{ $nationalHolidays }}</h3>
                        <p>National Holidays</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-800 dark:text-white small-box-icon position-absolute top-0 end-0 m-3"
                        style="width: 60px; height: 60px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M13.09 3.294c1.924.95 3.422 1.69 5.472.692a1 1 0 0 1 1.438.9v9.54a1 1 0 0 1-.562.9c-2.981 1.45-5.382.24-7.25-.701a38.739 38.739 0 0 0-.622-.31c-1.033-.497-1.887-.812-2.756-.77-.76.036-1.672.357-2.81 1.396V21a1 1 0 1 1-2 0V4.971a1 1 0 0 1 .297-.71c1.522-1.506 2.967-2.185 4.417-2.255 1.407-.068 2.653.453 3.72.967.225.108.443.216.655.32Z" />
                    </svg>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <div class="small-box text-bg-info">
                    <div class="inner py-3">
                        <h3
                        id="stat-religious"
                        >{{ $religiousHolidays }}</h3>
                        <p>Religious Holidays</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-800 dark:text-white small-box-icon position-absolute top-0 end-0 m-3"
                        style="width: 60px; height: 60px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 640 640">
                        <path
                            d="M448 128C448 92.7 419.3 64 384 64C348.7 64 320 92.7 320 128C320 163.3 348.7 192 384 192C419.3 192 448 163.3 448 128zM328.7 328L351.6 359.5C358.1 368.4 367.9 374.2 378.8 375.6C389.7 377 400.7 373.9 409.2 366.9L497.2 294.9C514.3 280.9 516.8 255.7 502.8 238.6C488.8 221.5 463.6 219 446.5 233L391.3 278.2L365.1 242.2C349.5 220.7 324.5 208 297.9 208C267 208 238.7 225.1 224.3 252.4L175.8 344.9C155.6 383.4 166.4 430.8 201.4 456.7L254.6 496L168 496C145.9 496 128 513.9 128 536C128 558.1 145.9 576 168 576L376 576C393.3 576 408.6 564.9 414 548.5C419.4 532.1 413.7 514.1 399.8 503.8L283.7 418L328.7 328z" />
                    </svg>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <div class="small-box text-bg-secondary">
                    <div class="inner py-3">
                        <h3
                        id="stat-other"
                        >{{ $otherHolidays }}</h3>
                        <p>Other Holidays</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-800 dark:text-white small-box-icon position-absolute top-0 end-0 m-3"
                        style="width: 60px; height: 60px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-1 ">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap justify-content-center">
                            <span class="font-weight-bold mb-2"><i class="fas fa-filter mr-1"></i>
                                Filter:&nbsp;&nbsp;</span>

                            <button class="btn btn-sm btn-primary holiday-filter active mb-2" data-filter="all"
                                onclick="toggleFilter('all')">
                                {{-- <i class="fas fa-check mr-1"></i> --}}
                                All
                            </button>
                            <button class="btn btn-sm holiday-filter active mb-2" data-filter="Public Holiday"
                                style="background-color: #4DB6AC; border:none;" onclick="toggleFilter('Public Holiday')">
                                {{-- <i class="fas fa-landmark mr-1"></i> --}}
                                Public
                            </button>
                            <button class="btn btn-sm holiday-filter active mb-2" data-filter="School Holiday"
                                style="background-color: #FF8A65; border:none;" onclick="toggleFilter('School Holiday')">
                                {{-- <i class="fas fa-school mr-1"></i> --}}
                                School
                            </button>
                            <button class="btn btn-sm holiday-filter active mb-2"
                                style="background-color: #9575CD; border:none;" data-filter="Religious Holiday"
                                onclick="toggleFilter('Religious Holiday')">
                                {{-- <i class="fas fa-pray mr-1"></i> --}}
                                Religious
                            </button>
                            <button class="btn btn-sm holiday-filter active mb-2"
                                style="background-color: #81C784; border:none;" data-filter="National Holiday"
                                onclick="toggleFilter('National Holiday')">
                                {{-- <i class="fas fa-flag mr-1"></i> --}}
                                National
                            </button>
                            <button class="btn btn-sm holiday-filter active mb-2"
                                style="background-color: #FFD54F; border:none;" data-filter="Other"
                                onclick="toggleFilter('Other')">
                                {{-- <i class="fas fa-question mr-1"></i> --}}
                                Other
                            </button>

                            <div class="ml-auto mb-2">
                                <button class="btn btn-sm btn-outline-indigo" onclick="calendarRefetch()">
                                    <i class="fas fa-sync-alt mr-1"></i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Card -->
        <div class="row">
            <div class="col-12">
                <div class="card card-indigo">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar mr-2"></i>
                            Calendar View
                        </h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool" onclick="changeView('dayGridMonth')"
                                    title="Month View">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                <button type="button" class="btn btn-tool" onclick="changeView('dayGridWeek')"
                                    title="Week View">
                                    <i class="fas fa-calendar-week"></i>
                                </button>
                                <button type="button" class="btn btn-tool" onclick="changeView('listMonth')"
                                    title="List View">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div id="holidayCalendar"></div>

                    </div>
                    <div class="card-footer text-muted">
                        <small><i class="fas fa-info-circle mr-1"></i> Click on a holiday to view details. Click on an
                            empty date to add a new holiday.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Holiday Detail Modal -->
    <div class="modal fade" id="holidayModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header holiday-modal-header">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-calendar-day mr-2"></i>
                        <span id="modalTitle">Holiday Details</span>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div id="modalDateBadge" class="d-inline-block px-4 py-2 rounded bg-light">
                            <h4 class="m-0 text-indigo font-weight-bold" id="modalDate">Date</h4>
                        </div>
                    </div>

                    <table class="table table-borderless">
                        <tr>
                            <td class="font-weight-bold text-muted" width="30%">Name:</td>
                            <td id="modalName" class="font-weight-bold text-lg"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-muted">Type:</td>
                            <td><span id="modalType" class="badge badge-lg"></span></td>
                        </tr>
                        <tr id="descriptionRow" style="display: none;">
                            <td class="font-weight-bold text-muted">Description:</td>
                            <td id="modalDescription"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-muted">Day:</td>
                            <td id="modalDay"></td>
                        </tr>
                        <tr id="modalCreatedRow">
                            <td class="font-weight-bold text-muted">Added:</td>
                            <td id="modalCreated"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer bg-light">
                    <a href="{{ route('holiday-calendars.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <a href="#" id="modalEditBtn" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form id="modalDeleteForm" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this holiday?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Modal -->
    <div class="modal fade" id="quickAddModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Add Holiday
                    </h5>

                </div>
                <form action="{{ route('holiday-calendars.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="required-field">Holiday Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. New Holiday"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="required-field">Date</label>
                            <input type="date" name="date" id="quickAddDate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="required-field">Type</label>
                            <select name="type" class="form-control" required>
                                <option value="">-- Select Type --</option>
                                @foreach (array_column(\App\Helpers\HolidayType::cases(), 'value') as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_recurring">Is Recurring?</label>
                            <select name="is_recurring" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Recurring Pattern</label>
                            <select name="recurring_pattern" id="recurringPattern" class="form-control">
                                @foreach (\App\Helpers\RecurringPattern::cases() as $pattern)
                                    <option value="{{ $pattern->value }}">{{ $pattern->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Show explanation based on selection -->
                        <div id="recurringExplanation" class="alert alert-info mt-2" style="display: none;">
                            This holiday will automatically appear every year.
                        </div>
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Additional details about the holiday"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('holiday-calendars.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Save Holiday
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('fullcalendar/dist/index.global.min.js') }}"></script>
    <script src="{{ asset('fullcalendar/packages/daygrid/index.global.min.js') }}"></script>
    <script src="{{ asset('fullcalendar/packages/interaction/index.global.min.js') }}"></script>
    <script src="{{ asset('fullcalendar/packages/list/index.global.min.js') }}"></script>
    <script>
        document.getElementById('recurringPattern').addEventListener('change', function() {
            const explanation = document.getElementById('recurringExplanation');
            explanation.style.display = this.value !== 'none' ? 'block' : 'none';
        });
    </script>
   <script>
    let calendar;
    let activeFilters = new Set(['all']);
    let allEvents = [];

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('holidayCalendar');

        if (!calendarEl) {
            console.error('Calendar element not found!');
            return;
        }

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            height: 'auto',
            selectable: true,
            selectMirror: true,
            dayMaxEvents: 3,
            moreLinkClick: 'popover',
            weekends: true,

            eventSourceFailure: function(error) {
                console.error('Event source failed:', error);
            },

            events: function(fetchInfo, successCallback, failureCallback) {
                console.log('Fetching events...');


                if (allEvents.length > 0) {
                    console.log('Using cached events:', allEvents.length);
                    successCallback(filterEvents(allEvents));
                    return;
                }

                fetch('{{ route('holiday-calendars.events') }}')
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) throw new Error('Network response was not ok: ' +
                            response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Received data:', data);
                        allEvents = data;
                        successCallback(filterEvents(allEvents));
                    })
                    .catch(error => {
                        console.error('Error fetching holidays:', error);
                        failureCallback(error);
                    });
            },

            eventClick: function(info) {
                info.jsEvent.preventDefault();
                showHolidayModal(info.event);
            },

            dateClick: function(info) {
                showQuickAddModal(info.date);
            }
        });

        calendar.render();
        console.log('Calendar rendered');
    });


    function toggleFilter(filterType) {
        const btn = document.querySelector(`[data-filter="${filterType}"]`);
        if (!btn) return;

        const allTypes = ['Public Holiday', 'School Holiday', 'Religious Holiday', 'National Holiday', 'Other'];
        const allBtn = document.querySelector('[data-filter="all"]');

        if (filterType === 'all') {

            if (activeFilters.has('all')) {

                activeFilters.clear();
                document.querySelectorAll('.holiday-filter').forEach(b => {
                    b.classList.remove('active');
                    b.classList.add('inactive');
                });
            } else {

                activeFilters = new Set(['all', ...allTypes]);
                document.querySelectorAll('.holiday-filter').forEach(b => {
                    b.classList.add('active');
                    b.classList.remove('inactive');
                });
            }
        } else {

            if (activeFilters.has(filterType)) {

                activeFilters.delete(filterType);
                btn.classList.remove('active');
                btn.classList.add('inactive');


                activeFilters.delete('all');
                if (allBtn) {
                    allBtn.classList.remove('active');
                    allBtn.classList.add('inactive');
                }
            } else {

                activeFilters.add(filterType);
                btn.classList.add('active');
                btn.classList.remove('inactive');


                const allIndividualsSelected = allTypes.every(t => activeFilters.has(t));
                if (allIndividualsSelected) {
                    activeFilters.add('all');
                    if (allBtn) {
                        allBtn.classList.add('active');
                        allBtn.classList.remove('inactive');
                    }
                }
            }
        }

        console.log('Active filters:', Array.from(activeFilters));


        if (calendar) {
            calendar.removeAllEvents();
            const newFiltered = filterEvents(allEvents);
            calendar.addEventSource(newFiltered);
        }
    }

    function filterEvents(events) {
        if (!events || events.length === 0) return [];


        if (activeFilters.has('all')) return events;


        return events.filter(event => {
            const type = event.extendedProps?.type;
            return activeFilters.has(type);
        });
    }

    function changeView(viewName) {
        if (calendar) {
            calendar.changeView(viewName);
        }
    }

    function calendarRefetch() {
        
        allEvents = [];
        if (calendar) {
            calendar.refetchEvents();
        }
    }

    function showHolidayModal(event) {
        const props = event.extendedProps || {};
        const date = new Date(event.start);

        document.getElementById('modalTitle').textContent = event.title || 'Holiday Details';
        document.getElementById('modalName').textContent = event.title || '';
        document.getElementById('modalDate').textContent = date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('modalDay').textContent = date.toLocaleDateString('en-US', {
            weekday: 'long'
        });

        const typeBadge = document.getElementById('modalType');
        typeBadge.textContent = props.type || 'Unknown';
        typeBadge.style.cssText = getBadgeClass(props.type);
        typeBadge.className = 'badge badge-lg';

        const descriptionRow = document.getElementById('descriptionRow');
        const modalDescription = document.getElementById('modalDescription');

        if (props.description && props.description.trim() !== '') {
            descriptionRow.style.display = 'table-row';
            modalDescription.textContent = props.description;
        } else {
            descriptionRow.style.display = 'none';
            modalDescription.textContent = '';
        }

        const createdRow = document.getElementById('modalCreatedRow');
        if (props.created_at && createdRow) {
            const createdDate = new Date(props.created_at);
            document.getElementById('modalCreated').textContent = createdDate.toLocaleDateString();
            createdRow.style.display = 'table-row';
        } else if (createdRow) {
            createdRow.style.display = 'none';
        }

        document.getElementById('modalEditBtn').href = '{{ url('holiday-calendars') }}/' + event.id + '/edit';
        document.getElementById('modalDeleteForm').action = '{{ url('holiday-calendars') }}/' + event.id;

        const modalEl = document.getElementById('holidayModal');
        if (typeof $ !== 'undefined') {
            $(modalEl).modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modalEl).show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
        }
    }

    function showQuickAddModal(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        document.getElementById('quickAddDate').value = `${year}-${month}-${day}`;

        const modalEl = document.getElementById('quickAddModal');
        if (typeof $ !== 'undefined') {
            $(modalEl).modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modalEl).show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
        }
    }

    function getBadgeClass(type) {
        const classes = {
            'Public Holiday': 'background-color: #4DB6AC; color: white;',
            'School Holiday': 'background-color: #FF8A65; color: white;',
            'Religious Holiday': 'background-color: #9575CD; color: white;',
            'National Holiday': 'background-color: #81C784; color: white;',
            'Other': 'background-color: #FFD54F; color: #333;'
        };
        return classes[type] || 'background-color: #64B5F6; color: white;';
    }
</script>
@endsection
