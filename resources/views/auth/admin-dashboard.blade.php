@extends('layout')
@section('title', 'Dashboard | Chomply')

@section('content')

<style>
/* --- Base --- */
body {
    font-family: 'Poppins', sans-serif;
    background: #faf9f6;
    margin: 0;
    padding: 0;
}

.dashboard-container {
    width: 90%;
    margin: 50px auto;
}

/* --- Header --- */
.dashboard-header {
    text-align: center;
    margin-bottom: 40px;
}

.dashboard-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
}

.dashboard-header p {
    color: #666;
    margin-top: 8px;
}

/* --- Grid Layout --- */
.dashboard-grid {
    display: grid;
    grid-template-columns: 1.3fr 1.7fr;
    gap: 25px;
}

@media (max-width: 900px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

/* --- Cards --- */
.card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
    padding: 25px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 12px rgba(0, 0, 0, 0.25);
}

/* --- Card Headers --- */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.card-header h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
}

/* --- Patients with Balance --- */
.card-balance h2 {
    color: #c0392b;
    text-transform: uppercase;
}

.count-badge {
    background: #ffe5e5;
    color: #c0392b;
    padding: 5px 10px;
    border-radius: 10px;
    font-weight: 600;
}

/* --- Patient List --- */
.patient-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.patient-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.patient-info small {
    display: block;
    color: #888;
    font-size: 0.85rem;
}

.balance-amount {
    background: #e74c3c;
    color: white;
    border-radius: 8px;
    padding: 4px 10px;
    font-weight: 600;
}

/* --- Recent Activities --- */
.card-activities h2 {
    color: #2980b9;
}

.actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

#refreshLogs {
    background: none;
    border: 2px solid #2980b9;
    color: #2980b9;
    padding: 5px 10px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}

#refreshLogs:hover {
    background: #2980b9;
    color: white;
}

/* --- Loading Spinner --- */
.loading {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: row;
    gap: 10px;
    padding: 40px 0;
    color: #2980b9;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 3px solid #2980b9;
    border-top: 3px solid transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.hidden {
    display: none;
}

</style>

<div class="dashboard-container">

    <!-- HEADER -->
    <div class="dashboard-header">
        <h1>Welcome to the Dashboard</h1>
        <p>This is where you can manage your dental practice efficiently.</p>
    </div>

    <div class="dashboard-grid">
        <!-- LEFT CARD: PATIENTS WITH BALANCE -->
        <div class="card card-balance">
            <div class="card-header">
                <h2>Patients with Balance</h2>
                <span class="count-badge">3</span>
            </div>

            <div class="patient-list">
                <div class="patient-item">
                    <div class="patient-info">
                        <strong>Doe, John</strong>
                        <small>#101</small>
                    </div>
                    <span class="balance-amount">₱1,200.00</span>
                </div>

                <div class="patient-item">
                    <div class="patient-info">
                        <strong>Smith, Jane</strong>
                        <small>#102</small>
                    </div>
                    <span class="balance-amount">₱850.00</span>
                </div>

                <div class="patient-item">
                    <div class="patient-info">
                        <strong>Lee, Alex</strong>
                        <small>#103</small>
                    </div>
                    <span class="balance-amount">₱500.00</span>
                </div>
            </div>
        </div>

        <!-- RIGHT CARD: RECENT ACTIVITIES -->
        <div class="card card-activities" onclick="window.location='{{ route('tools') }}'">
            <div class="card-header">
                <h2>Recent Activities</h2>
                <div class="actions">
                    <small id="lastUpdated"></small>
                    <button id="refreshLogs"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                </div>
            </div>

            <!-- Right Card with buffer -->
            <div class="col-md-7">
                <div class="card shadow-sm border-1 border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Recent Activities</h5>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-3" id="lastUpdated"></small>
                                <button id="refreshLogs" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div id="recentActivitiesWrapper" class="col-md-12 position-relative" onclick="window.location='{{ route('tools') }}'" style="cursor: pointer;">
                            <!-- Loader -->
                            <div id="recentActivitiesLoading" class="d-flex justify-content-center align-items-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <span class="ms-2">Loading activities...</span>
                            </div>

                            <!-- Content (hidden until ready) -->
                            <div id="recentActivitiesContent" class="d-none">
                                @include('auth.partials.admin-dashboard-partial', ['logs' => $logs])
                            </div>
                        </div>
                    </div>
                </div>

                <div id="recentActivitiesContent" class="hidden">
                    @include('auth.partials.admin-dashboard-partial', ['logs' => $logs])
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
            document.getElementById('recentActivitiesLoading').classList.add('hidden');
            document.getElementById('recentActivitiesContent').classList.remove('hidden');
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
