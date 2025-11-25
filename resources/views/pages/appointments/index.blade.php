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

    /* ======================================
       FIXED BUTTON STYLES (Custom Classes)
       ====================================== */

    /* OUTLINE BUTTON (Show Appointments) */
    .action-btn-outline {
        border: 2px solid #0d6efd !important;
        color: #0d6efd !important;
        background-color: transparent !important;
        transition: all 0.3s ease-in-out !important;
    }

    .action-btn-outline:hover {
        background-color: #e2e6ea !important;
        color: #000 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
    }

    .action-btn-outline:active {
        transform: translateY(2px) scale(0.97) !important;
        background-color: #d0d4d8 !important;
        color: #000 !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2) !important;
    }

    /* PRIMARY BUTTON (New Appointment) */
    .action-btn-primary {
        background-color: #0d6efd !important;
        border: none !important;
        color: white !important;
        transition: all 0.3s ease-in-out !important;
    }

    .action-btn-primary:hover {
        background-color: #0b5ed7 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
    }

    .action-btn-primary:active {
        background-color: #0a58ca !important;
        transform: translateY(2px) scale(0.97) !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2) !important;
    }
</style>
@endsection

@section('content')

<div class="container-fluid py-1">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 bg-light p-3 border-end" style="height: 100vh">
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
                    <label class="form-check-label d-flex align-items-center"
                        for="assoc_{{ $associate->associate_id }}">
                        <span style="width:12px; height:12px; background-color: {{ $associate->color ?? '#6c757d' }}; 
                                display:inline-block; border-radius:50%; margin-right:6px;"></span>
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

        <!-- Right side -->
        <div class="col-md-10 d-flex flex-column gap-3">

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Toggle Button -->
                <button id="toggle-partial" class="btn action-btn-outline">
                    <i class="bi bi-arrow-repeat me-2"></i> Show Appointments
                </button>

                <button type="button" class="btn action-btn-primary"
                    data-bs-toggle="modal" data-bs-target="#add-appointment-modal">
                    <i class="bi bi-plus-circle"></i> New Appointment
                </button>
            </div>

            @include('pages.appointments.modals.add')
            @include('pages.appointments.modals.edit')
            @include('pages.appointments.modals.delete')

            <!-- Partials -->
            <div id="calendar-partial">
                @include('pages.appointments.partials.index-partial')
            </div>

            <div id="appointments-partial" class="d-none">
                @include('pages.appointments.partials.list-partial')
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggle-partial');
    const calendarPartial = document.getElementById('calendar-partial');
    const appointmentsPartial = document.getElementById('appointments-partial');

    const updateURLTab = (tab) => {
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
    }

    const params = new URLSearchParams(window.location.search);
    if (params.get('tab') === 'appointments') {
        calendarPartial.classList.add('d-none');
        appointmentsPartial.classList.remove('d-none');
        toggleBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Show Calendar';
    }

    toggleBtn.addEventListener('click', () => {
        const isCalendarVisible = !calendarPartial.classList.contains('d-none');

        if (isCalendarVisible) {
            calendarPartial.classList.add('d-none');
            appointmentsPartial.classList.remove('d-none');
            toggleBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Show Calendar';
            updateURLTab('appointments');
        } else {
            calendarPartial.classList.remove('d-none');
            appointmentsPartial.classList.add('d-none');
            toggleBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Show Appointments';
            updateURLTab('calendar');
        }
    });

    document.querySelectorAll('.delete-appointment-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const appointmentId = btn.dataset.id;
            const input = document.getElementById('delete_appointment_id');
            if (input) input.value = appointmentId;

            const deleteModalEl = document.getElementById('delete-appointment-modal');
            if (deleteModalEl) new bootstrap.Modal(deleteModalEl).show();
        });
    });
});
</script>
@endpush

@endsection
