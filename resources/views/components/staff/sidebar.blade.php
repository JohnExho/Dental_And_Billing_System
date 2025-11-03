<style>
/* Sidebar Wrapper */
.sidebar-wrapper {
    width: 80px;
    height: 100vh;
    background-color: #1e3765;
    color: #fff;
    position: fixed;
    top: 0;
    left: 0;
    transition: width 0.3s ease;
    overflow: hidden;
    z-index: 100;
}

/* Sidebar expand on hover or via script */
.sidebar-wrapper.expanded {
    width: 250px;
}

/* Sidebar inner layout */
.sidebar {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
}

/* Brand section */
.sidebar-brand {
    text-align: center;
    margin-bottom: 40px;
    transition: all 0.3s ease;
}

.sidebar-brand img {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    transition: all 0.3s ease;
}

/* Grow logo when expanded */
.sidebar-wrapper.expanded .sidebar-brand img {
    width: 80px;
    height: 80px;
}

/* Brand text hidden by default */
.sidebar-brand h3,
.sidebar-brand p {
    display: none;
}

/* Show brand text only when expanded */
.sidebar-wrapper.expanded .sidebar-brand h3,
.sidebar-wrapper.expanded .sidebar-brand p {
    display: block;
}

/* Navigation */
.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
    width: 100%;
}

/* Nav item */
.nav-item {
    width: 100%;
}

/* Nav link style */
.nav-item a {
    display: flex;
    align-items: center;
    color: #d3d3d3;
    text-decoration: none;
    padding: 12px 20px;
    transition: all 0.3s ease;
    white-space: nowrap;
    font-size: 16px;
}

/* Always show icon */
.nav-item a i {
    font-size: 22px;
    min-width: 50px;
    text-align: center;
}

/* Text hidden by default */
.nav-text {
    display: none;
}

/* Reveal only when expanded */
.sidebar-wrapper.expanded .nav-text {
    display: inline-block;
}

/* Hover effect */
.nav-item a:hover {
    background-color: #314d7a;
    color: #fff;
}

/* Active link */
.nav-item.active a {
    background-color: #314d7a;
    border-right: 4px solid #ff6b81;
    color: #fff;
}

/* Toggle button */
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


<aside class="sidebar-wrapper">
    <button id="sidebarToggle" aria-label="Toggle sidebar" title="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <nav class="sidebar">
        <div class="sidebar-brand">
            <img src="https://placehold.co/400x400?text=placeholder" alt="Logo" class="brand-logo">
            <h3>Dayao</h3>
            <p>Dental Home</p>
        </div>

        <ul class="nav-menu">
            <li class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <a href="{{ route('staff.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if (!session('clinic_id'))
            @else
                <li class="nav-item mb-1">
                    <a class="text-decoration-none fw-bold fs-4
                    {{ request()->routeIs('patients') ? 'active text-primary' : 'text-dark' }}"
                        href="{{ route('patients') }}" style="{{ request()->routeIs('patients') }}">
                        <span class="nav-text">Patients</span>
                        <i class="bi bi-people-fill"></i>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="text-decoration-none fw-bold fs-4
                    {{ request()->routeIs('waitlist') ? 'active text-primary' : 'text-dark' }}"
                        href="{{ route('waitlist') }}" style="{{ request()->routeIs('waitlist') }}">
                        <span class="nav-text">Waitlist</span>
                        <i class="bi bi-list-check"></i>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="text-decoration-none fw-bold fs-4
                    {{ request()->routeIs('appointments') ? 'active text-primary' : 'text-dark' }}"
                        href="{{ route('appointments') }}" style="{{ request()->routeIs('appointments') }}">
                        <span class="nav-text">Appointments</span>
                        <i class="bi bi-calendar-check-fill"></i>
                    </a>
                </li>
            @endif

        </ul>
    </nav>

   <script>
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector('.sidebar-wrapper');
    const navTexts = document.querySelectorAll('.nav-text');
    const brandTexts = document.querySelectorAll('.sidebar-brand h3, .sidebar-brand p');

    // Hide text initially
    navTexts.forEach(text => text.style.display = "none");
    brandTexts.forEach(text => text.style.display = "none");

    // Expand when hovered
    sidebar.addEventListener("mouseenter", () => {
        sidebar.classList.add("expanded");
        navTexts.forEach(text => text.style.display = "inline-block");
        brandTexts.forEach(text => text.style.display = "block");
    });

    // Collapse when mouse leaves
    sidebar.addEventListener("mouseleave", () => {
        sidebar.classList.remove("expanded");
        navTexts.forEach(text => text.style.display = "none");
        brandTexts.forEach(text => text.style.display = "none");
    });
});
</script>

</aside>

