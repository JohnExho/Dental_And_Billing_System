<style>
/* ==== SIDEBAR BASE ==== */
.sidebar-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 80px;
    background-color: #1e3765;
    color: #fff;
    overflow: hidden;
    transition: width 0.3s ease, transform 0.3s ease;
    z-index: 1050;
}

/* Expanded */
.sidebar-wrapper.expanded {
    width: 250px !important;
}

/* Sidebar inner */
.sidebar {
    height: 100%;
    display: flex;
    flex-direction: column;
    padding-top: 20px;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Custom scrollbar */
.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-track {
    background: #1e3765;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #314d7a;
    border-radius: 10px;
}

/* Brand */
.sidebar-brand {
    text-align: center;
    margin-bottom: 25px;
}

.sidebar-brand img {
    width: 60px;
    border-radius: 50%;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.sidebar-wrapper.expanded .sidebar-brand img {
    width: 80px;
    height: 80px;
}

.sidebar-brand h3,
.sidebar-brand p {
    color: #fff;
    margin: 0;
    display: none;
    transition: opacity 0.3s ease;
}

.sidebar-wrapper.expanded .sidebar-brand h3,
.sidebar-wrapper.expanded .sidebar-brand p {
    display: block;
}

/* Menu */
.sidebar .nav-menu {
    padding: 0;
    margin: 0;
    list-style: none;
}

/* Each Nav Item */
.nav-item a {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    padding: 12px 20px;
    color: #d3d3d3;
    text-decoration: none;
    font-size: 16px;
    transition: all 0.3s ease;
}

/* When collapsed → center icon */
.sidebar-wrapper:not(.expanded) .nav-item a {
    justify-content: center;
}

/* Icons */
.nav-item a i {
    font-size: 22px;
    min-width: 40px;
    width: 40px;
    text-align: center;
    flex-shrink: 0;
}

/* Text hidden by default */
.nav-text {
    display: none;
    transition: opacity 0.3s ease;
    opacity: 0;
    width: 0;
    overflow: hidden;
}

/* Show text when expanded */
.sidebar-wrapper.expanded .nav-text {
    display: inline-block;
    opacity: 1;
    width: auto;
}

/* Hover */
.nav-item a:hover {
    background-color: #314d7a;
    color: #fff;
}

/* Active */
.nav-item.active a {
    background-color: #314d7a;
    border-right: 4px solid #ff6b81;
    color: #fff;
}

/* Mobile toggle button */
.sidebar-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1051;
    background-color: #1e3765;
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
        width: 230px !important;
    }

    .nav-item a {
        padding: 10px 18px;
        font-size: 15px;
    }

    .nav-item a i {
        font-size: 20px;
        min-width: 45px;
    }
}

/* Medium laptops and tablets (992px - 1199px) */
@media (max-width: 1199px) {
    .sidebar-wrapper {
        width: 70px;
    }

    .sidebar-wrapper.expanded {
        width: 210px !important;
    }

    .sidebar-brand img {
        width: 50px;
    }

    .sidebar-wrapper.expanded .sidebar-brand img {
        width: 70px;
        height: 70px;
    }

    .nav-item a {
        padding: 10px 15px;
        font-size: 14px;
    }

    .nav-item a i {
        font-size: 20px;
        min-width: 38px;
        width: 38px;
    }
}

/* Small laptops and tablets (768px - 991px) */
@media (max-width: 991px) {
    .sidebar-wrapper {
        width: 65px;
    }

    .sidebar-wrapper.expanded {
        width: 190px !important;
    }

    .sidebar {
        padding-top: 15px;
    }

    .sidebar-brand {
        margin-bottom: 20px;
    }

    .sidebar-brand img {
        width: 45px;
    }

    .sidebar-wrapper.expanded .sidebar-brand img {
        width: 65px;
        height: 65px;
    }

    .sidebar-brand h3 {
        font-size: 1.1rem;
    }

    .sidebar-brand p {
        font-size: 0.85rem;
    }

    .nav-item a {
        padding: 9px 12px;
        font-size: 14px;
    }

    .nav-item a i {
        font-size: 19px;
        min-width: 36px;
        width: 36px;
    }
}

