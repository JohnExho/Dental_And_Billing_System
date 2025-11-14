<style>
    /* ==== SIDEBAR BASE ==== */
    .sidebar-wrapper {
        position: fixed;
        top: 0;
        left: 0; /* ✅ stays at very left */
        height: 100vh;
        width: 80px; /* collapsed width */
        background-color: #1f3556;
        border-right: 1px solid #dee2e6;
        overflow: hidden;
        transition: width 0.3s ease;
        z-index: 1050; /* ✅ make sure it overlays on top of content */
        pointer-events: auto;
    }

    /* ✅ expands on hover */
    .sidebar-wrapper.expanded {
        width: 240px;
    }

    .sidebar {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        padding-top: 1rem;
        background-color: #1f3556;
    }

    .sidebar-brand {
        text-align: center;
        color: white;
        margin-bottom: 2rem;
        transition: opacity 0.3s;
    }

    .sidebar-brand img {
        width: 60px;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    .sidebar-wrapper:not(.expanded) .sidebar-brand h3,
    .sidebar-wrapper:not(.expanded) .sidebar-brand p {
        display: none;
    }

    .sidebar .nav {
        width: 100%;
        padding: 0;
        margin: 0; /* ✅ prevent unwanted spacing */
    }

    .sidebar .nav-item {
        list-style: none;
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .sidebar .nav a {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #dcdcdc;
        text-decoration: none;
        font-weight: 600;
        padding: 12px 20px;
        transition: background-color 0.3s, color 0.3s;
        position: relative;
        white-space: nowrap;
    }

    .sidebar .nav a:hover {
        background-color: #304b78;
    }

    /* Active link styling */
    .sidebar .nav a.active {
        background-color: #3c537d;
    }

    .sidebar .nav a.active::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 5px;
        background-color: #ff5c5c;
        border-radius: 10px 0 0 10px;
    }

    /* Icons */
    .sidebar .nav i {
        font-size: 1.5rem;
        min-width: 30px;
        text-align: center;
        flex-shrink: 0;
    }

    /* Hide labels when collapsed */
    .sidebar-wrapper:not(.expanded) .nav-text {
        display: none;
    }

    .sidebar-wrapper.expanded .nav-text {
        display: inline;
    }

    /* ✅ Smooth label fade animation */
    .nav-text {
        transition: opacity 0.3s ease;
    }

    .sidebar-wrapper:not(.expanded) .nav-text {
        opacity: 0;
    }

    .sidebar-wrapper.expanded .nav-text {
        opacity: 1;
    }
</style>

<aside class="sidebar-wrapper" id="sidebar">
    <nav class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/dayao.jpg') }}" alt="Logo" width="50" height="50">
            <h3 class="fw-bold">Dayao Dental</h3>
            <p class="text-light">Home</p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" 
                   class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clinics') }}" 
                   class="{{ request()->routeIs('clinics') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span class="nav-text">Clinics</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('associates') }}" 
                   class="{{ request()->routeIs('associates') ? 'active' : '' }}">
                    <i class="bi bi-person-bounding-box"></i>
                    <span class="nav-text">Associates</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('staffs') }}" 
                   class="{{ request()->routeIs('staffs') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span class="nav-text">Staff</span>
                </a>
            </li>
            <li class="nav-item">
    <a href="{{ route('teeth') }}" 
       class="{{ request()->routeIs('teeth') ? 'active' : '' }}">
        <i class="fa-solid fa-tooth"></i>
        <span class="nav-text">Teeth</span>
    </a>
</li>

            <li class="nav-item">
                <a href="{{ route('medicines') }}" 
                   class="{{ request()->routeIs('medicines') ? 'active' : '' }}">
                    <i class="fa-solid fa-tablets"></i>
                    <span class="nav-text">Medicine</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('services') }}" 
                   class="{{ request()->routeIs('services') ? 'active' : '' }}">
                    <i class="fa-solid fa-stethoscope"></i>
                    <span class="nav-text">Service</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tools') }}" 
                   class="{{ request()->routeIs('tools') ? 'active' : '' }}">
                    <i class="bi bi-wrench-adjustable"></i>
                    <span class="nav-text">Tools</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#">
                    <i class="bi bi-file-earmark-text"></i>
                    <span class="nav-text">Reports</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<script> const sidebar = document.getElementById('sidebar'); sidebar.addEventListener('mouseenter', () => { sidebar.classList.add('expanded'); }); sidebar.addEventListener('mouseleave', () => { sidebar.classList.remove('expanded'); }); </script>