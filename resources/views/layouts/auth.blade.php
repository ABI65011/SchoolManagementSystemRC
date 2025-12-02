<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('app.name') }} | @yield('title', 'Auth')</title>

    @include('includes.metas')

    @include('includes.header')
    @yield('header')

</head>
<!--end::Head-->
<!--begin::Body-->

<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>{{ config('app.name-short') }} | </b>DASHBOARD</a>
        </div>
        <!-- /.login-logo -->
        @yield('content')
    </div>
    <!-- /.login-box -->
    @include('includes.javascript')
    @yield('javascript')
</body>
<!--end::Body-->

</html>
