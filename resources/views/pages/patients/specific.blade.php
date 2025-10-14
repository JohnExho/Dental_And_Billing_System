@extends('layout')
@section('title', 'Patient Profile | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white position-relative">
                @php
                    $defaultProfile = match ($patient->sex) {
                        'male' => asset('storage/defaults/male.png'),
                        'female' => asset('storage/defaults/female.png'),
                        default => asset('storage/defaults/other.png'),
                    };

                    $profileUrl = $patient->profile_picture ? Storage::url($patient->profile_picture) : $defaultProfile;
                @endphp

                <div class="d-flex justify-content-between align-items-center">
                    <!-- Left button -->
                    <button type="button" class="btn btn-secondary btn-sm" onclick="window.history.back()">
                        <i class="bi bi-arrow-left-circle me-1"></i> Return
                    </button>

                    <!-- Centered profile + name as a flex anchor -->
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
                <ul class="nav nav-tabs" id="patientTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info"
                            type="button" role="tab" aria-controls="info" aria-selected="true">
                            Patient Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress"
                            type="button" role="tab" aria-controls="progress" aria-selected="false">
                            Progress Notes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions"
                            type="button" role="tab" aria-controls="prescriptions" aria-selected="false">
                            Prescriptions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="diagnostics-tab" data-bs-toggle="tab" data-bs-target="#diagnostics"
                            type="button" role="tab" aria-controls="diagnostics" aria-selected="false">
                            Diagnostics
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates"
                            type="button" role="tab" aria-controls="certificates" aria-selected="false">
                            Certificates
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                            type="button" role="tab" aria-controls="general" aria-selected="false">
                            General Notes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="recalls-tab" data-bs-toggle="tab" data-bs-target="#recalls"
                            type="button" role="tab" aria-controls="recalls" aria-selected="false">
                            Recalls
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="treatment-tab" data-bs-toggle="tab" data-bs-target="#treatment"
                            type="button" role="tab" aria-controls="treatment" aria-selected="false">
                            Treatment Plans
                        </button>
                    </li>
                </ul>
                <div id="patients-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading Patients...</p>
                </div>

                <div class="tab-content mt-3" id="patientTabsContent">
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        {{-- Patient Info Partial --}}
                        @include('pages.patients.partials.specific-partial')
                    </div>

                    <div class="tab-pane fade" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                        @include('pages.patients.partials.progress-notes' )
                    </div>

                    <div class="tab-pane fade" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab">
                        @include('pages.patients.partials.prescriptions')
                    </div>

                    <div class="tab-pane fade" id="diagnostics" role="tabpanel" aria-labelledby="diagnostics-tab">
                        @include('pages.patients.partials.diagnostics')
                    </div>

                    <div class="tab-pane fade" id="certificates" role="tabpanel" aria-labelledby="certificates-tab">
                        @include('pages.patients.partials.certificates')
                    </div>

                    <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                        @include('pages.patients.partials.general-notes')
                    </div>

                    <div class="tab-pane fade" id="recalls" role="tabpanel" aria-labelledby="recalls-tab">
                        @include('pages.patients.partials.recalls')
                    </div>

                    <div class="tab-pane fade" id="treatment" role="tabpanel" aria-labelledby="treatment-tab">
                        @include('pages.patients.partials.treatment-plans')
                    </div>
                </div>

            </div>

        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.patients.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('patients-loading').classList.add('d-none');
                const patientTabs = document.querySelectorAll('#patientTabs button');
                const tabContent = document.querySelectorAll('#patientTabsContent .tab-pane');

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
