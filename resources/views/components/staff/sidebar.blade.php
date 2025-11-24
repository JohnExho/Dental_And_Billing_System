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
    transition: width 0.3s ease;
    z-index: 100;
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
}

.sidebar-wrapper.expanded .sidebar-brand h3,
.sidebar-wrapper.expanded .sidebar-brand p {
    display: block;
}

/* Menu */
.sidebar ul {
    padding: 0;
    margin: 0;
    list-style: none;
}

/* Each Nav Item */
.nav-item a {
    display: flex;
    align-items: center;

    /* === ADDED: centers icon when collapsed === */
    justify-content: center;
    
    width: 100%;
    padding: 12px 20px;
    color: #d3d3d3;
    text-decoration: none;
    font-size: 16px;
    transition: all 0.3s ease;
}

/* When expanded → align text/icons normally */
.sidebar-wrapper.expanded .nav-item a {
    justify-content: flex-start; /* ADDED */
}

/* Icons */
.nav-item a i {
    font-size: 22px;
    min-width: 50px; /* ORIGINAL VALUE (keeps icons normal) */
    text-align: center;
}

/* Text hidden by default */
.nav-text {
    display: none;
}

/* Show text when expanded */
.sidebar-wrapper.expanded .nav-text {
    display: inline-block;
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

/* Optional toggle button */
#sidebarToggle {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
}
</style>


<!-- ======================= SIDEBAR HTML ========================= -->

<aside class="sidebar-wrapper">
    <nav class="sidebar">

        <div class="sidebar-brand">
            <img src="{{ asset('public/images/dayao.jpg') }}" alt="Logo" width="50" height="50">
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

<!-- ======================= DEBUG SCRIPT ========================= -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    console.log("=== SIDEBAR DEBUG ===");
    
    const sidebar = document.querySelector(".sidebar-wrapper");
    console.log("Sidebar element:", sidebar);
    
    if (!sidebar) {
        console.error("SIDEBAR NOT FOUND!");
        return;
    }
    
    console.log("Current classes:", sidebar.classList.toString());
    
    sidebar.addEventListener("mouseenter", () => {
        console.log("Mouse ENTER");
        sidebar.classList.add("expanded");
        console.log("Classes after add:", sidebar.classList.toString());
    });

    sidebar.addEventListener("mouseleave", () => {
        console.log("Mouse LEAVE");
        sidebar.classList.remove("expanded");
    });
    
    console.log("Event listeners attached!");
});
</script>

<!-- ======================= EXPAND ON HOVER SCRIPT ========================= -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".sidebar-wrapper");

    sidebar.addEventListener("mouseenter", () => {
        sidebar.classList.add("expanded");
    });

    sidebar.addEventListener("mouseleave", () => {
        sidebar.classList.remove("expanded");
    });
});
</script>
