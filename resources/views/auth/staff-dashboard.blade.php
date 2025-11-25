@extends('layout')
@section('title', 'Dashboard | Chomply')
@section('content')

    <style>
        /* Hover lift for cards */
        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 1rem 1.5rem rgba(0, 0, 0, 0.15);
        }

        /* Card title spacing */
        .card-title {
            font-weight: 600;
        }

        /* List group hover effect */
        .list-group-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        /* Remove underline from links */
        a.text-decoration-none:hover {
            text-decoration: none;
        }
    </style>

    <div class="container-fluid mt-5">

        <div class="row g-4">
            <!-- Left Card (Patients with Balance) -->

            <!-- Right Card (Today's Appointments - Live Data) -->
            <div class="col-md-5">
                @if (session('clinic_id'))
                    <a href="{{ route('appointments') }}" class="text-decoration-none">
                        <div class="card dashboard-card shadow-sm border border-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title text-info mb-0">Today's Appointments</h5>
                                    <span class="badge bg-info-subtle text-info border border-info">
                                        {{ $todayAppointments->count() }}
                                    </span>
                                </div>
                                <div class="list-group list-group-flush">
                                    @forelse($todayAppointments as $appointment)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $appointment->patient->last_name }},
                                                    {{ $appointment->patient->first_name }}</strong><br>
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</small>
                                            </div>
                                            <span
                                                class="badge bg-primary">{{ ucfirst($appointment->appointment_type ?? 'Appointment') }}</span>
                                        </div>
                                    @empty
                                        <div class="list-group-item text-center text-muted">
                                            <i class="bi bi-calendar-x"></i> No appointments scheduled for today
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </a>
                @else
                    <div class="card dashboard-card shadow-sm border border-info">
                        <div class="card-body text-muted" style="cursor: default;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title text-info mb-0">Today's Appointments</h5>
                            </div>
                            <div class="list-group list-group-flush">
                                @forelse($todayAppointments as $appointment)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $appointment->patient->last_name }},
                                                {{ $appointment->patient->first_name }}</strong><br>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</small>
                                        </div>
                                        <span
                                            class="badge bg-primary">{{ ucfirst($appointment->appointment_type ?? 'Appointment') }}</span>
                                    </div>
                                @empty
                                    <div class="list-group-item text-center text-muted">
                                        <i class="bi bi-calendar-x"></i> No appointments scheduled for today
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-md-7">
                <div class="card dashboard-card shadow-sm border border-danger">
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
            if (lastUpdated) lastUpdated.textContent = "Last updated: " + now.format("MMM D, YYYY h:mm A");
        }

        document.addEventListener("DOMContentLoaded", () => {
            setTimeout(() => {
                const loading = document.getElementById('recentActivitiesLoading');
                const content = document.getElementById('recentActivitiesContent');
                if (loading) loading.classList.add('d-none');
                if (content) content.classList.remove('d-none');
                updateTimes();
            }, 600);
        });

        setInterval(updateTimes, 300000); // refresh every 5 min

        document.addEventListener("click", e => {
            if (e.target.closest('#refreshLogs')) updateTimes();
        });
    </script>
@endsection
