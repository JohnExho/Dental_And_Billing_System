@extends('layout')
@section('title', 'Dashboard | Chomply')

@section('content')

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            font-weight: 600;
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.4em 0.8em;
            border-radius: 10px;
        }

        .text-danger {
            color: #d9534f !important;
        }

        .border-danger {
            border-color: #d9534f !important;
        }

        .border-info {
            border-color: #0dcaf0 !important;
        }

        .btn-outline-primary {
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }

        .btn-outline-primary:hover {
            background: #0d6efd;
            color: #fff;
        }

        #recentActivitiesWrapper {
            cursor: pointer;
            border-radius: 10px;
            overflow: hidden;
        }

        #recentActivitiesWrapper:hover {
            background-color: #f8f9fa;
        }

        #recentActivitiesLoading {
            padding: 2rem 0;
        }

        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }

        @media (max-width: 768px) {
            .card {
                margin-bottom: 1rem;
            }
        }

        /* Responsive breakpoints */

        /* Large devices (desktops, 1200px and up) */
        @media (max-width: 1399px) {
            .container-fluid {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        /* Medium-Large devices (laptops, 992px to 1199px) */
        @media (max-width: 1199px) {

            .col-md-7,
            .col-md-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .card-title {
                font-size: 1.1rem;
            }

            .badge {
                font-size: 0.85rem;
            }
        }

        /* Medium devices (tablets, laptops, 768px to 991px) */
        @media (max-width: 991px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .col-md-12,
            .col-md-7,
            .col-md-5 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 1rem;
            }

            .card {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }

            .d-flex.justify-content-between .d-flex {
                width: 100%;
                justify-content: space-between;
            }

            #lastUpdated {
                font-size: 0.75rem;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
        }

        /* Small devices (landscape phones, 576px to 767px) */
        @media (max-width: 767px) {
            .card-title {
                font-size: 1rem;
            }

            .list-group-item {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }

            .list-group-item .badge {
                align-self: flex-start;
            }

            .card:hover,
            .dashboard-card:hover {
                transform: translateY(-2px);
            }

            #recentActivitiesLoading {
                padding: 1rem 0;
            }

            .spinner-border {
                width: 1.25rem;
                height: 1.25rem;
            }
        }

        /* Extra small devices (portrait phones, less than 576px) */
        @media (max-width: 575px) {
            .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .card {
                border-radius: 10px;
            }

            .card-body {
                padding: 0.75rem;
            }

            .card-title {
                font-size: 0.95rem;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.3em 0.6em;
            }

            .btn-outline-primary {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            .btn-outline-primary i {
                font-size: 0.8rem;
            }

            #lastUpdated {
                display: none;
                /* Hide on very small screens */
            }

            .list-group-item {
                padding: 0.75rem;
                font-size: 0.9rem;
            }

            .list-group-item strong {
                font-size: 0.9rem;
            }

            .list-group-item small {
                font-size: 0.8rem;
            }

            hr {
                margin: 0.5rem 0;
            }
        }

        /* Landscape orientation adjustments */
        @media (max-width: 991px) and (orientation: landscape) {

            .col-md-7,
            .col-md-5 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .row.g-4 {
                --bs-gutter-x: 1rem;
            }
        }

        /* Print styles */
        @media print {

            .btn,
            .bi-arrow-clockwise {
                display: none;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .card:hover {
                transform: none;
            }
        }
        .col-md-12, .col-md-7 {
    border: 3px solid green !important;
}
    </style>

  <div class="container-fluid px-4 mt-3">
    <div class="row g-4 mb-4">
        <!-- Recent Activities - Full Width -->
        <div class="col-12">
            <div class="card border-1 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 text-info">Recent Activities</h5>
                        <div class="d-flex align-items-center">
                            <small class="text-muted me-3" id="lastUpdated"></small>
                            <button id="refreshLogs" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div id="recentActivitiesWrapper" class="position-relative"
                        onclick="window.location='{{ route('tools') }}'">
                        <div id="recentActivitiesLoading" class="d-flex justify-content-center align-items-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <span class="ms-2 text-muted">Loading activities...</span>
                        </div>
                        <div id="recentActivitiesContent" class="d-none">
                            @include('auth.partials.admin-dashboard-partial', ['logs' => $logs])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row for Patients with Balance -->
    <div class="row g-4">
        <div class="col-md-7">
            <div class="card dashboard-card shadow-sm border border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title text-danger mb-0">Patients with Balance</h5>
                    </div>
                    @include('auth.partials.dashboard-bill-partial')
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/dayjs/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs/plugin/duration.js"></script>
    <script>
        dayjs.extend(window.dayjs_plugin_duration);

        function formatPreciseDiff(start, end) {
            const diffMs = end.diff(start);
            const dur = dayjs.duration(diffMs);

            if (dur.asSeconds() < 60) return `${Math.floor(dur.asSeconds())}s ago`;
            if (dur.asMinutes() < 60) return `${Math.floor(dur.asMinutes())}m ${dur.seconds()}s ago`;
            if (dur.asHours() < 24) return `${Math.floor(dur.asHours())}h ${dur.minutes()}m ago`;
            if (dur.asDays() < 30) return `${Math.floor(dur.asDays())}d ${dur.hours()}h ago`;
            if (dur.asMonths() < 12) return `${Math.floor(dur.asMonths())}mo ${dur.days()}d ago`;

            return `${Math.floor(dur.asYears())}y ${dur.months()}mo ago`;
        }

        function updateTimes() {
            const now = dayjs();
            document.querySelectorAll('[data-time]').forEach(el => {
                const time = dayjs(el.dataset.time);
                el.textContent = formatPreciseDiff(time, now);
            });
            const lastUpdated = document.getElementById('lastUpdated');
            if (lastUpdated) {
                lastUpdated.textContent = "Last updated: " + now.format("MMM D, YYYY h:mm A");
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                document.getElementById('recentActivitiesLoading').classList.add('d-none');
                document.getElementById('recentActivitiesContent').classList.remove('d-none');
                updateTimes();
            }, 600);
        });

        setInterval(updateTimes, 300000);

        document.addEventListener("click", (e) => {
            if (e.target.closest('#refreshLogs')) {
                updateTimes();
            }
        });
    </script>

@endsection
