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
