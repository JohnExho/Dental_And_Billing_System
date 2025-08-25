<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Unnamed | Comply')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @stack('styles')
    <style>
        /* main-content shifts when sidebar is present by default */
        .main-content {
            transition: margin-left .28s ease, transform .28s ease;
            min-height: 100vh;
            margin-left: 240px;
            /* default offset for the visible sidebar */
        }

        /* collapsed state: don't fully hide — shrink to compact width */
        .sidebar-wrapper {
            transition: width .28s ease, left .28s ease;
        }

        body.sidebar-collapsed .sidebar-wrapper {
            width: 64px;
            /* compact width when "collapsed" */
            left: 0;
            /* keep it visible */
        }

        /* when compacted use smaller offset for main content */
        body.sidebar-collapsed .main-content {
            margin-left: 64px;
        }

        /* small toggle button */
        #sidebarToggle {
            z-index: 1100;
            border: none;
            background: #fff;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .08);
        }
    </style>
</head>

<body>
@php
$invalidRoute = ['login', 'forgot-password', 'confirm-otp', 'reset-password'];
@endphp

@if ($errors->any())
    <div class="alert alert-danger" style="z-index: 99999;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    @unless (Route::is($invalidRoute))
        @include('components.navbar')
    @endunless
    <div class="d-flex">
        @unless (Route::is($invalidRoute))
            @include('components.sidebar')
        @endunless


        @if (Route::is($invalidRoute))
            <div class=" flex-grow-1">
        @else
                <div class=" flex-grow-1 main-content">
            @endif
                @yield('content')
                @yield('modals')
            </div>
        </div>
        @if (Session::has('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ Session::get('success') }}',
                    position: 'top-right',
                    timer: 3000,
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            </script>
        @endif

        @if (Session::has('error') || $errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ Session::get('error') }}',
                    position: 'top-right',
                    timer: 3000,
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            </script>
        @endif
        <script src="{{ asset('js/app.js') }}"></script>
        @stack('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // no need to add a 'has-sidebar' class since sidebar is assumed present by default
                const btn = document.getElementById('sidebarToggle');
                if (btn) {
                    btn.setAttribute('aria-expanded', 'true');
                    btn.addEventListener('click', function () {
                        const collapsed = document.body.classList.toggle('sidebar-collapsed');
                        btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    });
                }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>