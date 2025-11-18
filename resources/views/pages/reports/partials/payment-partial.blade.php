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

            function aggregateRevenueData(period) {
                const revenueData = window.revenueData || {};
                const entries = Object.entries(revenueData).sort((a, b) => moment(a[0]).valueOf() - moment(b[0]).valueOf());
                let labels = [], data = [];
                const now = moment();

                const mapByDay = (daysBack) => {
                    const map = {};
                    entries.forEach(([date, value]) => {
                        const day = moment(date).format('YYYY-MM-DD');
                        map[day] = (map[day] || 0) + value;
                    });
                    for (let i = daysBack - 1; i >= 0; i--) {
                        const day = now.clone().subtract(i, 'days').format('YYYY-MM-DD');
                        labels.push(moment(day).format('MMM DD'));
                        data.push(map[day] || 0);
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
                        data.push(map[month] || 0);
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
                        data.push(hourlyMap[i] || 0);
                    }
                } else if (period === 'weekly') {
                    mapByDay(7);
                } else if (period === 'monthly') {
                    mapByDay(31);
                } else if (period === 'quarterly') {
                    mapByMonth(4);
                } else if (period === 'semi-annual') {
                    mapByMonth(6);
                } else if (period === 'annually') {
                    mapByMonth(12);
                }

                return { labels, data };
            }

            function initRevenueDetailChart() {
                const ctx = document.getElementById('revenueDetailChart');
                if (!ctx) return;

                if (revenueDetailChart) revenueDetailChart.destroy();

                const period = document.getElementById('timePeriod')?.value || 'daily';
                const { labels, data } = aggregateRevenueData(period);

                revenueDetailChart = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Revenue',
                            data: data,
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
            }

            function updateRevenueDetailChart(period) {
                if (!revenueDetailChart) return;
                const { labels, data } = aggregateRevenueData(period);
                revenueDetailChart.data.labels = labels;
                revenueDetailChart.data.datasets[0].data = data;
                revenueDetailChart.update();
            }

            const revenueTab = document.querySelector('#revenue-detail-tab');
            if (revenueTab) revenueTab.addEventListener('shown.bs.tab', () => setTimeout(initRevenueDetailChart, 100));

            const revenuePane = document.querySelector('#revenue-detail');
            if (revenuePane && revenuePane.classList.contains('show')) setTimeout(initRevenueDetailChart, 100);

            const timePeriodSelect = document.getElementById('timePeriod');
            if (timePeriodSelect) timePeriodSelect.addEventListener('change', e => updateRevenueDetailChart(e.target.value));
        });
        </script>
    </div>
</div>
