<!--begin::Header-->
<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Home</a></li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li>
        </ul>
        <!--end::Start Navbar Links-->
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
            <!--begin::Theme Switch-->
            @include('includes.widgets.theme_switcher')
            <!--end::Theme Switch-->

            <!--begin::Navbar Search-->
            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="bi bi-search"></i>
                </a>
            </li>
            <!--end::Navbar Search-->
            <!--begin::Messages Dropdown Menu-->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-chat-text"></i>
                    <span class="navbar-badge badge text-bg-danger">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <a href="#" class="dropdown-item">
                        <!--begin::Message-->
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="./assets/img/user1-128x128.jpg" alt="User Avatar"
                                    class="img-size-50 rounded-circle me-3" />
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    Brad Diesel
                                    <span class="float-end fs-7 text-danger"><i class="bi bi-star-fill"></i></span>
                                </h3>
                                <p class="fs-7">Call me whenever you can...</p>
                                <p class="fs-7 text-secondary">
                                    <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                                </p>
                            </div>
                        </div>
                        <!--end::Message-->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <!--begin::Message-->
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="./assets/img/user8-128x128.jpg" alt="User Avatar"
                                    class="img-size-50 rounded-circle me-3" />
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    John Pierce
                                    <span class="float-end fs-7 text-secondary">
                                        <i class="bi bi-star-fill"></i>
                                    </span>
                                </h3>
                                <p class="fs-7">I got your message bro</p>
                                <p class="fs-7 text-secondary">
                                    <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                                </p>
                            </div>
                        </div>
                        <!--end::Message-->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <!--begin::Message-->
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="./assets/img/user3-128x128.jpg" alt="User Avatar"
                                    class="img-size-50 rounded-circle me-3" />
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    Nora Silvester
                                    <span class="float-end fs-7 text-warning">
                                        <i class="bi bi-star-fill"></i>
                                    </span>
                                </h3>
                                <p class="fs-7">The subject goes here</p>
                                <p class="fs-7 text-secondary">
                                    <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                                </p>
                            </div>
                        </div>
                        <!--end::Message-->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                </div>
            </li>
            <!--end::Messages Dropdown Menu-->
            <!--begin::Notifications Dropdown Menu-->
            @php
                $userStaffId = auth()->user()->staff?->id;
                $replacementKey = 'replacement_notification_' . $userStaffId;
                $allSession = session()->all();
                $pendingReplacement = \App\Models\leave_application::where(
                    'replacement_employee_id',
                    auth()->user()->staff?->id,
                )
                    ->where('replacement_status', \App\Helpers\ReplacementStatus::Pending->value)
                    ->with('employee.user')
                    ->latest()
                    ->first();
                $notificationCount = $pendingReplacement ? 1 : 0;
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-bell-fill"></i>
                    @if ($notificationCount > 0)
                        <span class="navbar-badge badge text-bg-warning">{{ $notificationCount }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <span class="dropdown-item dropdown-header">{{ $notificationCount }} Notifications</span>
                    @if ($pendingReplacement)
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('leave.applications.index') }}" class="dropdown-item">
                            <i class="bi bi-envelope me-2"></i>
                            <strong>Replacement Request</strong>
                            <div class="text-secondary fs-7 ps-4">
                                Cover for {{ $pendingReplacement->employee->user->name }}
                            </div>
                            <div class="text-secondary fs-7 ps-4 italic">
                                {{ $pendingReplacement->start_date?->format('d M') }} -
                                {{ $pendingReplacement->end_date?->format('d M') }}
                            </div>
                        </a>
                    @else
                        <p class="dropdown-item text-center text-muted small">No new notifications</p>
                    @endif
                </div>
            </li>
            <!--end::Notifications Dropdown Menu-->
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!-- Default switch -->
            <li class="nav-item d-flex align-items-center">
                <i class="bi bi-sun-fill text-warning"></i>
                <div class="form-check form-switch ms-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="themeSwitcher">
                </div>
                <i class="bi bi-moon-fill ms-1"></i>
            </li>
            <!--end::Default switch -->
            <!--begin::User Menu Dropdown ./assets/img/user2-160x160.jpg-->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="{{ asset('theme/src/assets/img/user2-160x160.jpg') }}"
                        class="user-image rounded-circle shadow" alt="User Image" />
                    <span class="d-none d-md-inline">Alexander Pierce</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <!--begin::User Image-->
                    <li class="user-header text-bg-primary">
                        <img src="./assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image" />
                        <p>
                            Alexander Pierce - Web Developer
                            <small>Member since Nov. 2023</small>
                        </p>
                    </li>
                    <!--end::User Image-->
                    <!--begin::Menu Body-->
                    <li class="user-body">
                        <!--begin::Row-->
                        <div class="row">
                            <div class="col-4 text-center"><a href="#">Followers</a></div>
                            <div class="col-4 text-center"><a href="#">Sales</a></div>
                            <div class="col-4 text-center"><a href="#">Friends</a></div>
                        </div>
                        <!--end::Row-->
                    </li>
                    <!--end::Menu Body-->
                    <!--begin::Menu Footer-->
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                        <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-end">Sign out</a>
                    </li>
                    <!--end::Menu Footer-->
                </ul>
            </li>
            <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
</nav>
<!--end::Header-->
