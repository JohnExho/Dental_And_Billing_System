<!DOCTYPE html>
<html lang="en">
{{-- php artisan db:seed Yajra\\Address\\Seeders\\AddressSeeder --}}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Unnamed | Chomply')</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    @vite(['resources/css/index.blade.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css"/>

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

        .comment {
            color: green;
        }
    </style>
</head>

<body class="sidebar-collapsed">
    @php
        $invalidRoute = ['login', 'forgot-password', 'confirm-otp', 'reset-password', 'dashboard'];
        $activeRole = session('active_role', auth()->user()?->role);
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
            @if ($activeRole === 'staff')
                @include('components.staff.sidebar')
            @else
                @include('components.admin.sidebar')
            @endif

        @endunless


        @if (Route::is($invalidRoute))
            <div class=" flex-grow-1">
            @else
                <div class=" flex-grow-1 main-content">
        @endif
        @yield('content')
        @yield('modals')
        @if (Auth::check() && $activeRole === 'staff')
            @include('pages.patients.modals.add')
        @endif

    </div>
    @unless (in_array(Route::currentRouteName(), ['login', 'forgot-password', 'confirm-otp', 'reset-password']))
        <div class="footer text-center py-2 bg-light border-top mt-4 fixed-bottom">
            <small>
                <span class="text-mute">Search and Sort functions temporarily disabled due to encrypted columns,
                    Security Over
                    Functionality</span>
            </small>
        </div>
    @endunless

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            @if (Session::has('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ Session::get('success') }}',
                    position: 'top-right',
                    timer: 2000,
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            @endif

            @if (Session::has('stock_error'))
                setTimeout(() => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Low Stock Alert',
                        text: '{{ Session::get('stock_error') }}',
                        position: 'middle',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal-lower-right'
                        }
                    });
                }, {{ Session::has('success') ? 2100 : 0 }});
            @endif

            @if (Session::has('error') || $errors->any())
                setTimeout(() => {
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
                }, {{ Session::has('success') || Session::has('stock_error') ? 4500 : 0 }});
            @endif

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const toggleBtn = document.getElementById('sidebarToggle');

            // Check for saved preference, default is collapsed
            const saved = localStorage.getItem('sidebarCollapsed');
            if (saved === 'false') {
                body.classList.remove('sidebar-collapsed');
            } else {
                body.classList.add('sidebar-collapsed');
            }

            toggleBtn?.addEventListener('click', function() {
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapsed'));
            });
        });
    </script>


</body>

</html>
