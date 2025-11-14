@extends('layout')
@section('title', 'Reports | Chomply')
@section('content')
<div class="container py-4">
    <h3 class="mb-4">Reports Dashboard</h3>

    <div class="row g-4">
        <!-- Waitlist Volume -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i> Waitlist Volume</h6>
                </div>
                <div class="card-body">
                    <canvas id="waitlistChart" style="height:250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2"></i> Revenue</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="height:250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Location Demand -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt-fill me-2"></i> Location Demand</h6>
                </div>
                <div class="card-body">
                    <canvas id="locationChart" style="height:250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Common Treatments -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-activity me-2"></i> Common Treatments</h6>
                </div>
                <div class="card-body">
                    <canvas id="treatmentChart" style="height:250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Waitlist Volume
    new Chart(document.getElementById('waitlistChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Week 1','Week 2','Week 3','Week 4'],
            datasets: [{
                label: 'Waitlist',
                data: [12, 19, 8, 15],
                backgroundColor: '#0d6efd'
            }]
        },
        options: { responsive:true }
    });

    // Revenue
    new Chart(document.getElementById('revenueChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun'],
            datasets: [{
                label: 'Revenue ($)',
                data: [1200, 1500, 1300, 1700, 1800, 2000],
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive:true }
    });

    // Location Demand
    new Chart(document.getElementById('locationChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Clinic A','Clinic B','Clinic C','Clinic D'],
            datasets: [{
                label: 'Demand',
                data: [35, 25, 20, 15],
                backgroundColor: ['#ffc107','#0dcaf0','#198754','#6c757d']
            }]
        },
        options: { responsive:true }
    });

    // Common Treatments
    new Chart(document.getElementById('treatmentChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Cleaning','Filling','Extraction','Whitening'],
            datasets: [{
                label: 'Treatments',
                data: [40, 25, 15, 20],
                backgroundColor:'#0dcaf0'
            }]
        },
        options: { responsive:true, indexAxis:'y', plugins:{legend:{display:false}} }
    });
});
</script>
@endpush
@endsection
