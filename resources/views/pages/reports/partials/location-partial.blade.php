<div class="card shadow-sm border-0">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-geo-alt-fill me-2"></i> Location Detail
        </h6>
    </div>

    <div class="card-body">
        <!-- Filters -->
        <div class="mb-3 d-flex gap-2 flex-wrap">
            <select id="provinceFilter" class="form-select" style="width: 200px;">
                <option value="">-- Select Province --</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province['id'] }}">{{ $province['name'] }}</option>
                @endforeach
            </select>

            <select id="cityFilter" class="form-select" style="width: 200px;">
                <option value="">-- Select City --</option>
                @foreach ($cities as $city)
                    <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                @endforeach
            </select>

            <select id="barangayFilter" class="form-select" style="width: 200px;">
                <option value="">-- Select Barangay --</option>
                @foreach ($barangays as $barangay)
                    <option value="{{ $barangay['id'] }}">{{ $barangay['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-center">
            <div style="position: relative; height: 400px; width: 400px;">
                <canvas id="locationDetailChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const allLocations = @json($locations);
                let locationChart = null;

                function initLocationChart() {
                    const ctx = document.getElementById('locationDetailChart');
                    if (!ctx) return;

                    // Destroy existing chart if it exists
                    if (locationChart) {
                        locationChart.destroy();
                    }

                    locationChart = new Chart(ctx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Patient Locations',
                                data: [],
                                backgroundColor: [
                                    '#ffc107', '#0dcaf0', '#198754', '#6c757d',
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
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Patient Distribution by Location',
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    padding: {
                                        top: 10,
                                        bottom: 20
                                    }
                                }
                            }
                        }
                    });

                    updateLocationChart();
                }

                function filterLocations() {
                    const provinceId = document.getElementById('provinceFilter').value;
                    const cityId = document.getElementById('cityFilter').value;
                    const barangayId = document.getElementById('barangayFilter').value;

                    return allLocations.filter(loc => {
                        let matches = true;
                        if (provinceId) matches = matches && loc.province_id == provinceId;
                        if (cityId) matches = matches && loc.city_id == cityId;
                        if (barangayId) matches = matches && loc.barangay_id == barangayId;
                        return matches;
                    });
                }

                function updateLocationChart() {
                    if (!locationChart) return;

                    const provinceId = document.getElementById('provinceFilter').value;
                    const cityId = document.getElementById('cityFilter').value;
                    const barangayId = document.getElementById('barangayFilter').value;

                    const filtered = filterLocations();
                    const counts = {};

                    filtered.forEach(loc => {
                        let label;

                        // If barangay is selected, show individual barangays
                        if (barangayId) {
                            label = loc.barangay_name;
                        }
                        // If city is selected (but not barangay), break down by barangays
                        else if (cityId) {
                            label = loc.barangay_name;
                        }
                        // If province is selected (but not city), break down by cities
                        else if (provinceId) {
                            label = loc.city_name;
                        }
                        // If nothing selected, show provinces
                        else {
                            label = loc.province_name;
                        }

                        counts[label] = (counts[label] || 0) + 1;
                    });

                    locationChart.data.labels = Object.keys(counts);
                    locationChart.data.datasets[0].data = Object.values(counts);
                    locationChart.update();
                }

                // Attach filter change events
                ['provinceFilter', 'cityFilter', 'barangayFilter'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.addEventListener('change', updateLocationChart);
                    }
                });

                // Initialize chart when location tab is shown
                const locationTab = document.querySelector('#location-detail-tab');
                if (locationTab) {
                    locationTab.addEventListener('shown.bs.tab', function() {
                        setTimeout(initLocationChart, 100);
                    });
                }

                // Initialize immediately if tab is already visible
                const locationPane = document.querySelector('#location-detail');
                if (locationPane && locationPane.classList.contains('show')) {
                    setTimeout(initLocationChart, 100);
                }
            });
        </script>
    </div>
</div>
<!-- Top 3 Forecasted Locations -->
@if(!session('clinic_id') && !empty($forecastedLocationValue['clusters']))
<div class="mt-4 row g-3">
    @php
        $topForecasts = collect($forecastedLocationValue['clusters'] ?? [])
            ->sortByDesc(fn($c) => $c['demand_30d'] ?? 0)
            ->take(3);
    @endphp

    @foreach($topForecasts as $cluster)
        <div class="col-md-4">
            <div class="card border-warning shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">{{ $cluster['province_name'] ?? 'N/A' }} - {{ $cluster['city_name'] ?? 'N/A' }}</h6>
                    <p class="mb-1"><strong>Barangay:</strong> {{ $cluster['barangay_name'] ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>30-Day Demand:</strong> {{ $cluster['demand_30d'] ?? 0 }}</p>
                    <p class="mb-1"><strong>Forecast Next 7 Days:</strong></p>
                    <ul class="mb-0 ps-3">
                        @foreach($cluster['forecast_next_7_days'] ?? [] as $date => $value)
                            <li>{{ \Carbon\Carbon::parse($date)->format('M d') }}: {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif