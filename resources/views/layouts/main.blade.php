<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('app.name') }} | @yield('title', 'App')</title>

    @include('includes.metas')

    @include('includes.header')

    @yield('header')

</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        @include('includes.navbar')
        @include('includes.sidebar')

        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            @include('includes.page-header')
            <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->

        </main>
        <!--end::App Main-->

        @include('includes.footer')
    </div>
    <!--end::App Wrapper-->
    @include('includes.javascript')
    @yield('javascript')
</body>
<!--end::Body-->

</html>
