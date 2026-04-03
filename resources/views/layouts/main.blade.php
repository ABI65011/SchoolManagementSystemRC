<!doctype html>
<html lang="en">
<!--begin::Head-->

{{-- Extra line here😌 --}}

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
    <script src="https://kit.fontawesome.com/b0ddfd4740.js" crossorigin="anonymous"></script>


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
                    {{-- @include('includes.toast') --}}
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var toastElements = document.querySelectorAll('.toast');
            toastElements.forEach(function(toastEl) {
                var toast = new bootstrap.Toast(toastEl, {
                    delay: 5000,
                    autohide: true
                });
                toast.show();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: toastEl.dataset.bsAutohide !== 'false',
                    delay: 5000
                });
            });
            toastList.forEach(toast => toast.show());
        });

        function dismissReplacementToast(applicationId) {

            fetch('/dismiss-replacement-toast/' + applicationId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            bootstrap.Toast.getInstance(document.getElementById('toastReplacement')).hide();
        }
    </script>
    @include('includes.javascript')
    @yield('javascript')
</body>
<!--end::Body-->

</html>
