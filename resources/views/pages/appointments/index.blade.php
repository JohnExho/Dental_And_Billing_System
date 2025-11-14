@extends('layout')
@section('title', 'Appointment | Chomply')
@section('styles')
    <style>
        table {
            table-layout: fixed;
            width: 100%;
        }

        td {
            height: 100px;
            vertical-align: top;
            padding: 8px;
            position: relative;
        }

        td div.badge {
            font-size: 0.75rem;
            white-space: normal;
            line-height: 1.1rem;
        }
    </style>
@endsection
@section('content')

    <div class="container-fluid py-1">
        <div class="row">
            <!-- Sidebar (spans full height) -->
            <div class="col-md-2 bg-light p-3 border-end">
                <h5 class="mb-3 fw-bold">Associates</h5>
                <form id="filterForm" method="GET">
                    <input type="hidden" name="year" value="{{ $currentYear }}">
                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                    <input type="hidden" name="view" value="{{ $viewMode }}">

@foreach ($associates as $associate)
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="associates[]"
            value="{{ $associate->associate_id }}" id="assoc_{{ $associate->associate_id }}"
            onchange="this.form.submit()"
            {{ in_array($associate->associate_id, $associatesFilter) ? 'checked' : '' }}>
        <label class="form-check-label d-flex align-items-center" for="assoc_{{ $associate->associate_id }}">
            <span style="width:12px; height:12px; background-color: {{ $associate->color ?? '#6c757d' }}; display:inline-block; border-radius:50%; margin-right:6px;"></span>
            {{ $associate->full_name }}
        </label>
    </div>
@endforeach


                    <h5 class="mt-4 mb-3 fw-bold">Event Types</h5>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="show_followups" id="show_followups"
                            onchange="this.form.submit()" {{ $showFollowups ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_followups">
                            <i class="bi bi-arrow-repeat"></i> Follow-Ups/Recalls
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="show_appointments" id="show_appointments"
                            onchange="this.form.submit()" {{ $showAppointments ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_appointments">
                            <i class="bi bi-calendar-check"></i> Appointments
                        </label>
                    </div>
                </form>
            </div>

            <!-- Right side: button + calendar -->
            <div class="col-md-10 d-flex flex-column gap-3">
                <!-- Action Button -->
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> New Appointment
                    </a>
                </div>

                <!-- Calendar -->
                <div>
                    @include('pages.appointments.partials.index-partial')
                </div>
            </div>
        </div>
    </div>

@endsection
