<style>
    .sidebar-wrapper {
        /* make the sidebar fixed under the navbar so it fills the viewport on every page */
        position: fixed;
        top: 0;
        /* matches navbar height (see navbar CSS) */
        left: 0;
        height: calc(100vh - 56px);
        width: 240px;
        background-color: #f8f9fa;
        border-right: 1px solid #dee2e6;
        padding: 1rem 0.75rem;
        transition: left 0.28s ease, width 0.28s ease;
        z-index: 1050;
        overflow-y: auto;
        /* allow scrolling if content is taller than viewport */
    }

    .sidebar .nav a {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: 0.5rem;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar a:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* small toggle located on the sidebar */
    #sidebarToggle {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 1100;
        border: none;
        background: transparent;
        color: #333;
        padding: .25rem .4rem;
    }

    /* hide text labels and brand when compacted */
    body.sidebar-collapsed .sidebar .nav-text,
    body.sidebar-collapsed .sidebar .sidebar-brand {
        display: none;
    }

    /* add top space so nav items don't overlap the toggle when brand is hidden */
    body.sidebar-collapsed .sidebar .nav {
        padding-top: 56px;
        /* adjust value (e.g. 40-64px) to match your toggle/brand height */
    }

    /* center icons in compact mode */
    body.sidebar-collapsed .sidebar .nav a {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
    }
</style>


<aside class="sidebar-wrapper h-100">

    {{-- toggle moved here --}}
    <button id="sidebarToggle" aria-label="Toggle sidebar" title="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>
    <nav class="sidebar text-center ">
        <div class="mb-4 sidebar-brand">
            <img src="https://placehold.co/400x400?text=placeholder" alt="Logo" class="mb-3 border-round rounded-3"
                style="width:80px;">
            <h3 class="fw-bold">Dayao</h3>
            <p class="text-light">Dental Home</p>
        </div>
        <ul class="nav flex-column text-start">
            <li class="nav-item mb-1">
                <a href="{{ route('staff.dashboard') }}"
                    class="text-decoration-none fw-bold fs-4 
                   {{ request()->routeIs('staff.dashboard') ? 'text-primary' : 'text-dark' }}">
                    <span class="nav-text">Dashboard</span>
                    <i class="bi bi-speedometer2 ms-1"></i>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="text-decoration-none fw-bold fs-4
                    {{ request()->routeIs('waitlist') ? 'active text-primary' : 'text-dark' }}"
                    href="{{ route('waitlist') }}"
                    style="{{ request()->routeIs('waitlist') }}">
                    <span class="nav-text">Waitlist</span>
                    <i class="bi bi-list-check"></i>
                </a>
            </li>
        </ul>
    </nav>
</aside>
