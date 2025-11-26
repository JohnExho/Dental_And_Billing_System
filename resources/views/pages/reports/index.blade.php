@extends('layout')
@section('title', 'Reports | Chomply')
@section('content')
<style>
@media print {
    /* Force landscape orientation */
    @page {
        size: landscape;
        margin: 0.5in;
    }
    
    /* Hide everything except the partial content */
    body * {
        visibility: hidden;
    }
    
    /* Show only the report content */
    #printableArea,
    #printableArea * {
        visibility: visible;
    }
    
    /* Position the printable area at the top left */
    #printableArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100vh;
        overflow: hidden;
    }
    
    /* Scale content to fit on one page */
    #printableArea {
        transform: scale(0.85);
        transform-origin: top left;
    }
    
    /* Prevent page breaks */
    #printableArea,
    #printableArea * {
        page-break-inside: avoid;
        page-break-after: avoid;
        page-break-before: avoid;
    }
    
    /* Hide buttons and controls when printing */
    .btn,
    .dropdown,
    .d-flex.justify-content-between,
    #generateReportBtn {
        display: none !important;
    }
    
    /* Adjust chart sizes for better fit */
    canvas {
        max-height: 200px !important;
    }
}
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Reports Dashboard</h3>

        <!-- Time Period Selector -->
        <div class="dropdown">
            <select class="form-select" id="timePeriod" style="width: 200px;">
                <option value="daily" selected>Daily (24 hours)</option>
                <option value="weekly">Weekly (7 days)</option>
                <option value="monthly">Monthly (31 days)</option>
                <option value="quarterly">Quarterly (4 months)</option>
                <option value="semi-annual">Semi-Annual (6 months)</option>
                <option value="annually">Annually (12 months)</option>
            </select>
        </div>

        <div>
            <button class="btn btn-primary" id="generateReportBtn">Generate Report</button>
        </div>
    </div>

    <!-- Wrap the partial in a printable area -->
    <div id="printableArea">
        @include('pages.reports.partials.index-partial')
    </div>
</div>

<script>
    document.getElementById('generateReportBtn').addEventListener('click', function () {
        window.print();
    });
</script>

@endsection