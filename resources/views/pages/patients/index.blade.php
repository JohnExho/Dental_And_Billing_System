<style>
    /* Smooth transitions */
    .btn.btn-light {
        transition:
            background 0.4s ease-in-out,
            transform 0.4s ease-in-out,
            box-shadow 0.4s ease-in-out !important;
    }

    /* Hover */
    .btn.btn-light:hover {
        background: #e2e6ea !important;
        color: #000 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15) !important;
    }

    /* Active */
    .btn.btn-light:active {
        background: #d0d4d8 !important;
        transform: translateY(2px) scale(0.98) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }
</style>

@extends('layout')
@section('title', 'Patient | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-3">

                        <span>No. of Patients # {{ $patientCount }}</span>

                        <!-- My Patients Filter Button -->
                        <button id="filter-account-btn" class="btn btn-sm btn-outline-light"
                            title="Toggle: Show only my patients">
                            <i class="bi bi-funnel"></i> My Patients
                        </button>

                        <!-- Archived Toggle Button -->
                        <button id="toggle-archive-btn" class="btn btn-light btn-sm d-flex align-items-center gap-1">
                            <i class="bi bi-archive"></i>
                            <span id="archive-btn-label">
                                {{ request('archived') == 1 ? 'Show Active Patients' : 'Show Archived' }}
                            </span>
                        </button>

                    </div>

                    <!-- Add Patient Button (hidden in archived view) -->
                    @if (request('archived') != 1)
                        <a href="#" class="btn btn-light btn-sm d-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#add-patient-modal">
                            <i class="bi bi-plus-circle"></i> Add Patient
                        </a>
                    @endif
                </div>
            </div>

            <div id="patient-container" class="position-relative">
                <div id="patients-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading Patients...</p>
                </div>

                <div id="patients-content" class="d-none">
                    @include('pages.patients.partials.index-partial')
                </div>
            </div>

        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.patients.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Hide loading and show content
                document.getElementById('patients-loading').classList.add('d-none');
                document.getElementById('patients-content').classList.remove('d-none');

                // Filter by account toggle
                const filterBtn = document.getElementById('filter-account-btn');
                const url = new URL(window.location);

                // Check if filter is already active
                const isFilterActive = url.searchParams.get('filter_by_account') === '1';

                // Update button appearance based on filter state
                if (isFilterActive) {
                    filterBtn.classList.remove('btn-outline-light');
                    filterBtn.classList.add('btn-light');
                }

                filterBtn.addEventListener('click', function() {
                    const url = new URL(window.location);
                    const isActive = url.searchParams.get('filter_by_account') === '1';

                    if (isActive) {
                        // Remove filter
                        url.searchParams.delete('filter_by_account');
                        filterBtn.classList.remove('btn-light');
                        filterBtn.classList.add('btn-outline-light');
                    } else {
                        // Add filter
                        url.searchParams.set('filter_by_account', '1');
                        filterBtn.classList.remove('btn-outline-light');
                        filterBtn.classList.add('btn-light');
                    }

                    // Redirect with new filter parameter
                    window.location.href = url.toString();
                });

                // Archive toggle button logic
                const archiveBtn = document.getElementById('toggle-archive-btn');
                const archiveLabel = document.getElementById('archive-btn-label');

                archiveBtn.addEventListener('click', function() {
                    const url = new URL(window.location);

                    const isArchived = url.searchParams.get('archived') === '1';

                    if (isArchived) {
                        // Switch to normal active view
                        url.searchParams.delete('archived');
                    } else {
                        // Switch to archived view
                        url.searchParams.set('archived', '1');
                    }

                    window.location.href = url.toString();
                });

                // Highlight the button when in archived mode
                const isArchived = url.searchParams.get('archived') === '1';
                if (isArchived) {
                    archiveBtn.classList.add('btn-warning');
                    archiveBtn.classList.remove('btn-light');
                    archiveLabel.textContent = 'Show Active Patients';
                } else {
                    archiveBtn.classList.remove('btn-warning');
                    archiveBtn.classList.add('btn-light');
                    archiveLabel.textContent = 'Show Archived';
                }
            });
        </script>
    @endsection
