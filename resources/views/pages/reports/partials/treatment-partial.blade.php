<div class="card shadow-sm border-0">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-activity me-2"></i> Common Treatments
        </h6>
    </div>

    <div class="card-body">
        <div class="d-flex justify-content-center">
            <div style="position: relative; height: 400px; width: 400px;">
                <canvas id="treatmentDetailChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allTreatments = @json($treatment); // your treatments data
            let treatmentChart = null;

            function initTreatmentChart() {
                const ctx = document.getElementById('treatmentDetailChart');
                if (!ctx) return;

                if (treatmentChart) {
                    treatmentChart.destroy();
                }

                treatmentChart = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Treatment Counts',
                            data: [],
                            backgroundColor: [
                                '#0dcaf0', '#ffc107', '#198754', '#6c757d',
                                '#6f42c1', '#fd7e14', '#d63384', '#0d6efd'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { 
                                position: 'bottom',
                                labels: { padding: 15, font: { size: 12 } }
                            },
                            title: { 
                                display: true, 
                                text: 'Common Treatments',
                                font: { size: 16, weight: 'bold' },
                                padding: { top: 10, bottom: 20 }
                            }
                        }
                    }
                });

                updateTreatmentChart();
            }

            function updateTreatmentChart() {
                if (!treatmentChart) return;

                const counts = {};

                allTreatments.forEach(t => {
                    const label = t.treatment_type || t.treatment_name;
                    counts[label] = (counts[label] || 0) + 1;
                });

                treatmentChart.data.labels = Object.keys(counts);
                treatmentChart.data.datasets[0].data = Object.values(counts);
                treatmentChart.update();
            }

            // Initialize chart when treatment tab is shown
            const treatmentTab = document.querySelector('#treatment-detail-tab');
            if (treatmentTab) {
                treatmentTab.addEventListener('shown.bs.tab', function() {
                    setTimeout(initTreatmentChart, 100);
                });
            }

            // Initialize immediately if tab is already visible
            const treatmentPane = document.querySelector('#treatment-detail');
            if (treatmentPane && treatmentPane.classList.contains('show')) {
                setTimeout(initTreatmentChart, 100);
            }
        });
        </script>
    </div>
</div>
<!-- Top 3 Forecasted Treatments -->
@if(!empty($forecastedTreatmentValue['treatments']))
<div class="mt-4 row g-3">
    @php
        $treatments = collect($forecastedTreatmentValue['treatments']);
        $totals7d = $forecastedTreatmentValue['treatment_totals_7d'];
        $next7days = $forecastedTreatmentValue['forecast_next_7_days'];

        // Sort treatments by 7-day total and take top 3
        $topTreatments = $treatments->sortByDesc(fn($t) => $totals7d[$t] ?? 0)->take(3);
    @endphp

    @foreach($topTreatments as $treatment)
        <div class="col-md-4">
            <div class="card border-info shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">{{ ucfirst($treatment) }}</h6>
                    <p class="mb-1"><strong>7-Day Total Forecast:</strong> {{ number_format($totals7d[$treatment] ?? 0, 2) }}</p>
                    <p class="mb-1"><strong>Next 7 Days Forecast:</strong></p>
                    <ul class="mb-0 ps-3">
                        @foreach($next7days as $dayForecast)
                            <li>
                                {{ \Carbon\Carbon::parse($dayForecast['date'])->format('M d') }}: 
                                {{ number_format($dayForecast['predictions'][$treatment] ?? 0, 2) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif
