<style>
.btn-secondary {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-secondary:hover {
    transform: translateX(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-secondary:active {
    transform: translateX(-3px) scale(0.98);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.btn-secondary i {
    transition: transform 0.3s ease;
}

.btn-secondary:hover i {
    transform: translateX(-3px);
}
</style>

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
                            window.forecastedWaitlistValue = @json($forecastedWaitlistValue);
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
                            window.forecastedRevenueValue = @json($forecastedRevenueValue);
                        </script>
                    </div>
                </div>
            </div>
            <HR>
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
        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-secondary btn-sm" onclick="backToDashboard()">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="printWaitlistDetail()" title="Print Waitlist Detail">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
        @include('pages.reports.partials.waitlist-partial')
    </div>

    <!-- Revenue Detail -->
    <div class="tab-pane fade" id="revenue-detail" role="tabpanel" aria-labelledby="revenue-detail-tab">
        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-secondary btn-sm" onclick="backToDashboard()">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="printRevenueDetail()" title="Print Revenue Detail">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
        @include('pages.reports.partials.payment-partial')
    </div>

    <!-- Location Detail -->
    <div class="tab-pane fade" id="location-detail" role="tabpanel" aria-labelledby="location-detail-tab">
        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-secondary btn-sm" onclick="backToDashboard()">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="printLocationDetail()" title="Print Location Detail">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
        @include('pages.reports.partials.location-partial')
    </div>

    <!-- Treatment Detail -->
    <div class="tab-pane fade" id="treatment-detail" role="tabpanel" aria-labelledby="treatment-detail-tab">
        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-secondary btn-sm" onclick="backToDashboard()">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="printTreatmentDetail()" title="Print Treatment Detail">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
        @include('pages.reports.partials.treatment-partial')
    </div>
</div>  

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script>
    function backToDashboard() {
        const dashboardTabButton = document.querySelector('#dashboard-tab');
        if (!dashboardTabButton) return console.warn('Dashboard tab button not found!');

        let tabInstance = bootstrap.Tab.getInstance(dashboardTabButton);
        if (!tabInstance) {
            tabInstance = new bootstrap.Tab(dashboardTabButton);
        }

        tabInstance.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        let charts = {};

        // Debug: Log revenue data
        console.log('Revenue Data:', window.revenueData);

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

        function aggregateRevenueData(period, clinicId = null) {
            const allData = window.revenueData || {};

            // Use only selected clinic's data or sum all clinics
            let entries = [];
            if (clinicId && allData[clinicId]) {
                entries = Object.entries(allData[clinicId]);
            } else {
                const summed = {};
                Object.values(allData).forEach(clinic => {
                    Object.entries(clinic).forEach(([ts, val]) => {
                        summed[ts] = (summed[ts] || 0) + parseFloat(val);
                    });
                });
                entries = Object.entries(summed);
            }

            entries.sort((a, b) => moment(a[0]).valueOf() - moment(b[0]).valueOf());
            const now = moment();
            let labels = [],
                values = [],
                forecastData = [];

            if (period === 'daily') {
                const today = now.format('YYYY-MM-DD');
                const hourlyMap = {};
                entries.forEach(([date, value]) => {
                    if (moment(date).format('YYYY-MM-DD') === today) {
                        const hour = moment(date).hour();
                        hourlyMap[hour] = (hourlyMap[hour] || 0) + parseFloat(value);
                    }
                });
                for (let i = 0; i < 24; i++) {
                    labels.push(moment(`${today} ${i}:00`, 'YYYY-MM-DD HH:mm').format('HH:mm'));
                    values.push(hourlyMap[i] || 0);
                }
                forecastData = new Array(24).fill(null);
            } else {
                // Map by day (weekly/monthly)
                const mapByDay = (daysBack) => {
                    const map = {};
                    entries.forEach(([date, value]) => {
                        const day = moment(date).format('YYYY-MM-DD');
                        map[day] = (map[day] || 0) + parseFloat(value);
                    });
                    for (let i = daysBack - 1; i >= 0; i--) {
                        const day = now.clone().subtract(i, 'days').format('YYYY-MM-DD');
                        labels.push(moment(day).format('MMM DD'));
                        values.push(map[day] || 0);
                    }
                };

                // Map by month (quarter, semi-annual, annual)
                const mapByMonth = (monthsBack) => {
                    const map = {};
                    entries.forEach(([date, value]) => {
                        const month = moment(date).format('YYYY-MM');
                        map[month] = (map[month] || 0) + parseFloat(value);
                    });
                    for (let i = monthsBack - 1; i >= 0; i--) {
                        const month = now.clone().subtract(i, 'months').format('YYYY-MM');
                        labels.push(moment(month).format('MMM YYYY'));
                        values.push(map[month] || 0);
                    }
                };

                if (period === 'weekly') mapByDay(7);
                else if (period === 'monthly') mapByDay(31);
                else if (period === 'quarterly') mapByMonth(3);
                else if (period === 'semi-annual') mapByMonth(6);
                else if (period === 'annually') mapByMonth(12);

                // Forecast (only for weekly/monthly if available)
                const revenueForecast = window.forecastedRevenueValue?.forecast_next_7_days || {};
                if (Object.keys(revenueForecast).length && (period === 'weekly' || period === 'monthly')) {
                    const forecastDates = Object.keys(revenueForecast).sort();
                    const forecastLabels = forecastDates.map(d => moment(d).format('MMM DD'));
                    labels = [...labels, ...forecastLabels];
                    forecastData = [...new Array(values.length).fill(null), ...forecastDates.map(d =>
                        revenueForecast[d])];
                    values = [...values, ...new Array(forecastDates.length).fill(null)];
                }
            }

            return {
                labels,
                values,
                forecastData
            };
        }





        function initCharts() {
            const period = document.getElementById('timePeriod')?.value || 'weekly';

            // Waitlist Chart with Forecast (only for weekly)
            const waitlistAgg = aggregateWaitlistData(period);

            let allLabels = [...waitlistAgg.labels];
            let historicalData = [...waitlistAgg.values];
            let forecastData = [];

            if (period === 'weekly') {
                const waitlistForecast = window.forecastedWaitlistValue?.forecast_next_7_days || {};
                const forecastDates = Object.keys(waitlistForecast).sort();

                const forecastLabels = forecastDates.map(d => moment(d).format('MMM DD'));
                allLabels = [...allLabels, ...forecastLabels];

                const forecastValues = forecastDates.map(date => waitlistForecast[date]);

                historicalData = [...historicalData, ...new Array(forecastValues.length).fill(null)];

                forecastData = [...new Array(waitlistAgg.values.length).fill(null), ...forecastValues];
            } else {
                forecastData = [];
            }

            charts.waitlist = new Chart(document.getElementById('waitlistChart'), {
                type: 'line',
                data: {
                    labels: allLabels,
                    datasets: [{
                        label: 'Historical Waitlist',
                        data: historicalData,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.2)',
                        fill: true,
                        tension: 0.4,
                        spanGaps: false
                    }, {
                        label: 'Forecasted Waitlist',
                        data: forecastData,
                        borderColor: '#fd7e14',
                        backgroundColor: 'rgba(253,126,20,0.2)',
                        fill: true,
                        tension: 0.4,
                        borderDash: [5, 5],
                        spanGaps: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            filter: function(tooltipItem) {
                                return tooltipItem.parsed.y !== null;
                            }
                        }
                    }
                }
            });

            // Revenue Chart
            const revenueAgg = aggregateRevenueData(period);
            charts.revenue = new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: revenueAgg.labels,
                    datasets: [{
                            label: 'Historical Revenue',
                            data: revenueAgg.values,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25,135,84,0.2)',
                            fill: true,
                            tension: 0.4,
                            spanGaps: false
                        },
                        {
                            label: 'Forecasted Revenue',
                            data: revenueAgg.forecastData,
                            borderColor: '#20c997',
                            backgroundColor: 'rgba(32,201,151,0.2)',
                            fill: true,
                            tension: 0.4,
                            borderDash: [5, 5],
                            spanGaps: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => '₱' + v.toLocaleString()
                            }
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

                let allLabels = [...agg.labels];
                let historicalData = [...agg.values];
                let forecastData = [];

                if (period === 'weekly') {
                    const waitlistForecast = window.forecastedWaitlistValue?.forecast_next_7_days || {};
                    const forecastDates = Object.keys(waitlistForecast).sort();

                    const forecastLabels = forecastDates.map(d => moment(d).format('MMM DD'));
                    allLabels = [...allLabels, ...forecastLabels];

                    const forecastValues = forecastDates.map(date => waitlistForecast[date]);

                    historicalData = [...historicalData, ...new Array(forecastValues.length).fill(null)];

                    forecastData = [...new Array(agg.values.length).fill(null), ...forecastValues];
                } else {
                    forecastData = [];
                }

                charts.waitlist.data.labels = allLabels;
                charts.waitlist.data.datasets[0].data = historicalData;
                charts.waitlist.data.datasets[1].data = forecastData;
                charts.waitlist.update();
            }

            if (charts.revenue) {
                const agg = aggregateRevenueData(period);
                charts.revenue.data.labels = agg.labels;
                charts.revenue.data.datasets[0].data = agg.values;
                charts.revenue.data.datasets[1].data = agg.forecastData;
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
    });
</script>
