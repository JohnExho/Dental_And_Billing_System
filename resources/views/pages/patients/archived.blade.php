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
@section('title', 'Archived Patients | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <div class="d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-3">

                        <span><i class="bi bi-archive"></i> Archived Patients # {{ $patientCount }}</span>

                        <!-- My Patients Filter Button -->
                        <button id="filter-account-btn" class="btn btn-sm btn-outline-dark"
                            title="Toggle: Show only my patients">
                            <i class="bi bi-funnel"></i> My Patients
                        </button>

                        <!-- Back to Active Patients Button -->
                        <a href="{{ route('patients') }}" class="btn btn-light btn-sm d-flex align-items-center gap-1">
                            <i class="bi bi-arrow-left"></i>
                            <span>Back to Active Patients</span>
                        </a>

                    </div>
                </div>
            </div>

            <div id="patient-container" class="position-relative">
                <div id="patients-loading" class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-2">Loading Archived Patients...</p>
                </div>

                <div id="patients-content" class="d-none">
                    @include('pages.patients.partials.archived-partial')
                </div>
            </div>

        </div>
        
        @include('pages.patients.modals.unarchive')
        @include('pages.patients.modals.delete')
        
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
                    filterBtn.classList.remove('btn-outline-dark');
                    filterBtn.classList.add('btn-dark');
                }

                filterBtn.addEventListener('click', function() {
                    const url = new URL(window.location);
                    const isActive = url.searchParams.get('filter_by_account') === '1';

                    if (isActive) {
                        url.searchParams.delete('filter_by_account');
                    } else {
                        url.searchParams.set('filter_by_account', '1');
                    }

                    window.location.href = url.toString();
                });
            });
        </script>
    @endsection