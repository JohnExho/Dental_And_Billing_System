@extends('layout')
@section('title', 'Appointment | Chomply')
@section('styles')
    <style>
       /* Calendar layout */
    table { table-layout: fixed; width: 100%; }
    td { height: 100px; vertical-align: top; padding: 8px; position: relative; }
    td div.badge { font-size: 0.75rem; white-space: normal; line-height: 1.1rem; }

    /* Strong button specificity to override Bootstrap */
    button.btn.action-btn-primary,
    .btn.action-btn-primary {
        background-color: #0d6efd !important;
        border: 0 !important;
        color: #fff !important;
        padding: 0.45rem 0.9rem !important;
        border-radius: 0.45rem !important;
        box-shadow: 0 4px 10px rgba(13,110,253,0.12) !important;
        transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease !important;
    }
    button.btn.action-btn-primary:hover,
    .btn.action-btn-primary:hover,
    .btn.action-btn-primary:focus {
        background-color: #0b5ed7 !important;
        transform: translateY(-2px) !important;
        outline: none !important;
        box-shadow: 0 8px 18px rgba(13,110,253,0.16) !important;
    }
    button.btn.action-btn-primary:active,
    .btn.action-btn-primary:active,
    .btn.action-btn-primary.active {
        background-color: #0a58ca !important;
        transform: translateY(1px) scale(0.99) !important;
        box-shadow: 0 3px 8px rgba(10,88,202,0.18) !important;
    }

    /* SHOW APPOINTMENTS (outline but visible) - high specificity */
    /* Use both .action-btn-outline and element selector to beat Bootstrap */
    button.btn.action-btn-outline,
    .btn.action-btn-outline {
        /* visible background for contrast on white header */
        background: linear-gradient(180deg, #ffffff 0%, #f3f6fb 100%) !important;
        border: 1.5px solid rgba(13,110,253,0.95) !important;
        color: #0d6efd !important;
        padding: 0.42rem 0.85rem !important;
        border-radius: 0.45rem !important;
        box-shadow: 0 3px 8px rgba(13,110,253,0.06) !important;
        transition: background-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease, color 0.18s ease !important;
        backdrop-filter: none !important;
    }

    /* ensure focus (keyboard) is visible */
    button.btn.action-btn-outline:focus,
    .btn.action-btn-outline:focus {
        outline: 3px solid rgba(13,110,253,0.12) !important;
        box-shadow: 0 6px 14px rgba(13,110,253,0.12) !important;
    }

    /* hover/active states */
    button.btn.action-btn-outline:hover,
    .btn.action-btn-outline:hover {
        background: #e9f2ff !important; /* light blue tint */
        color: #03396c !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 20px rgba(13,110,253,0.08) !important;
    }

    button.btn.action-btn-outline:active,
    .btn.action-btn-outline:active,
    .btn.action-btn-outline.active {
        background: #d7eaff !important;
        color: #022a53 !important;
        transform: translateY(1px) scale(0.995) !important;
        box-shadow: 0 3px 8px rgba(2,42,83,0.12) !important;
    }

    /* If bootstrap applies btn-outline-* classes, neutralize them */
    .btn.action-btn-outline.btn-outline-primary,
    button.btn.action-btn-outline.btn-outline-primary {
        background: inherit !important;
        border-color: rgba(13,110,253,0.95) !important;
        color: #0d6efd !important;
    }

    /* Make sure icons inside buttons keep correct color */
    button.btn.action-btn-outline i,
    .btn.action-btn-outline i,
    button.btn.action-btn-primary i,
    .btn.action-btn-primary i {
        color: inherit !important;
        opacity: 0.95 !important;
    }

    /* utility: prevent pointer-events issues from overlays */
    button.btn.action-btn-outline[disabled],
    button.btn.action-btn-primary[disabled],
    .btn.action-btn-outline[disabled],
    .btn.action-btn-primary[disabled] {
        opacity: 0.6 !important;
        pointer-events: none !important;
    }

    </style>
    
@endsection
@section('content')

    <div class="container-fluid py-1">
        <div class="row">
            <!-- Sidebar (spans full height) -->
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
                                <span
                                    style="width:12px; height:12px; background-color: {{ $associate->color ?? '#6c757d' }}; display:inline-block; border-radius:50%; margin-right:6px;"></span>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Toggle Button -->
                    <button id="toggle-partial" class="btn btn-outline-primary action-btn">
                        <i class="bi bi-arrow-repeat me-2"></i> Show Appointments
                    </button>

                    <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal"
                        data-bs-target="#add-appointment-modal">
                        <i class="bi bi-plus-circle"></i> New Appointment
                    </button>
                </div>
                @include('pages.appointments.modals.add')
                @include('pages.appointments.modals.edit')
                @include('pages.appointments.modals.delete')

                <!-- Partial Content -->
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

    // Utility: update URL without reloading
    const updateURLTab = (tab) => {
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
    }

    // Show appointments if URL contains ?tab=appointments
    const params = new URLSearchParams(window.location.search);
    if (params.get('tab') === 'appointments') {
        calendarPartial.classList.add('d-none');
        appointmentsPartial.classList.remove('d-none');
        toggleBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Show Calendar';
    }

    // Toggle button
    toggleBtn.addEventListener('click', () => {
        const isCalendarVisible = !calendarPartial.classList.contains('d-none');

        if (isCalendarVisible) {
            calendarPartial.classList.add('d-none');
            appointmentsPartial.classList.remove('d-none');
            toggleBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Show Calendar';
            updateURLTab('appointments'); // Update URL
        } else {
            calendarPartial.classList.remove('d-none');
            appointmentsPartial.classList.add('d-none');
            toggleBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Show Appointments';
            updateURLTab('calendar'); // Update URL
        }
    });

    // Delete appointment buttons
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
