<style>
    .navbar-fixed {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1030;
        /* Bootstrap default */
    }

    .navbar-fixed {
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


    /* space for sidebar (matches sidebar width when expanded) */
    .echo-movebar {
        transition: margin-left .28s ease;
    }

    /* when sidebar is compacted reduce the left margin */
    body.sidebar-collapsed .echo-movebar {
        margin-left: -200px;
    }

    body {
        padding-top: 56px;
        /* Adjust if your navbar is taller/shorter */
    }
</style>

@php
    $navItems = [
        'admin.dashboard' => 'Dashboard',
        'staff.dashboard' => 'Dashboard',
        'settings' => 'Settings',
        'clinics' => 'Clinics',
        'associates' => 'Associates',
        'laboratories' => 'Laboratories',
        'teeth' => 'Teeth',
        'medicines' => 'Medicines',
        'services' => 'Services',
    ];

    $currentClinic = null;
    if (session('clinic_id')) {
        $currentClinic = \App\Models\Clinic::find(session('clinic_id'));
    }
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

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav ms-auto" style="margin-right: 0; margin-left: 250px;">

                @if ($currentClinic !== null)
                    <li class="nav-item d-flex align-items-center me-3">
                        <a href="{{ route('clinics') }}"
                            class="badge rounded-pill bg-light text-dark border d-flex align-items-center px-3 py-2 shadow-sm text-decoration-none">
                            <i class="bi bi-hospital me-2 text-primary"></i>
                            <span class="fw-semibold">Clinic:</span>&nbsp;{{ $currentClinic->name }}
                        </a>
                    </li>
                @endif


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fs-4" href="#" id="userDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->last_name }}, {{ Auth::user()->first_name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('settings') }}">Profile</a>
                        </li>

                        {{-- Role switcher (only for admins who can act as staff) --}}
                        @if (auth()->user()->role === 'admin' && auth()->user()->can_act_as_staff)
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('process-switch-role') }}">
                                    @csrf
                                    <button type="submit" name="role" value="admin"
                                        class="dropdown-item {{ session('active_role') === 'admin' ? 'active fw-bold' : '' }}">
                                        Act as Admin
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('process-switch-role') }}">
                                    @csrf
                                    <button type="submit" name="role" value="staff"
                                        class="dropdown-item {{ session('active_role') === 'staff' ? 'active fw-bold' : '' }}">
                                        Act as Staff
                                    </button>
                                </form>
                            </li>
                        @endif

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('process-logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
</nav>

<script>
    // Wait for the DOM to load
    document.addEventListener("DOMContentLoaded", function() {
        const navbar = document.querySelector('.navbar-fixed');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) { // adjust scroll trigger
                navbar.classList.add('shrink');
            } else {
                navbar.classList.remove('shrink');
            }
        });
    });
</script>