/* Mobile devices (less than 768px) */
@media (max-width: 767px) {
    /* Hide sidebar by default on mobile */
    .sidebar-wrapper {
        transform: translateX(-100%);
        width: 250px;
    }

    /* Show toggle button */
    .sidebar-toggle {
        display: block;
    }

    /* When mobile menu is open */
    .sidebar-wrapper.mobile-open {
        transform: translateX(0);
        width: 250px !important;
    }

    .sidebar {
        padding-top: 4rem;
    }

    .sidebar-brand img {
        width: 70px !important;
        height: 70px !important;
    }

    .sidebar-brand h3,
    .sidebar-brand p {
        display: block !important;
    }

    .nav-text {
        display: inline-block !important;
        opacity: 1 !important;
    }

    .nav-item a {
        padding: 12px 20px;
        font-size: 16px;
        justify-content: flex-start !important;
    }

    .nav-item a i {
        font-size: 22px;
        min-width: 50px;
    }
}

/* Extra small devices (less than 576px) */
@media (max-width: 575px) {
    .sidebar-wrapper {
        width: 230px;
    }

    .sidebar-wrapper.mobile-open {
        width: 230px !important;
    }

    .sidebar-brand img {
        width: 60px !important;
        height: 60px !important;
    }

    .sidebar-brand h3 {
        font-size: 1rem;
    }

    .sidebar-brand p {
        font-size: 0.8rem;
    }

    .nav-item a {
        padding: 10px 15px;
        font-size: 14px;
    }

    .nav-item a i {
        font-size: 20px;
        min-width: 45px;
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
        padding-top: 10px;
    }

    .sidebar-brand {
        margin-bottom: 15px;
    }

    .sidebar-brand img {
        width: 40px !important;
    }

    .sidebar-wrapper.expanded .sidebar-brand img,
    .sidebar-wrapper.mobile-open .sidebar-brand img {
        width: 55px !important;
        height: 55px !important;
    }

    .nav-item a {
        padding: 7px 10px;
        font-size: 13px;
    }

    .nav-item a i {
        font-size: 18px;
        min-width: 34px;
        width: 34px;
    }
}
</style>

<!-- Mobile Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar Overlay (for mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ======================= SIDEBAR HTML ========================= -->
<aside class="sidebar-wrapper">
    <nav class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/dayao.jpg') }}" alt="Logo" width="50" height="50">
            <h3>Dayao</h3>
            <p>Dental Home</p>
        </div>

        <ul class="nav-menu">
            <!-- Dashboard -->
            <li class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <a href="{{ route('staff.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            @if(session('clinic_id'))

            <li class="nav-item {{ request()->routeIs('patients') ? 'active' : '' }}">
                <a href="{{ route('patients') }}">
                    <i class="bi bi-people-fill"></i>
                    <span class="nav-text">Patients</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('waitlist') ? 'active' : '' }}">
                <a href="{{ route('waitlist') }}">
                    <i class="bi bi-list-check"></i>
                    <span class="nav-text">Waitlist</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('appointments') ? 'active' : '' }}">
                <a href="{{ route('appointments') }}">
                    <i class="bi bi-calendar-check-fill"></i>
                    <span class="nav-text">Calendar</span>
                </a>
            </li>

            @endif
        </ul>
    </nav>
</aside>

<!-- ======================= RESPONSIVE SCRIPT ========================= -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".sidebar-wrapper");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebarOverlay = document.getElementById("sidebarOverlay");
    
    // Check if mobile view
    function isMobile() {
        return window.innerWidth <= 767;
    }
    
    // Desktop hover behavior
    function initDesktopBehavior() {
        sidebar.addEventListener("mouseenter", handleMouseEnter);
        sidebar.addEventListener("mouseleave", handleMouseLeave);
    }
    
    function handleMouseEnter() {
        if (!isMobile()) {
            sidebar.classList.add("expanded");
        }
    }
    
    function handleMouseLeave() {
        if (!isMobile()) {
            sidebar.classList.remove("expanded");
        }
    }
    
    // Initialize desktop hover
    if (!isMobile()) {
        initDesktopBehavior();
    }
    
    // Mobile toggle behavior
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("mobile-open");
            sidebarOverlay.classList.toggle("active");
        });
    }
    
    // Close sidebar when clicking overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", () => {
            sidebar.classList.remove("mobile-open");
            sidebarOverlay.classList.remove("active");
        });
    }
    
    // Close sidebar when clicking a link on mobile
    const navLinks = document.querySelectorAll(".sidebar .nav-item a");
    navLinks.forEach(link => {
        link.addEventListener("click", () => {
            if (isMobile()) {
                sidebar.classList.remove("mobile-open");
                sidebarOverlay.classList.remove("active");
            }
        });
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (!isMobile()) {
                sidebar.classList.remove("mobile-open");
                sidebarOverlay.classList.remove("active");
            }
        }, 250);
    });
});
</script>