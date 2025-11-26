@extends('layout')
@section('title', 'Reports | Chomply')
@section('content')


    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 100% !important;
                padding: 0 !important;
            }
            .card {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            canvas {
                max-height: 500px !important;
            }
        }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h3 class="mb-0">Reports Dashboard</h3>

            <!-- Time Period Selector -->
            <div class="d-flex gap-3 align-items-center">
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
                <button class="btn btn-primary" id="generateReportBtn">Generate Report</button>
            </div>
        </div>

        <script>
            document.getElementById('generateReportBtn').addEventListener('click', function () {
                window.print();
            });
        </script>
        @include('pages.reports.partials.index-partial')
    @endsection