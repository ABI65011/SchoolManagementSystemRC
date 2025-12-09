<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('app.name') }} | @yield('title', 'App')</title>

    @include('includes.metas')

    @include('includes.header')

    @yield('header')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <!-- Select2 - Load only ONE -->
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">

    <!-- Select2 - Load only ONE version -->
    <script src="{{ asset('js/select2.full.min.js') }}"></script>


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

    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });
    </script>
    @include('includes.javascript')
    @yield('javascript')
</body>
<!--end::Body-->

</html>
