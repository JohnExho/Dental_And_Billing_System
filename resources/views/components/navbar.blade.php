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

    /* ⭐ NEW: Navbar content moves with sidebar */
    .echo-movebar {
        transition: margin-left 0.3s ease;
        margin-left: -235px; /* Default collapsed sidebar width */
    }

    /* When sidebar is expanded (hover) */
    .sidebar-wrapper.expanded ~ * .echo-movebar,
    body:has(.sidebar-wrapper.expanded) .echo-movebar {
        margin-left: -200px; /* Expanded sidebar width */
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
    <div class="container d-flex align-items-center">
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
            <ul class="navbar-nav ms-auto" style="margin-right: 0; margin-left: 250px;">

                {{-- IF ADMIN --}}
                @if ($activeRole === 'admin')
                    <li class="nav-item dropdown d-flex align-items-center me-3">
                        <a class="badge rounded-pill bg-light text-dark border d-flex align-items-center shadow-sm text-decoration-none dropdown-toggle me-1 clinic-badge-compact"
                           href="#" id="clinicDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-hospital me-2 text-primary"></i>
                            <span class="fw-semibold">Clinic:</span>&nbsp;{{ $currentClinic->name ?? 'No Selected' }}
                        </a>

                        <ul class="dropdown-menu shadow-sm" aria-labelledby="clinicDropdown">
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

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
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

        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('shrink');
            } else {
                navbar.classList.remove('shrink');
            }
        });

        /* ⭐ Smooth dropdown open/close for User + Clinic */
        const dropdowns = document.querySelectorAll('.dropdown');

        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector('.dropdown-menu');
            if (!menu) return;

            menu.classList.add('dropdown-animated');

            dropdown.addEventListener('hide.bs.dropdown', function (e) {
                e.preventDefault();
                menu.classList.remove('show');
                setTimeout(() => {
                    bootstrap.Dropdown.getOrCreateInstance(dropdown).hide();
                }, 180); // matches CSS transition
            });
        });
    });
</script>
