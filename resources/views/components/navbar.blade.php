<style>
    .navbar-fixed {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1030;
        transition: padding 0.3s ease, font-size 0.3s ease, opacity 0.3s ease, visibility 0.3s ease;
    }

    .navbar-fixed.shrink {
        padding-top: 0;
        padding-bottom: 0;
        font-size: 0;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    /* ⭐ Navbar content moves with sidebar */
    .echo-movebar {
        transition: margin-left 0.3s ease;
        margin-left: 80px; /* Default collapsed sidebar width */
    }

    /* When sidebar is expanded (hover) */
    .sidebar-wrapper.expanded ~ * .echo-movebar,
    body:has(.sidebar-wrapper.expanded) .echo-movebar {
        margin-left: 240px; /* Expanded sidebar width */
    }

    body {
        padding-top: 56px;
    }

    /* Compact Add Patient button */
    .btn-add-patient {
        font-size: 0.8rem !important;
        padding: 6px 12px !important;
        line-height: 1 !important;
        height: 34px;
        display: flex;
        align-items: center;
        border-radius: 6px;
        white-space: nowrap;
    }

    /* Compact Admin dropdown */
    .nav-user-compact {
        font-size: 1rem !important;
        padding: 6px 12px !important;
        height: 45px;
        display: flex;
        align-items: center;
    }

    .nav-user-compact,
    .nav-user-compact:focus,
    .nav-user-compact:hover,
    .nav-user-compact:active,
    .nav-user-compact.show {
        color: #000 !important;
        background-color: transparent !important;
        box-shadow: none !important;
    }

    /* Clinic dropdown same size as Add Patient */
    .clinic-badge-compact {
        font-size: 0.8rem !important;
        padding: 6px 12px !important;
        line-height: 1 !important;
        height: 34px !important;
        display: flex;
        align-items: center;
        border-radius: 6px !important;
        white-space: nowrap;
    }

    /* Smooth dropdown animation */
    .dropdown-animated {
        display: block !important;
        opacity: 0;
        transform: translateY(-6px) scale(0.95);
        transition: opacity 0.18s ease, transform 0.18s ease;
        pointer-events: none;
    }

    .dropdown-animated.show {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Navbar nav items spacing */
    .navbar-nav {
        margin-right: 0;
        margin-left: auto;
    }

    /* ========== RESPONSIVE BREAKPOINTS ========== */

    /* Large laptops (1200px - 1399px) */
    @media (max-width: 1399px) {
        .echo-movebar {
            margin-left: 70px;
        }

        .sidebar-wrapper.expanded ~ * .echo-movebar,
        body:has(.sidebar-wrapper.expanded) .echo-movebar {
            margin-left: 220px;
        }

        .btn-add-patient,
        .clinic-badge-compact {
            font-size: 0.75rem !important;
            padding: 5px 10px !important;
            height: 32px !important;
        }

        .nav-user-compact {
            font-size: 0.95rem !important;
            padding: 5px 10px !important;
            height: 42px;
        }

        .echo-movebar .nav-link {
            font-size: 1.1rem !important;
        }
    }

    /* Medium laptops and tablets (992px - 1199px) */
    @media (max-width: 1199px) {
        .echo-movebar {
            margin-left: 70px;
        }

        .sidebar-wrapper.expanded ~ * .echo-movebar,
        body:has(.sidebar-wrapper.expanded) .echo-movebar {
            margin-left: 200px;
        }

        .btn-add-patient,
        .clinic-badge-compact {
            font-size: 0.7rem !important;
            padding: 5px 8px !important;
            height: 30px !important;
        }

        .btn-add-patient i,
        .clinic-badge-compact i {
            font-size: 0.8rem;
        }

        .nav-user-compact {
            font-size: 0.9rem !important;
            padding: 5px 8px !important;
            height: 40px;
        }

        .echo-movebar .nav-link {
            font-size: 1rem !important;
        }

        .clinic-badge-compact span {
            display: none;
        }

        .clinic-badge-compact::after {
            content: none !important;
        }
    }

    /* Small laptops and tablets (768px - 991px) */
    @media (max-width: 991px) {
        .echo-movebar {
            margin-left: 65px;
        }

        .sidebar-wrapper.expanded ~ * .echo-movebar,
        body:has(.sidebar-wrapper.expanded) .echo-movebar {
            margin-left: 190px;
        }

        .navbar-nav {
            margin-left: 0;
            padding-top: 1rem;
        }

        .navbar-collapse {
            background-color: #fff;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .btn-add-patient,
        .clinic-badge-compact {
            width: 100%;
            justify-content: center;
        }

        .clinic-badge-compact span {
            display: inline;
        }

        .echo-movebar .nav-link {
            font-size: 1.2rem !important;
        }
    }

    /* Mobile devices (less than 768px) */
    @media (max-width: 767px) {
        /* On mobile, sidebar is hidden by default */
        .echo-movebar {
            margin-left: 0 !important;
        }

        /* When sidebar is open on mobile */
        .sidebar-wrapper.mobile-open ~ * .echo-movebar,
        body:has(.sidebar-wrapper.mobile-open) .echo-movebar {
            margin-left: 0 !important;
        }

        body {
            padding-top: 60px;
        }

        .navbar-fixed {
            padding-left: 60px; /* Space for hamburger menu */
        }

        .echo-movebar .nav-link {
            font-size: 1.1rem !important;
        }

        .btn-add-patient {
            font-size: 0.8rem !important;
            height: 36px !important;
        }

        .clinic-badge-compact {
            font-size: 0.75rem !important;
            height: 34px !important;
        }

        .nav-user-compact {
            font-size: 0.9rem !important;
            height: 38px;
            justify-content: flex-start;
        }

        .dropdown-menu {
            width: 100%;
            margin-top: 0.5rem !important;
        }

        /* Stack clinic and user info vertically */
        .navbar-nav .nav-item {
            width: 100%;
        }

        .navbar-nav .nav-item.dropdown {
            margin-bottom: 0.5rem;
        }

        .navbar-collapse {
            max-height: 80vh;
            overflow-y: auto;
        }
    }

    /* Extra small devices (less than 576px) */
    @media (max-width: 575px) {
        .navbar-fixed {
            padding-left: 50px;
        }

        .echo-movebar .nav-link {
            font-size: 1rem !important;
        }

        .btn-add-patient {
            font-size: 0.75rem !important;
            padding: 5px 8px !important;
            height: 34px !important;
        }

        .btn-add-patient i {
            display: none; /* Hide icon on very small screens */
        }

        .clinic-badge-compact {
            font-size: 0.7rem !important;
            padding: 5px 8px !important;
            height: 32px !important;
        }

        .clinic-badge-compact i {
            font-size: 0.75rem;
            margin-right: 4px !important;
        }

        .nav-user-compact {
            font-size: 0.85rem !important;
            padding: 5px 8px !important;
        }

        .dropdown-item {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }

        .dropdown-item i {
            font-size: 0.9rem;
        }
    }

    /* Landscape mode on mobile/tablet */
    @media (max-width: 991px) and (orientation: landscape) {
        .echo-movebar {
            margin-left: 60px;
        }

        .sidebar-wrapper.expanded ~ * .echo-movebar,
        body:has(.sidebar-wrapper.expanded) .echo-movebar {
            margin-left: 180px;
        }

        .navbar-collapse {
            max-height: 60vh;
        }

        .echo-movebar .nav-link {
            font-size: 0.95rem !important;
        }
    }

    /* Hide echo-movebar text on very small screens if needed */
    @media (max-width: 480px) {
        .echo-movebar .nav-link {
            font-size: 0.9rem !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 180px;
        }
    }

    /* Tablet portrait specific adjustments */
    @media (min-width: 768px) and (max-width: 991px) and (orientation: portrait) {
        .echo-movebar {
            margin-left: 65px;
        }

        .sidebar-wrapper.expanded ~ * .echo-movebar {
            margin-left: 190px;
        }

        .navbar-nav {
            flex-direction: row;
            flex-wrap: wrap;
        }

        .nav-item {
            margin-bottom: 0;
            margin-right: 1rem;
        }
    }
</style>

@php
    $navItems = [
        'admin.dashboard' => 'Dashboard',
        'staff.dashboard' => 'Dashboard',
        'settings' => 'Settings',
        'clinics' => 'Clinics',
        'associates' => 'Associates',
        'teeth' => 'Teeth',
        'medicines' => 'Medicines',
        'services' => 'Services',
        'waitlist' => 'Waitlist',
        'patients' => 'Patients',
        'specific-patient' => 'Patient Details',
        'appointments' => 'Calendar',
        'reports' => 'Reports',
        'tools' => 'Tools',
    ];

    $currentClinic = null;
    if (session('clinic_id')) {
        $currentClinic = \App\Models\Clinic::find(session('clinic_id'));
    }

    $activeRole = session('active_role', auth()->user()?->role);
@endphp

<nav class="navbar navbar-left navbar-expand-lg navbar-light border-3 border-primary text-black navbar-fixed">
    <div class="container-fluid d-flex align-items-center">
        <div class="d-flex align-items-center echo-movebar">
            @foreach ($navItems as $route => $label)
                @if (Route::is($route))
                    <a class="nav-link fs-5">{{ $label }}</a>
                    @break
                @endif
            @endforeach
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">

                {{-- IF ADMIN --}}
                @if ($activeRole === 'admin')
                    <li class="nav-item dropdown d-flex align-items-center me-3">
                        <a class="badge rounded-pill bg-light text-dark border d-flex align-items-center shadow-sm text-decoration-none dropdown-toggle me-1 clinic-badge-compact"
                           href="#" id="clinicDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-hospital me-2 text-primary"></i>
                            <span class="fw-semibold">Clinic:</span>&nbsp;{{ $currentClinic->name ?? 'No Selected' }}
                        </a>

                        <ul class="dropdown-menu dropdown-animated shadow-sm" aria-labelledby="clinicDropdown">
                            @foreach ($clinics as $clinic)
                                <li>
                                    <form action="{{ route('process-select-clinic') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="clinic_id" value="{{ $clinic->clinic_id }}">
                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center">
                                            {{ $clinic->name }}
                                            @if ($currentClinic && $clinic->clinic_id === $currentClinic->clinic_id)
                                                <i class="bi bi-check2 text-success"></i>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                {{-- IF STAFF --}}
                @else
                    <li class="nav-item d-flex align-items-center me-3">
                        <a class="badge rounded-pill bg-light text-dark border d-flex align-items-center shadow-sm text-decoration-none me-1 pe-none clinic-badge-compact">
                            <i class="bi bi-hospital me-2 text-primary"></i>
                            <span class="fw-semibold">Clinic:</span>&nbsp;{{ $currentClinic->name ?? 'No Selected' }}
                        </a>

                        <a href="#" class="btn btn-primary btn-add-patient"
                           data-bs-target="#add-patient-modal" data-bs-toggle="modal">
                            <i class="bi bi-person-plus-fill me-1"></i>
                            Add Patient
                        </a>
                    </li>
                @endif

                {{-- USER DROPDOWN --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-user-compact"
                       href="#" id="userDropdown"
                       role="button" data-bs-toggle="dropdown">
                        {{ auth()->user()->last_name }}, {{ auth()->user()->first_name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-animated" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('settings') }}">
                            <i class="bi bi-person-circle"></i>
                                Profile</a></li>

                        @if (auth()->user()->role === 'admin' && auth()->user()->can_act_as_staff)
                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="{{ route('process-switch-role') }}">
                                    @csrf
                                    <button type="submit" name="role" value="admin"
                                        class="dropdown-item {{ session('active_role') === 'admin' ? 'active fw-bold' : '' }}">
                                        <i class="bi bi-person-gear"></i>
                                        Act as Admin
                                    </button>
                                </form>
                            </li>

                            <li>
                                <form method="POST" action="{{ route('process-switch-role') }}">
                                    @csrf
                                    <button type="submit" name="role" value="staff"
                                        class="dropdown-item {{ session('active_role') === 'staff' ? 'active fw-bold' : '' }}">
                                        <i class="bi bi-people"></i>
                                        Act as Staff
                                    </button>
                                </form>
                            </li>
                        @endif

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <form method="POST" action="{{ route('process-logout') }}">
                                @csrf
                                
                                <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right"></i>
                                Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const navbar = document.querySelector('.navbar-fixed');
        const echoMovebar = document.querySelector('.echo-movebar');
        const sidebar = document.querySelector('.sidebar-wrapper');

        // Navbar shrink on scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('shrink');
            } else {
                navbar.classList.remove('shrink');
            }
        });

        // Check if mobile view
        function isMobile() {
            return window.innerWidth <= 767;
        }

        // Update echo-movebar margin based on sidebar state
        function updateEchoMovebarMargin() {
            if (!echoMovebar || !sidebar) return;

            if (isMobile()) {
                // On mobile, no margin adjustment needed
                echoMovebar.style.marginLeft = '0';
            } else {
                // Desktop: let CSS handle it via classes
                echoMovebar.style.marginLeft = '';
            }
        }

        // Listen for sidebar expansion (desktop hover)
        if (sidebar && !isMobile()) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        updateEchoMovebarMargin();
                    }
                });
            });

            observer.observe(sidebar, { attributes: true });
        }

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                updateEchoMovebarMargin();
            }, 250);
        });

        // Initial setup
        updateEchoMovebarMargin();

        /* ⭐ Smooth dropdown open/close for User + Clinic */
        const dropdowns = document.querySelectorAll('.dropdown');

        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector('.dropdown-menu');
            if (!menu) return;

            dropdown.addEventListener('hide.bs.dropdown', function (e) {
                e.preventDefault();
                menu.classList.remove('show');
                setTimeout(() => {
                    const instance = bootstrap.Dropdown.getInstance(dropdown.querySelector('[data-bs-toggle="dropdown"]'));
                    if (instance) {
                        instance.hide();
                    }
                }, 180); // matches CSS transition
            });
        });

        // Close mobile navbar when clicking on nav items
        if (isMobile()) {
            document.querySelectorAll('.navbar-collapse .nav-link, .navbar-collapse .dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                });
            });
        }
    });
</script>