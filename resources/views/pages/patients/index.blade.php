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
    box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
}

/* Active */
.btn.btn-light:active {
    background: #d0d4d8 !important;
    transform: translateY(2px) scale(0.98) !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
}
</style>

@extends('layout')
@section('title', 'Patient | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        No of Patient # {{ $patientCount }}
                    </div>

                    <a href="#" class="btn btn-light btn-sm d-flex align-items-center gap-1 float-end"
                        data-bs-toggle="modal" data-bs-target="#add-patient-modal">
                        <i class="bi bi-plus-circle"></i> Add Patient
                    </a>
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
        <div class="mt-3 px-3">
            {{ $patients->links('vendor.pagination.bootstrap-5') }}
        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.patients.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('patients-loading').classList.add('d-none');
                document.getElementById('patients-content').classList.remove('d-none');
            });
        </script>
    @endsection
