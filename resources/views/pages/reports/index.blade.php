@extends('layout')
@section('title', 'Reports | Chomply')
@section('content')
<style>
@media print {
    /* Hide everything except the partial content */
    body * {
        visibility: hidden;
    }
    
    /* Show only the report content */
    #printableArea,
    #printableArea * {
        visibility: visible;
    }
    
        #printableArea,
    #printableArea * {
        page-break-inside: avoid;
        page-break-after: avoid;
        page-break-before: avoid;
    }
    /* Position the printable area at the top left */
    #printableArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    /* Add 10% blank space at the top */
    #printableArea {
        margin-top: 10%;
    }
    
    /* Hide buttons and controls when printing */
    .btn,
    .dropdown,
    .d-flex.justify-content-between,
    #generateReportBtn {
        display: none !important;
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