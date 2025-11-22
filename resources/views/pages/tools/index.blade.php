<style>
.btn-primary {
    transition:
        background 0.4s ease-in-out,
        transform 0.4s ease-in-out,
        box-shadow 0.4s ease-in-out !important;
}

.btn-primary:hover {
    background: #0D6EFD !important;
    color: #ffffff !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
}

.btn-primary:active {
    background: #003f9c !important;
    transform: translateY(2px) scale(0.98) !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
}

.btn-secondary {
    transition:
        background 0.4s ease-in-out,
        transform 0.4s ease-in-out,
        box-shadow 0.4s ease-in-out !important;
}

.btn-secondary:hover {
    background: #5a6269 !important;
    color: #ffffff !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
}

.btn-secondary:active {
    background: #4e5257 !important;
    transform: translateY(2px) scale(0.98) !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
}
</style>

@extends('layout')
@section('title', 'Tools | Chomply')
@section('content')

    <div class="container-fluid py-5">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h1 class="fw-bold">Tools</h1>

                {{-- 🔧 FIXED: changed <div class="row"> to <div class="d-flex gap-2">
                     This makes the buttons appear in a ROW instead of a COLUMN --}}
                <div class="d-flex gap-2">

                    @if (session('clinic_id'))
                        <a href="{{ route('process-export-patients') }}" class="btn btn-primary" target="_blank">
                            <i class="bi bi-file-earmark-arrow-down"></i> Export Patient Data
                        </a>
                    @else
                        <a href="#" class="btn btn-primary disabled">
                            <i class="bi bi-file-earmark-arrow-down"></i> Export Patient Data
                        </a>
                    @endif

                    @if (session('clinic_id'))
                        <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#qrModal">
                            <i class="bi bi-gear-fill"></i> QR Codes
                        </a>
                    @else
                        <a href="#" class="btn btn-secondary disabled" data-bs-toggle="modal" data-bs-target="#qrModal">
                            <i class="bi bi-gear-fill"></i> QR Codes
                        </a>
                    @endif

                </div> {{-- END FIXED BUTTON LAYOUT --}}
            </div>
        </div>

        {{-- Recent Activities Card --}}
        <div class="card shadow-sm border-info">
            <div class="card-body">

                {{-- Card Header --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                    <div class="d-flex align-items-center gap-3">
                        <small class="text-muted" id="lastUpdated"></small>
                        <button id="refreshLogs" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </button>
                    </div>
                </div>

                {{-- Activity Wrapper --}}
                <div id="recentActivitiesWrapper" class="position-relative">

                    {{-- Loader --}}
                    <div id="recentActivitiesLoading" class="d-flex justify-content-center align-items-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <span class="ms-2">Loading activities...</span>
                    </div>

                    {{-- Logs Content --}}
                    <div id="recentActivitiesContent" class="d-none">
                        @include('pages.tools.partials.index-partial', ['logs' => $logs])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.tools.modals.qr')

    {{-- Day.js --}}
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

        document.addEventListener("DOMContentLoaded", () => {
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
