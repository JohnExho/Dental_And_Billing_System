<div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-cash-stack me-2"></i> Revenue Detail
        </h6>
    </div>

    <div class="card-body">
        <canvas id="revenueDetailChart" style="height:400px;"></canvas>

        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let revenueDetailChart = null;

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

                function initRevenueDetailChart() {
                    const ctx = document.getElementById('revenueDetailChart');
                    if (!ctx) return;

                    if (revenueDetailChart) revenueDetailChart.destroy();

                    const period = document.getElementById('timePeriod')?.value || 'weekly';
                    const {
                        labels,
                        values,
                        forecastData
                    } = aggregateRevenueData(period);

                    revenueDetailChart = new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Historical Revenue',
                                data: values,
                                borderColor: '#198754',
                                backgroundColor: 'rgba(25,135,84,0.2)',
                                fill: true,
                                tension: 0.4,
                                spanGaps: false
                            }, {
                                label: 'Forecasted Revenue',
                                data: forecastData,
                                borderColor: '#20c997',
                                backgroundColor: 'rgba(32,201,151,0.2)',
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
                                intersect: false
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: v => '₱' + v.toLocaleString()
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
                }

                function updateRevenueDetailChart(period) {
                    if (!revenueDetailChart) return;
                    const {
                        labels,
                        values,
                        forecastData
                    } = aggregateRevenueData(period);
                    revenueDetailChart.data.labels = labels;
                    revenueDetailChart.data.datasets[0].data = values;
                    revenueDetailChart.data.datasets[1].data = forecastData;
                    revenueDetailChart.update();
                }

                const revenueTab = document.querySelector('#revenue-detail-tab');
                if (revenueTab) revenueTab.addEventListener('shown.bs.tab', () => setTimeout(initRevenueDetailChart,
                    100));

                const revenuePane = document.querySelector('#revenue-detail');
                if (revenuePane && revenuePane.classList.contains('show')) setTimeout(initRevenueDetailChart, 100);

                const timePeriodSelect = document.getElementById('timePeriod');
                if (timePeriodSelect) timePeriodSelect.addEventListener('change', e => updateRevenueDetailChart(e.target
                    .value));
            });
        </script>
    </div>
</div>
