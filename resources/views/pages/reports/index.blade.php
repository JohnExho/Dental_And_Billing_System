@extends('layout')
@section('title', 'Reports | Chomply')
@section('content')


    <div class="container py-4">
        <div class="d-flex justify-content-between al</div>ign-items-center mb-4">
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

<script>
    document.getElementById('generateReportBtn').addEventListener('click', function () {
        window.print();
    });
</script>

        </div>
        @include('pages.reports.partials.index-partial')
    @endsection