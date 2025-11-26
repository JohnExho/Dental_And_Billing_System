@extends('layout')
@section('title', 'Reports | Chomply')
@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Reports Dashboard</h3>

            <div class="d-flex gap-2 align-items-center">
                <!-- Time Period Selector -->
                <select class="form-select" id="timePeriod" style="width: 200px;">
                    <option value="daily" selected>Daily (24 hours)</option>
                    <option value="weekly">Weekly (7 days)</option>
                    <option value="monthly">Monthly (31 days)</option>
                    <option value="quarterly">Quarterly (4 months)</option>
                    <option value="semi-annual">Semi-Annual (6 months)</option>
                    <option value="annually">Annually (12 months)</option>
                </select>
                
                <!-- Print Button -->
                <button class="btn btn-outline-primary btn-sm" onclick="printReportPage()" title="Print Report">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
            </div>
        </div>
        @include('pages.reports.partials.index-partial')
    
    <script>
        function printReportPage() {
            window.print();
        }
        
        // Add print styles
        const printStyle = `
            @media print {
                body { margin: 0; padding: 0; }
                .container { width: 100%; max-width: 100%; }
                .no-print { display: none !important; }
                .btn, .form-select, .dropdown { display: none !important; }
                .card { page-break-inside: avoid; break-inside: avoid; }
                canvas { max-height: 600px; }
            }
        `;
        const style = document.createElement('style');
        style.innerHTML = printStyle;
        document.head.appendChild(style);
    </script>
    @endsection