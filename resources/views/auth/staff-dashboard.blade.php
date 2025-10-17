@extends('layout')
@section('title', 'Dashboard | Chomply')
@section('content')
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Welcome to the Dashboard</h1>
                <p class="text-center">This is where you can manage your dental practice efficiently.</p>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Left Card (Patients with Balance) -->
            <div class="col-md-5">
                <div class="card shadow-sm border-1 border-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0 text-danger">Patients with Balance</h5>
                            <span class="badge bg-danger-subtle text-danger border border-danger">
                                {{ $unpaidBills->count() }}
                            </span>
                        </div>
                    @include("auth.partials.dashboard-bill-partial")
                    </div>
                </div>
            </div>

            <!-- Right Card with buffer -->
            <!-- Right Card (Today's Appointments - Fake Data) -->
            <div class="col-md-7">
                <div class="card shadow-sm border-1 border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0 text-info">Today's Appointments</h5>
                            <span class="badge bg-info-subtle text-info border border-info">
                                4
                            </span>
                        </div>

                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Garcia, Maria</strong><br>
                                    <small class="text-muted">10:00 AM</small>
                                </div>
                                <span class="badge bg-primary">Check-up</span>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Santos, Juan</strong><br>
                                    <small class="text-muted">11:30 AM</small>
                                </div>
                                <span class="badge bg-primary">Cleaning</span>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Reyes, Anna</strong><br>
                                    <small class="text-muted">1:00 PM</small>
                                </div>
                                <span class="badge bg-primary">Filling</span>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Lopez, Mark</strong><br>
                                    <small class="text-muted">3:15 PM</small>
                                </div>
                                <span class="badge bg-primary">Extraction</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Day.js -->
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

                // Simulate loading buffer
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(() => {
                        document.getElementById('recentActivitiesLoading').classList.add('d-none');
                        document.getElementById('recentActivitiesContent').classList.remove('d-none');
                        updateTimes();
                    }, 600); // ~0.6s buffer for effect
                });

                // Run immediately & refresh every 5 min
                setInterval(updateTimes, 300000);

                // Manual refresh button
                document.addEventListener("click", (e) => {
                    if (e.target.closest('#refreshLogs')) {
                        updateTimes();
                    }
                });
            </script>
        @endsection
