<!-- Index Partial -->
<ul class="nav nav-tabs d-none" id="reportTabs" role="tablist">
    <li role="presentation">
        <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button"
            role="tab" aria-controls="dashboard" aria-selected="true">
            Dashboard
        </button>
    </li>
    <li role="presentation">
        <button class="nav-link" id="waitlist-detail-tab" data-bs-toggle="tab" data-bs-target="#waitlist-detail"
            type="button" role="tab" aria-controls="waitlist-detail" aria-selected="false">
            Waitlist Detail
        </button>
    </li>
    <li role="presentation">
        <button class="nav-link" id="revenue-detail-tab" data-bs-toggle="tab" data-bs-target="#revenue-detail"
            type="button" role="tab" aria-controls="revenue-detail" aria-selected="false">
            Revenue Detail
        </button>
    </li>
    <li role="presentation">
        <button class="nav-link" id="location-detail-tab" data-bs-toggle="tab" data-bs-target="#location-detail"
            type="button" role="tab" aria-controls="location-detail" aria-selected="false">
            Location Detail
        </button>
    </li>
    <li role="presentation">
        <button class="nav-link" id="treatment-detail-tab" data-bs-toggle="tab" data-bs-target="#treatment-detail"
            type="button" role="tab" aria-controls="treatment-detail" aria-selected="false">
            Treatment Detail
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content mt-3" id="reportTabsContent">
    <!-- Dashboard View -->
    <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
        <div class="row g-4">

            <!-- Waitlist Volume -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i> Waitlist Volume</h6>
                        <button class="btn btn-sm btn-light view-detail" data-tab="waitlist-detail-tab">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="waitlistChart" style="height:250px;"></canvas>

                        {{-- Data for ChartJS --}}
                        <script>
                            window.waitlistData = @json($waitlist->groupBy(fn($w) => $w->created_at?->format('Y-m-d'))->map->count());
                        </script>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2"></i> Revenue</h6>
                        <button class="btn btn-sm btn-light view-detail" data-tab="revenue-detail-tab">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" style="height:250px;"></canvas>

                        {{-- Data for ChartJS --}}
                        <script>
                            window.revenueData = @json($payments->groupBy(fn($p) => $p->created_at?->format('Y-m-d'))->map->sum('amount'));
                        </script>
                    </div>
                </div>
            </div>

            <!-- Location Demand -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt-fill me-2"></i> Location Demand</h6>
                        <button class="btn btn-sm btn-dark view-detail" data-tab="location-detail-tab">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="locationChart" style="height:250px;"></canvas>

                        {{-- Data for ChartJS --}}
                        <script>
                            window.locationData = @json(collect($locations)->groupBy(fn($loc) => $loc['province_name'] ?? 'Unknown')->map(fn($group) => count($group)));
                        </script>
                    </div>
                </div>
            </div>

            <!-- Common Treatments -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-activity me-2"></i> Common Treatments</h6>
                        <button class="btn btn-sm btn-light view-detail" data-tab="treatment-detail-tab">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="treatmentChart" style="height:250px;"></canvas>

                        {{-- Data for ChartJS --}}
                        <script>
                            window.treatmentData = @json($treatment->groupBy('treatment_type')->map->count());
                        </script>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Waitlist Detail -->
    <div class="tab-pane fade" id="waitlist-detail" role="tabpanel" aria-labelledby="waitlist-detail-tab">
        <button class="btn btn-secondary btn-sm mb-3" onclick="backToDashboard()">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </button>
        @include('pages.reports.partials.waitlist-partial')
    </div>

    <!-- Revenue Detail -->
    <div class="tab-pane fade" id="revenue-detail" role="tabpanel" aria-labelledby="revenue-detail-tab">
        <button class="btn btn-secondary btn-sm mb-3" onclick="backToDashboard()">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </button>
        {{-- @include('reports.partials.revenue-partial') --}}
    </div>

    <!-- Location Detail -->
    <div class="tab-pane fade" id="location-detail" role="tabpanel" aria-labelledby="location-detail-tab">
        <button class="btn btn-secondary btn-sm mb-3" onclick="backToDashboard()">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </button>
         @include('pages.reports.partials.location-partial')
    </div>

    <!-- Treatment Detail -->
    <div class="tab-pane fade" id="treatment-detail" role="tabpanel" aria-labelledby="treatment-detail-tab">
        <button class="btn btn-secondary btn-sm mb-3" onclick="backToDashboard()">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </button>
        {{-- @include('reports.partials.treatment-partial') --}}
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let charts = {};

        function aggregateDataByPeriod(data, period) {
            const entries = Object.entries(data).sort((a, b) => {
                return moment(a[0]).valueOf() - moment(b[0]).valueOf();
            });

            let labels = [], values = [];

            if (period === 'daily') {
                labels = entries.map(e => moment(e[0]).format('MMM DD'));
                values = entries.map(e => e[1]);
            } else if (period === 'weekly') {
                const weeks = {};
                entries.forEach(([date, value]) => {
                    const weekYear = moment(date).format('YYYY-[W]WW');
                    const weekLabel = `Week ${moment(date).week()}, ${moment(date).year()}`;
                    if (!weeks[weekYear]) {
                        weeks[weekYear] = { label: weekLabel, value: 0, sortKey: moment(date).valueOf() };
                    }
                    weeks[weekYear].value += value;
                });
                const sorted = Object.values(weeks).sort((a, b) => a.sortKey - b.sortKey);
                labels = sorted.map(w => w.label);
                values = sorted.map(w => w.value);
            } else if (period === 'monthly') {
                const months = {};
                entries.forEach(([date, value]) => {
                    const month = moment(date).format('YYYY-MM');
                    const monthLabel = moment(date).format('MMM YYYY');
                    if (!months[month]) {
                        months[month] = { label: monthLabel, value: 0, sortKey: moment(date).valueOf() };
                    }
                    months[month].value += value;
                });
                const sorted = Object.values(months).sort((a, b) => a.sortKey - b.sortKey);
                labels = sorted.map(m => m.label);
                values = sorted.map(m => m.value);
            } else if (period === 'quarterly') {
                const periods = {};
                entries.forEach(([date, value]) => {
                    const monthNum = moment(date).month();
                    const year = moment(date).year();
                    const periodNum = Math.floor(monthNum / 4) + 1;
                    const key = `${year}-P${periodNum}`;
                    const periodLabel = `Period ${periodNum} ${year}`;
                    if (!periods[key]) {
                        periods[key] = { label: periodLabel, value: 0, sortKey: moment(date).valueOf() };
                    }
                    periods[key].value += value;
                });
                const sorted = Object.values(periods).sort((a, b) => a.sortKey - b.sortKey);
                labels = sorted.map(p => p.label);
                values = sorted.map(p => p.value);
            } else if (period === 'semi-annual') {
                const semiAnnuals = {};
                entries.forEach(([date, value]) => {
                    const monthNum = moment(date).month();
                    const year = moment(date).year();
                    const half = monthNum < 6 ? 1 : 2;
                    const key = `${year}-H${half}`;
                    const halfLabel = `H${half} ${year}`;
                    if (!semiAnnuals[key]) {
                        semiAnnuals[key] = { label: halfLabel, value: 0, sortKey: moment(date).valueOf() };
                    }
                    semiAnnuals[key].value += value;
                });
                const sorted = Object.values(semiAnnuals).sort((a, b) => a.sortKey - b.sortKey);
                labels = sorted.map(h => h.label);
                values = sorted.map(h => h.value);
            } else if (period === 'annually') {
                const years = {};
                entries.forEach(([date, value]) => {
                    const year = moment(date).year();
                    years[year] = (years[year] || 0) + value;
                });
                const sorted = Object.entries(years).sort((a, b) => a[0] - b[0]);
                labels = sorted.map(y => y[0]);
                values = sorted.map(y => y[1]);
            }

            return { labels, values };
        }

        function initCharts() {
            const period = document.getElementById('timePeriod')?.value || 'daily';

            // Waitlist Chart
            const waitlistAgg = aggregateDataByPeriod(window.waitlistData, period);
            charts.waitlist = new Chart(
                document.getElementById('waitlistChart'), {
                    type: 'bar',
                    data: {
                        labels: waitlistAgg.labels,
                        datasets: [{
                            label: 'Waitlist Volume',
                            data: waitlistAgg.values,
                            backgroundColor: '#0d6efd'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );

            // Revenue Chart
            const revenueAgg = aggregateDataByPeriod(window.revenueData, period);
            charts.revenue = new Chart(
                document.getElementById('revenueChart'), {
                    type: 'line',
                    data: {
                        labels: revenueAgg.labels,
                        datasets: [{
                            label: 'Revenue',
                            data: revenueAgg.values,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25,135,84,0.2)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );

            // Location Demand Chart (doesn't change with time period)
            charts.location = new Chart(
                document.getElementById('locationChart'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(window.locationData),
                        datasets: [{
                            label: 'Location Demand',
                            data: Object.values(window.locationData),
                            backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#6c757d', '#6f42c1', '#fd7e14', '#d63384', '#0d6efd']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );

            // Treatment Chart (doesn't change with time period)
            charts.treatment = new Chart(
                document.getElementById('treatmentChart'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(window.treatmentData),
                        datasets: [{
                            label: 'Treatments',
                            data: Object.values(window.treatmentData),
                            backgroundColor: '#0dcaf0'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                }
            );
        }

        function updateCharts(period) {
            // Update Waitlist Chart
            if (charts.waitlist) {
                const waitlistAgg = aggregateDataByPeriod(window.waitlistData, period);
                charts.waitlist.data.labels = waitlistAgg.labels;
                charts.waitlist.data.datasets[0].data = waitlistAgg.values;
                charts.waitlist.update();
            }

            // Update Revenue Chart
            if (charts.revenue) {
                const revenueAgg = aggregateDataByPeriod(window.revenueData, period);
                charts.revenue.data.labels = revenueAgg.labels;
                charts.revenue.data.datasets[0].data = revenueAgg.values;
                charts.revenue.update();
            }

            // Location and Treatment charts don't need time period updates
        }

        initCharts();

        // Listen for time period changes
        const timePeriodSelect = document.getElementById('timePeriod');
        if (timePeriodSelect) {
            timePeriodSelect.addEventListener('change', (e) => {
                updateCharts(e.target.value);
            });
        }

        // View detail buttons
        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                new bootstrap.Tab(document.querySelector(`#${tabId}`)).show();
            });
        });

        // Restore last active tab
        const activeTabId = localStorage.getItem('activeReportTab');
        if (activeTabId) {
            const trigger = document.querySelector(`#reportTabs button#${activeTabId}`);
            if (trigger) new bootstrap.Tab(trigger).show();
        }

        // Remember active tab
        document.querySelectorAll('#reportTabs button').forEach(tab => {
            tab.addEventListener('shown.bs.tab', e => {
                localStorage.setItem('activeReportTab', e.target.id);
            });
        });
    });

    // Back button
    function backToDashboard() {
        new bootstrap.Tab(document.querySelector('#dashboard-tab')).show();
    }
</script>