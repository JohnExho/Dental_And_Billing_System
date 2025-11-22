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
</style>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <!-- Left Card (Recent Activities) -->
        <div class="col-md-12 mb-4">
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

                    <div id="recentActivitiesWrapper" class="position-relative" onclick="window.location='{{ route('tools') }}'">
                        <!-- Loader -->
                        <div id="recentActivitiesLoading" class="d-flex justify-content-center align-items-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <span class="ms-2 text-muted">Loading activities...</span>
                        </div>

                        <!-- Content (hidden until ready) -->
                        <div id="recentActivitiesContent" class="d-none">
                            @include('auth.partials.admin-dashboard-partial', ['logs' => $logs])
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Card (Patients with Balance) -->
        <div class="col-md-7 mt-4">
            <div class="card border-1 border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title text-danger mb-0">Patients with Balance</h5>
                        <span class="badge bg-danger-subtle text-danger border border-danger">
                            {{ $unpaidBills->count() }}
                        </span>
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
