@extends("layout")
@section('title', 'Dashboard | Chomply')
@section("content")
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Welcome to the Dashboard</h1>
                <p class="text-center">This is where you can manage your dental practice efficiently.</p>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Left Card (Empty) -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <!-- Empty card -->
                    </div>
                </div>
            </div>

            <!-- Right Card (Recent Activities) -->
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

                        @if($logs->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="col-md-3">Name</th>
                                            <th>Description</th>
                                            <th class="col-md-4">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                            <tr>
                                                <td>
                                                    <strong title="Account ID: {{ $log->account_id }}">
                                                        {{ $log->account_name_snapshot ?? 'N/A' }}
                                                    </strong>
                                                </td>

                                                <td>{{ $log->description }}</td>
                                                <td>
                                                    <!-- Live relative time + exact tooltip -->
                                                    <span class="text-muted" data-time="{{ $log->created_at->toIso8601String() }}"
                                                        title="{{ $log->created_at->format('M d, Y h:i A') }}">
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No recent activities.</p>
                        @endif
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
            document.getElementById('lastUpdated').textContent =
                "Last updated: " + now.format("MMM D, YYYY h:mm A");
        }

        // Run immediately & set to refresh every 5 min
        updateTimes();
        setInterval(updateTimes, 300000);

        // Manual refresh button
        document.getElementById('refreshLogs').addEventListener('click', () => {
            updateTimes();
        });
    </script>
@endsection