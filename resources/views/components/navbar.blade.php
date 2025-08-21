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
    .margin-start{
        margin-left: 140px;
        transition: margin-left .28s ease;
    }

    /* when sidebar is compacted reduce the left margin */
    body.sidebar-collapsed .margin-start{
        margin-left: -5px;
    }

    body {
        padding-top: 56px;
        /* Adjust if your navbar is taller/shorter */
    }
</style>
        <nav class="navbar navbar-left navbar-expand-lg navbar-light bg-primary navbar-fixed">
        <div class="container d-flex align-items-center">
            <div class="d-flex align-items-center margin-start">
                @if (Route::is('dashboard'))
                <a class="nav-link fs-5 text-light">Dashboard</a>
                @elseif (Route::is('settings'))
                  <a class="nav-link fs-5 text-light">Settings</a>
                @endif
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav ms-auto" style="margin-right: 0; margin-left: 250px;">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light fs-4" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('settings') }}">Profile</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('process-logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link fs-4 text-light" href="{{ url('/') }}">Home</a>
                        </li>
                    @endauth
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