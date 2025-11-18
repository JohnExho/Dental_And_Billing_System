@extends('layout')
@section('title', 'Reports | Chomply')
@section('content')
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
        </div>
        @include('pages.reports.partials.index-partial')
    @endsection