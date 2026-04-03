<!--begin::Sidebar-->
<aside class="app-sidebar bg-body shadow">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="./index.html" class="brand-link">
            <!--begin::Brand Image assets/img/AdminLTELogo.png-->
            <img src="{{ asset('theme/src/assets/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">AdminLTE 4</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link  {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('students.index') }}"
                        class="nav-link  {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="bi bi-person-rolodex"></i>
                        <p>Students</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('staff.index') }}"
                        class="nav-link  {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                        <i class="bi bi-person-rolodex"></i>
                        <p>Staff</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admissions.index') }}"
                        class="nav-link  {{ request()->routeIs('admissions.*') ? 'active' : '' }}">
                        <i class="fas fa-door-open"></i>
                        <p>Admissions</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('exam-categories.*','exams.*','exam-results.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link  {{ request()->routeIs('exam-categories.*','exams.*','exam-results.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard-fill"></i>
                        <p>Exam Setup
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route(name: 'exam-categories.index') }}"
                                class="nav-link  {{ request()->routeIs('exam-categories.*') ? 'active' : '' }}">
                                <i class="bi bi-mortarboard-fill"></i>
                                <p>Exam Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route(name: 'exams.index') }}"
                                class="nav-link  {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                                <i class="bi bi-mortarboard-fill"></i>
                                <p>Exams</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route(name: 'exam-results.index') }}"
                                class="nav-link  {{ request()->routeIs('exam-results.*') ? 'active' : '' }}">
                                <i class="bi bi-mortarboard-fill"></i>
                                <p>Exam Results</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route(name: 'grading-scales.index') }}"
                        class="nav-link  {{ request()->routeIs('grading-scales.*') ? 'active' : '' }}">
                        <i class="fas fa-balance-scale"></i>
                        <p>Grading Scales</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route(name: 'report-cards.index') }}"
                        class="nav-link  {{ request()->routeIs('report-cards.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-bar"></i>
                        <p>Report Cards</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route(name: 'continuous-assessments.index') }}"
                        class="nav-link  {{ request()->routeIs('continuous-assessments.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <p>Continuous Assessments</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route(name: 'subjects.index') }}"
                        class="nav-link  {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i>
                        <p>Subjects</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('attendance.dashboard') }}"
                        class="nav-link  {{ request()->routeIs('attendance.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <p>Attendance Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('student-attendance.index') }}"
                        class="nav-link  {{ request()->routeIs('student-attendance.') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <p>Student Attendance</p>
                    </a>
                </li>
                @hasanyrole('Admin|Super')
                    <li class="nav-item">
                        <a href="{{ route('holiday-calendars.index') }}"
                            class="nav-link  {{ request()->routeIs('holiday-calendars.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar"></i>
                            <p>Holiday Calendar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.attendance.index') }}"
                            class="nav-link  {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                            <i class="bi bi-person"></i>
                            <p>Admin Attendance</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.attendance.location.index') }}"
                            class="nav-link  {{ request()->routeIs('admin.attendance.location.*') ? 'active' : '' }}">
                            <i class="bi bi-map"></i>
                            <p>Attendance Location</p>
                        </a>
                    </li>
                @endhasanyrole
                <li class="nav-item">
                    <a href="{{ route('leave.applications.index') }}"
                        class="nav-link  {{ request()->routeIs('leave.applications.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt fa-fw "></i>
                        <p>Leave Application</p>
                    </a>
                </li>
                {{-- <li class="nav-item {{ Request::is('reports*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::is('reports*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Application Reports
                            <i class="right fas fa-angle-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('reports.pending') }}"
                                class="nav-link {{ Request::is('reports/pending') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>Pending Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.approved') }}"
                                class="nav-link {{ Request::is('reports/approved') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Approved Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.rejected') }}"
                                class="nav-link {{ Request::is('reports/rejected') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-danger"></i>
                                <p>Rejected Reports</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->
