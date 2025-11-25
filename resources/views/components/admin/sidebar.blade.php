<style>
    /* ==== SIDEBAR BASE ==== */
    .sidebar-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 80px;
        background-color: #1f3556;
        border-right: 1px solid #dee2e6;
        overflow: hidden;
        transition: width 0.3s ease, transform 0.3s ease;
        z-index: 1050;
        pointer-events: auto;
    }

    /* ✅ expands on hover */
    .sidebar-wrapper.expanded {
        width: 240px !important;
    }

    .sidebar {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        padding-top: 1rem;
        background-color: #1f3556;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Custom scrollbar */
    .sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: #1f3556;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #304b78;
        border-radius: 10px;
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
        margin: 0;
    }

    .sidebar .nav-item {
        list-style: none;
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .sidebar .nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #dcdcdc;
        text-decoration: none;
        font-weight: 600;
        padding: 12px 20px;
        transition: background-color 0.3s, color 0.3s;
        position: relative;
        white-space: nowrap;
        justify-content: flex-start;
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
        width: 0;
        overflow: hidden;
    }

    .sidebar-wrapper:not(.expanded) .nav-text {
        opacity: 0;
        width: 0;
    }

    .sidebar-wrapper.expanded .nav-text {
        opacity: 1;
        width: auto;
    }

    /* Mobile toggle button */
    .sidebar-toggle {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1051;
        background-color: #1f3556;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .sidebar-toggle i {
        font-size: 1.2rem;
    }

    /* Overlay for mobile */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1049;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.active {
        display: block;
        opacity: 1;
    }

    /* ========== RESPONSIVE BREAKPOINTS ========== */

    /* Large laptops (1200px - 1399px) */
    @media (max-width: 1399px) {
        .sidebar-wrapper.expanded {
            width: 220px !important;
        }

        .sidebar .nav a {
            padding: 10px 15px;
            font-size: 0.95rem;
        }

        .sidebar .nav i {
            font-size: 1.4rem;
        }
    }

    /* Medium laptops and tablets (992px - 1199px) */
    @media (max-width: 1199px) {
        .sidebar-wrapper {
            width: 70px;
        }

        .sidebar-wrapper.expanded {
            width: 200px !important;
        }

        .sidebar-brand img {
            width: 50px;
        }

        .sidebar .nav a {
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .sidebar .nav i {
            font-size: 1.3rem;
            min-width: 25px;
        }

        .sidebar .nav-item {
            margin-bottom: 0.3rem;
        }
    }

    /* Small laptops and tablets (768px - 991px) */
    @media (max-width: 991px) {
        .sidebar-wrapper {
            width: 60px;
        }

        .sidebar-wrapper.expanded {
            width: 180px !important;
        }

        .sidebar {
            padding-top: 0.75rem;
        }

        .sidebar-brand {
            margin-bottom: 1.5rem;
        }

        .sidebar-brand img {
            width: 45px;
        }

        .sidebar-brand h3 {
            font-size: 1.1rem;
        }

        .sidebar-brand p {
            font-size: 0.85rem;
        }

        .sidebar .nav a {
            padding: 8px 12px;
            font-size: 0.85rem;
            gap: 0.75rem;
        }

        .sidebar .nav i {
            font-size: 1.2rem;
            min-width: 34px;
            width: 34px;
        }
    }

    /* Mobile devices (less than 768px) */
    @media (max-width: 767px) {
        /* Hide sidebar by default on mobile */
        .sidebar-wrapper {
            transform: translateX(-100%);
            width: 240px;
        }

        /* Show toggle button */
        .sidebar-toggle {
            display: block;
        }

        /* When mobile menu is open */
        .sidebar-wrapper.mobile-open {
            transform: translateX(0);
            width: 240px !important;
        }

        /* Disable hover expansion on mobile */
        .sidebar-wrapper.mobile-open.expanded {
            width: 240px !important;
        }

        .sidebar {
            padding-top: 4rem;
        }

        .sidebar-brand img {
            width: 60px;
        }

        .sidebar-brand h3,
        .sidebar-brand p {
            display: block !important;
        }

        .nav-text {
            display: inline !important;
            opacity: 1 !important;
        }

        .sidebar .nav a {
            padding: 12px 20px;
            font-size: 1rem;
            justify-content: flex-start;
        }

        .sidebar .nav i {
            font-size: 1.3rem;
            min-width: 30px;
        }
    }

    /* Extra small devices (less than 576px) */
    @media (max-width: 575px) {
        .sidebar-wrapper {
            width: 220px;
        }

        .sidebar-wrapper.mobile-open {
            width: 220px !important;
        }

        .sidebar .nav a {
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .sidebar .nav i {
            font-size: 1.2rem;
            min-width: 28px;
        }

        .sidebar-brand img {
            width: 50px;
        }

        .sidebar-brand h3 {
            font-size: 1rem;
        }

        .sidebar-brand p {
            font-size: 0.8rem;
        }
    }

    /* Landscape mode on mobile */
    @media (max-width: 991px) and (orientation: landscape) {
        .sidebar-wrapper {
            width: 60px;
        }

        .sidebar-wrapper.expanded,
        .sidebar-wrapper.mobile-open {
            width: 180px !important;
        }

        .sidebar {
            padding-top: 0.5rem;
        }

        .sidebar-brand {
            margin-bottom: 1rem;
        }

        .sidebar .nav-item {
            margin-bottom: 0.2rem;
        }

        .sidebar .nav a {
            padding: 6px 10px;
        }
    }
</style>

<!-- Mobile Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar Overlay (for mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar-wrapper" id="sidebar">
    <nav class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('public/images/dayao.jpg') }}" alt="Logo" width="50" height="50">
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
                <a href="{{ route('clinics') }}" class="{{ request()->routeIs('clinics') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span class="nav-text">Clinics</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('associates') }}" class="{{ request()->routeIs('associates') ? 'active' : '' }}">
                    <i class="bi bi-person-bounding-box"></i>
                    <span class="nav-text">Associates</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('staffs') }}" class="{{ request()->routeIs('staffs') ? 'active' : '' }}">
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
                <a href="{{ route('medicines') }}" class="{{ request()->routeIs('medicines') ? 'active' : '' }}">
                    <i class="fa-solid fa-tablets"></i>
                    <span class="nav-text">Medicine</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}">
                    <i class="fa-solid fa-stethoscope"></i>
                    <span class="nav-text">Service</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tools') }}" class="{{ request()->routeIs('tools') ? 'active' : '' }}">
                    <i class="bi bi-wrench-adjustable"></i>
                    <span class="nav-text">Tools</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports') }}" class="{{ request()->routeIs('reports') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span class="nav-text">Reports</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<script>
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Check if mobile view
    function isMobile() {
        return window.innerWidth <= 767;
    }
    
    // Desktop hover behavior
    if (!isMobile()) {
        sidebar.addEventListener('mouseenter', () => {
            if (!isMobile()) {
                sidebar.classList.add('expanded');
            }
        });
        
        sidebar.addEventListener('mouseleave', () => {
            if (!isMobile()) {
                sidebar.classList.remove('expanded');
            }
        });
    }
    
    // Mobile toggle behavior
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('mobile-open');
        sidebarOverlay.classList.toggle('active');
    });
    
    // Close sidebar when clicking overlay
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        sidebarOverlay.classList.remove('active');
    });
    
    // Close sidebar when clicking a link on mobile
    if (isMobile()) {
        document.querySelectorAll('.sidebar .nav a').forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            });
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', () => {
        if (!isMobile()) {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
        }
    });
</script>