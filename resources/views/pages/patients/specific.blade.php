<style>
    /* General hover effect for all patient tabs */
    #patientTabs .nav-link {
        position: relative;
        transition: all 0.25s ease-in-out;
    }

    #patientTabs .nav-link:hover {
        background-color: #e7f1ff !important;
        color: #0d6efd !important;
        transform: translateY(-2px);
    }

    /* Underline animation */
    #patientTabs .nav-link::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -3px;
        width: 0%;
        height: 2px;
        background-color: #0d6efd;
        transition: width 0.25s ease-in-out;
    }

    #patientTabs .nav-link:hover::after {
        width: 100%;
    }

    /* Active tab more noticeable */
    #patientTabs .nav-link.active {
        background-color: #0d6efd !important;
        color: white !important;
        font-weight: bold;
    }

    #patientTabs .nav-link.active::after {
        width: 100%;
        background-color: white;
    }

    .btn.btn-light {
        transition:
            background 0.4s ease-in-out,
            transform 0.4s ease-in-out,
            box-shadow 0.4s ease-in-out !important;
    }

    .btn.btn-light:hover {
        background: #e2e6ea !important;
        color: #000 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
    }

    .btn.btn-light:active {
        background: #d0d4d8 !important;
        transform: translateY(2px) scale(0.98) !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
    }

    .btn.btn-secondary {
        transition:
            background 0.4s ease-in-out,
            transform 0.4s ease-in-out,
            box-shadow 0.4s ease-in-out !important;
    }

    .btn.btn-secondary:hover {
        background: #606568 !important;
        color: #fff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
    }

    .btn.btn-secondary:active {
        background: #393b3d !important;
        transform: translateY(2px) scale(0.98) !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
    }
</style>

@extends('layout')
@section('title', 'Patient Profile | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">

            <!-- UPDATED HEADER COLOR -->
            <div class="card-header text-white position-relative" style="background-color: #0D6EFD;">
                @php
                    $defaultProfile = match ($patient->sex) {
                        'male' => asset('public/images/defaults/male.png'),
                        'female' => asset('public/images/defaults/female.png'),
                        default => asset('public/images/defaults/other.png'),
                    };

            $profileUrl = $patient->profile_picture
                ? asset('public/storage/' . $patient->profile_picture)
                : $defaultProfile;
                                @endphp

                <div class="d-flex justify-content-between align-items-center">
                    <!-- Left button -->
                    <button type="button" class="btn btn-secondary btn-sm" onclick="window.history.back()">
                        <i class="bi bi-arrow-left-circle me-1"></i> Return
                    </button>

                    <!-- Centered profile + name -->
                    <a href="#info" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="tab"
                        role="tab" aria-controls="info" aria-selected="true">
                        <img src="{{ $profileUrl }}" alt="Profile Picture"
                            class="rounded-circle border border-2 border-primary"
                            style="width: 50px; height: 50px; object-fit: cover;">
                        <h6 class="mb-0 text-white">{{ $patient->full_name }}</h6>
                    </a>

                    <!-- Right button -->
                    <a href="#" class="btn btn-light btn-sm d-flex align-items-center" data-bs-toggle="modal"
                        data-bs-target="#add-patient-modal">
                        <i class="bi bi-plus-circle"></i> Add Patient
                    </a>
                </div>
            </div>

            <div id="patient-container" class="position-relative">

                <!-- UPDATED FULL-WIDTH TABS -->
                <ul class="nav nav-tabs w-100 gap-3 justify-content-between flex-wrap"
                    id="patientTabs"
                    role="tablist"
                    style="overflow-x: auto; white-space: nowrap;">

                    <li role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info"
                            type="button" role="tab" aria-controls="info" aria-selected="true">
                            <i class="bi bi-person-circle me-2"></i> Patient Info
                        </button>
                    </li>

                    <li role="presentation">
                        <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress"
                            type="button" role="tab" aria-controls="progress" aria-selected="false">
                            <i class="bi bi-journal-text me-2"></i> Progress Notes
                        </button>
                    </li>

                    <li role="presentation">
                        <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions"
                            type="button" role="tab" aria-controls="prescriptions" aria-selected="false">
                            <i class="bi bi-prescription me-2"></i> Prescriptions
                        </button>
                    </li>

                    <li role="presentation">
                        <button class="nav-link" id="recalls-tab" data-bs-toggle="tab" data-bs-target="#recalls"
                            type="button" role="tab" aria-controls="recalls" aria-selected="false">
                            <i class="bi bi-calendar-check me-2"></i> Recalls
                        </button>
                    </li>

                    <li role="presentation">
                        <button class="nav-link" id="treatment-tab" data-bs-toggle="tab" data-bs-target="#treatment"
                            type="button" role="tab" aria-controls="treatment" aria-selected="false">
                            <i class="bi bi-clipboard-check me-2"></i> Treatment Plans
                        </button>
                    </li>

                    <li role="presentation">
                        <button class="nav-link" id="billing-tab" data-bs-toggle="tab" data-bs-target="#billing"
                            type="button" role="tab" aria-controls="billing" aria-selected="false">
                            <i class="bi bi-receipt me-2"></i> Billing
                        </button>
                    </li>
                </ul>

                <div id="patients-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading Patients...</p>
                </div>

                <div class="tab-content mt-3 w-100" id="patientTabsContent">
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        @include('pages.patients.partials.specific-partial')
                    </div>

                    <div class="tab-pane fade" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                        @include('pages.patients.partials.progress-notes')
                    </div>

                    <div class="tab-pane fade" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab">
                        @include('pages.patients.partials.prescriptions')
                    </div>

                    <div class="tab-pane fade" id="recalls" role="tabpanel" aria-labelledby="recalls-tab">
                        @include('pages.patients.partials.recalls')
                    </div>

                    <div class="tab-pane fade" id="treatment" role="tabpanel" aria-labelledby="treatment-tab">
                        @include('pages.patients.partials.treatment-plans')
                    </div>

                    <div class="tab-pane fade" id="billing" role="tabpanel" aria-labelledby="billing-tab">
                        @include('pages.patients.partials.billing')
                    </div>
                </div>
            </div>
        </div>

        @include('pages.patients.modals.add')

        <!-- DO NOT REMOVE JS -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('patients-loading').classList.add('d-none');
                const patientTabs = document.querySelectorAll('#patientTabs button');

                // Restore last active tab
                const activeTabId = localStorage.getItem('activePatientTab');
                if (activeTabId) {
                    const triggerEl = document.querySelector(`#patientTabs button#${activeTabId}`);
                    if (triggerEl) {
                        const tab = new bootstrap.Tab(triggerEl);
                        tab.show();
                    }
                }

                // Save active tab on click
                patientTabs.forEach(tab => {
                    tab.addEventListener('shown.bs.tab', function(event) {
                        localStorage.setItem('activePatientTab', event.target.id);
                    });
                });

                // Clicking name returns to info tab
                document.querySelectorAll('a[href="#info"]').forEach(el => {
                    el.addEventListener('click', function(e) {
                        e.preventDefault();
                        const tabTrigger = new bootstrap.Tab(document.querySelector('#info-tab'));
                        tabTrigger.show();
                    });
                });
            });
        </script>

@endsection
