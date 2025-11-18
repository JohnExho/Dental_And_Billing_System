<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-people-fill me-2"></i> Waitlist Detail
        </h6>
    </div>

    <div class="card-body">
        <canvas id="waitlistDetailChart" style="height:400px;"></canvas>

        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let waitlistDetailChart = null;

                function initWaitlistDetailChart() {
                    const ctx = document.getElementById('waitlistDetailChart');
                    if (!ctx) return;

                    // Destroy existing chart if it exists
                    if (waitlistDetailChart) {
                        waitlistDetailChart.destroy();
                    }

                    const waitlistData = window.waitlistData || {};

                    waitlistDetailChart = new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: Object.keys(waitlistData),
                            datasets: [{
                                label: 'Waitlist Volume',
                                data: Object.values(waitlistData),
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

                    // Get current period from dropdown and update chart
                    const currentPeriod = document.getElementById('timePeriod')?.value || 'daily';
                    updateWaitlistDetailChart(currentPeriod);
                }

                // Update function
                function updateWaitlistDetailChart(period) {
                    if (!waitlistDetailChart) return;

                    const waitlistData = window.waitlistData || {};
                    let labels = [],
                        data = [];

                    // Sort entries by date first
                    const entries = Object.entries(waitlistData).sort((a, b) => {
                        return moment(a[0]).valueOf() - moment(b[0]).valueOf();
                    });
if (period === 'daily') {
    const dataMap = {};
    const today = moment().format('YYYY-MM-DD'); // current day

    // Aggregate counts by hour for today only
    entries.forEach(([date]) => {
        if (moment(date).format('YYYY-MM-DD') === today) {
            const hour = moment(date).hour();  // 0-23
            dataMap[hour] = (dataMap[hour] || 0) + 1; // +1 per patient
        }
    });

    // Create all 24 hours as labels based on the same date
    labels = [];
    data = [];
    for (let i = 0; i < 24; i++) {
        const label = moment(today + ' ' + i + ':00', 'YYYY-MM-DD HH:mm').format('HH:mm');
        labels.push(label);
        data.push(dataMap[i] || 0);
    }
}


else if (period === 'weekly') {
                        // Show complete 7 days
                        const now = moment();
                        const dataMap = {};

                        entries.forEach(([date, count]) => {
                            const dateKey = moment(date).format('YYYY-MM-DD');
                            dataMap[dateKey] = (dataMap[dateKey] || 0) + count;
                        });

                        // Create all 7 days
                        for (let i = 6; i >= 0; i--) {
                            const day = now.clone().subtract(i, 'days');
                            const dateKey = day.format('YYYY-MM-DD');
                            labels.push(day.format('MMM DD'));
                            data.push(dataMap[dateKey] || 0);
                        }
                    } else if (period === 'monthly') {
                        // Show complete 31 days
                        const now = moment();
                        const dataMap = {};

                        entries.forEach(([date, count]) => {
                            const dateKey = moment(date).format('YYYY-MM-DD');
                            dataMap[dateKey] = (dataMap[dateKey] || 0) + count;
                        });

                        // Create all 31 days
                        for (let i = 30; i >= 0; i--) {
                            const day = now.clone().subtract(i, 'days');
                            const dateKey = day.format('YYYY-MM-DD');
                            labels.push(day.format('MMM DD'));
                            data.push(dataMap[dateKey] || 0);
                        }
                    } else if (period === 'quarterly') {
                        // Show last 4 months on x-axis
                        const now = moment();
                        const months = {};

                        // Initialize last 4 months with 0
                        for (let i = 3; i >= 0; i--) {
                            const month = now.clone().subtract(i, 'months');
                            const monthKey = month.format('YYYY-MM');
                            months[monthKey] = {
                                label: month.format('MMM YYYY'),
                                count: 0
                            };
                        }

                        // Aggregate waitlist counts into the months
                        entries.forEach(([date, count]) => {
                            const monthKey = moment(date).format('YYYY-MM');
                            if (months[monthKey]) {
                                months[monthKey].count += count;
                            }
                        });

                        const sorted = Object.values(months);
                        labels = sorted.map(m => m.label);
                        data = sorted.map(m => m.count);
                    } else if (period === 'semi-annual') {
                        // Show last 6 months on x-axis
                        const now = moment();
                        const months = {};

                        // Initialize last 6 months with 0
                        for (let i = 5; i >= 0; i--) {
                            const month = now.clone().subtract(i, 'months');
                            const monthKey = month.format('YYYY-MM');
                            months[monthKey] = {
                                label: month.format('MMM YYYY'),
                                count: 0
                            };
                        }

                        // Aggregate waitlist counts into the months
                        entries.forEach(([date, count]) => {
                            const monthKey = moment(date).format('YYYY-MM');
                            if (months[monthKey]) {
                                months[monthKey].count += count;
                            }
                        });

                        const sorted = Object.values(months);
                        labels = sorted.map(m => m.label);
                        data = sorted.map(m => m.count);
                    } else if (period === 'annually') {
                        // Show all 12 months of the year on x-axis
                        const now = moment();
                        const months = {};

                        // Initialize all 12 months with 0
                        for (let i = 11; i >= 0; i--) {
                            const month = now.clone().subtract(i, 'months');
                            const monthKey = month.format('YYYY-MM');
                            months[monthKey] = {
                                label: month.format('MMM YYYY'),
                                count: 0
                            };
                        }

                        // Aggregate waitlist counts into the months
                        entries.forEach(([date, count]) => {
                            const monthKey = moment(date).format('YYYY-MM');
                            if (months[monthKey]) {
                                months[monthKey].count += count;
                            }
                        });

                        const sorted = Object.values(months); // Already in chronological order
                        labels = sorted.map(m => m.label);
                        data = sorted.map(m => m.count);
                    }

                    waitlistDetailChart.data.labels = labels;
                    waitlistDetailChart.data.datasets[0].data = data;
                    waitlistDetailChart.update();
                }

                // Initialize chart when waitlist tab is shown
                const waitlistTab = document.querySelector('#waitlist-detail-tab');
                if (waitlistTab) {
                    waitlistTab.addEventListener('shown.bs.tab', function() {
                        setTimeout(initWaitlistDetailChart, 100);
                    });
                }

                // Initialize immediately if tab is already visible
                const waitlistPane = document.querySelector('#waitlist-detail');
                if (waitlistPane && waitlistPane.classList.contains('show')) {
                    setTimeout(initWaitlistDetailChart, 100);
                }

                // Listen for main dashboard filter change
                const timePeriodSelect = document.getElementById('timePeriod');
                if (timePeriodSelect) {
                    timePeriodSelect.addEventListener('change', (e) => {
                        updateWaitlistDetailChart(e.target.value);
                    });
                }
            });
        </script>
    </div>
</div>
