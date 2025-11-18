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
                        <script>
                            window.waitlistData = @json($waitlistByDateTime);
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
                        <script>
                            window.revenueData = @json($revenueData);
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
                        <script>
                            window.treatmentData = @json($treatmentData);
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
        @include('pages.reports.partials.payment-partial') 
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
    function backToDashboard() {
        const dashboardTabButton = document.querySelector('#dashboard-tab');
        if (!dashboardTabButton) return console.warn('Dashboard tab button not found!');

        // Get the existing Bootstrap tab instance or create it
        let tabInstance = bootstrap.Tab.getInstance(dashboardTabButton);
        if (!tabInstance) {
            tabInstance = new bootstrap.Tab(dashboardTabButton);
        }

        // Show the dashboard tab
        tabInstance.show();
    }


    document.addEventListener('DOMContentLoaded', function() {
        let charts = {};

        function aggregateWaitlistData(period) {
            const data = window.waitlistData || {};
            const entries = Object.entries(data).sort((a, b) => moment(a[0]).valueOf() - moment(b[0])
                .valueOf());
            const now = moment();
            let labels = [],
                values = [];

            const mapByDay = (daysBack) => {
                const map = {};
                entries.forEach(([date, count]) => {
                    const day = moment(date).format('YYYY-MM-DD');
                    map[day] = (map[day] || 0) + count;
                });
                for (let i = daysBack - 1; i >= 0; i--) {
                    const day = now.clone().subtract(i, 'days').format('YYYY-MM-DD');
                    labels.push(moment(day).format('MMM DD'));
                    values.push(map[day] || 0);
                }
            };

            const mapByMonth = (monthsBack) => {
                const map = {};
                entries.forEach(([date, count]) => {
                    const month = moment(date).format('YYYY-MM');
                    map[month] = (map[month] || 0) + count;
                });
                for (let i = monthsBack - 1; i >= 0; i--) {
                    const month = now.clone().subtract(i, 'months').format('YYYY-MM');
                    labels.push(moment(month).format('MMM YYYY'));
                    values.push(map[month] || 0);
                }
            };

            if (period === 'daily') {
                const today = now.format('YYYY-MM-DD');
                const hourlyMap = {};
                entries.forEach(([date, count]) => {
                    if (moment(date).format('YYYY-MM-DD') === today) {
                        const hour = moment(date).hour();
                        hourlyMap[hour] = (hourlyMap[hour] || 0) + count;
                    }
                });
                for (let i = 0; i < 24; i++) {
                    labels.push(moment(`${today} ${i}:00`, 'YYYY-MM-DD HH:mm').format('HH:mm'));
                    values.push(hourlyMap[i] || 0);
                }
            } else if (period === 'weekly') mapByDay(7);
            else if (period === 'monthly') mapByDay(31);
            else if (period === 'quarterly') mapByMonth(4);
            else if (period === 'semi-annual') mapByMonth(6);
            else if (period === 'annually') mapByMonth(12);

            return {
                labels,
                values
            };
        }

        function aggregateRevenueData(period) {
            const data = window.revenueData || {};
            const entries = Object.entries(data).sort((a, b) => moment(a[0]).valueOf() - moment(b[0])
                .valueOf());
            const now = moment();
            let labels = [],
                values = [];

            const mapByDay = (daysBack) => {
                const map = {};
                entries.forEach(([date, value]) => {
                    const day = moment(date).format('YYYY-MM-DD');
                    map[day] = (map[day] || 0) + value;
                });
                for (let i = daysBack - 1; i >= 0; i--) {
                    const day = now.clone().subtract(i, 'days').format('YYYY-MM-DD');
                    labels.push(moment(day).format('MMM DD'));
                    values.push(map[day] || 0);
                }
            };

            const mapByMonth = (monthsBack) => {
                const map = {};
                entries.forEach(([date, value]) => {
                    const month = moment(date).format('YYYY-MM');
                    map[month] = (map[month] || 0) + value;
                });
                for (let i = monthsBack - 1; i >= 0; i--) {
                    const month = now.clone().subtract(i, 'months').format('YYYY-MM');
                    labels.push(moment(month).format('MMM YYYY'));
                    values.push(map[month] || 0);
                }
            };

            if (period === 'daily') {
                const today = now.format('YYYY-MM-DD');
                const hourlyMap = {};
                entries.forEach(([date, value]) => {
                    if (moment(date).format('YYYY-MM-DD') === today) {
                        const hour = moment(date).hour();
                        hourlyMap[hour] = (hourlyMap[hour] || 0) + value;
                    }
                });
                for (let i = 0; i < 24; i++) {
                    labels.push(moment(`${today} ${i}:00`, 'YYYY-MM-DD HH:mm').format('HH:mm'));
                    values.push(hourlyMap[i] || 0);
                }
            } else if (period === 'weekly') mapByDay(7);
            else if (period === 'monthly') mapByDay(31);
            else if (period === 'quarterly') mapByMonth(4);
            else if (period === 'semi-annual') mapByMonth(6);
            else if (period === 'annually') mapByMonth(12);

            return {
                labels,
                values
            };
        }


        function initCharts() {
            const period = document.getElementById('timePeriod')?.value || 'daily';

            // Waitlist Chart
            const waitlistAgg = aggregateWaitlistData(period);
            charts.waitlist = new Chart(document.getElementById('waitlistChart'), {
                type: 'line',
                data: {
                    labels: waitlistAgg.labels,
                    datasets: [{
                        label: 'Waitlist Volume',
                        data: waitlistAgg.values,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Revenue Chart
            // Revenue Chart
            const revenueAgg = aggregateRevenueData(period);
            charts.revenue = new Chart(document.getElementById('revenueChart'), {
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
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });


            // Location Chart
            charts.location = new Chart(document.getElementById('locationChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(window.locationData),
                    datasets: [{
                        data: Object.values(window.locationData),
                        backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#6c757d', '#6f42c1',
                            '#fd7e14', '#d63384', '#0d6efd'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Treatment Chart
            charts.treatment = new Chart(document.getElementById('treatmentChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(window.treatmentData),
                    datasets: [{
                        data: Object.values(window.treatmentData),
                        backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#6c757d', '#6f42c1',
                            '#fd7e14', '#d63384', '#0d6efd'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

        }

        function updateCharts(period) {
            if (charts.waitlist) {
                const agg = aggregateWaitlistData(period);
                charts.waitlist.data.labels = agg.labels;
                charts.waitlist.data.datasets[0].data = agg.values;
                charts.waitlist.update();
            }
            if (charts.revenue) {
                const agg = aggregateRevenueData(period);
                charts.revenue.data.labels = agg.labels;
                charts.revenue.data.datasets[0].data = agg.values;
                charts.revenue.update();
            }

        }

        initCharts();

        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                new bootstrap.Tab(document.querySelector(`#${tabId}`)).show();
            });
        });

        document.querySelectorAll('#reportTabs button').forEach(tab => {
            tab.addEventListener('shown.bs.tab', e => localStorage.setItem('activeReportTab', e.target
                .id));
        });

        const activeTabId = localStorage.getItem('activeReportTab');
        if (activeTabId) {
            const trigger = document.querySelector(`#reportTabs button#${activeTabId}`);
            if (trigger) new bootstrap.Tab(trigger).show();
        }

        const timePeriodSelect = document.getElementById('timePeriod');
        if (timePeriodSelect) timePeriodSelect.addEventListener('change', e => updateCharts(e.target.value));


        function aggregateDataByPeriod(data, period) {
            const entries = Object.entries(data).sort((a, b) => moment(a[0]).valueOf() - moment(b[0])
                .valueOf());
            let labels = [],
                values = [];
            if (period === 'daily') {
                labels = entries.map(e => moment(e[0]).format('HH:mm'));
                values = entries.map(e => e[1]);
            } else if (period === 'weekly') {
                const weeks = {};
                entries.forEach(([date, value]) => {
                    const weekKey = moment(date).format('YYYY-[W]WW');
                    weeks[weekKey] ??= {
                        value: 0,
                        label: `Week ${moment(date).week()}, ${moment(date).year()}`,
                        sortKey: moment(date).valueOf()
                    };
                    weeks[weekKey].value += value;
                });
                const sorted = Object.values(weeks).sort((a, b) => a.sortKey - b.sortKey);
                labels = sorted.map(w => w.label);
                values = sorted.map(w => w.value);
            } else if (period === 'monthly') {
                const months = {};
                entries.forEach(([date, value]) => {
                    const m = moment(date).format('YYYY-MM');
                    months[m] ??= {
                        value: 0,
                        label: moment(date).format('MMM YYYY'),
                        sortKey: moment(date).valueOf()
                    };
                    months[m].value += value;
                });
                const sorted = Object.values(months).sort((a, b) => a.sortKey - b.sortKey);
                labels = sorted.map(m => m.label);
                values = sorted.map(m => m.value);
            }
            return {
                labels,
                values
            };
        }
    });
</script>
