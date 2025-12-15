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

.btn.btn-light.btn-sm:hover i {
    transform: rotate(90deg);
}

/* Active */
.btn.btn-light:active {
    background: #d0d4d8 !important;
    transform: translateY(2px) scale(0.98) !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
}

.pagination {
    justify-content: center !important;
}
</style>

@extends('layout')
@section('title', 'Medicines | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                @php
                    $currentClinic = session('clinic_id') ? \App\Models\Clinic::find(session('clinic_id')) : null;
                @endphp
                <div class="d-flex justify-content-between align-items-center">
                    @if ($currentClinic)
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('clinics') }}"
                                class="badge rounded-pill bg-light text-dark border d-flex align-items-center px-3 py-2 shadow-sm text-decoration-none">
                                <i class="bi bi-hospital me-2 text-primary"></i>
                                <span class="fw-semibold">Clinic:</span>&nbsp;{{ $currentClinic->name }}
                            </a>
                        </div>
                        <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal"
                            data-bs-target="#add-medicine-modal">
                            <i class="bi bi-plus-circle"></i> Add Medicine
                        </a>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('clinics') }}"
                                class="badge rounded-pill bg-secondary d-flex align-items-center px-3 py-2 shadow-sm text-decoration-none">
                                <i class="fa-solid fa-tablets me-2 text-white"></i>
                                <span class="fw-semibold">Showing default Medicine prices</span>
                            </a>
                        </div>
                        <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal"
                            data-bs-target="#add-medicine-modal">
                            <i class="bi bi-plus-circle"></i> Add Medicine
                        </a>
                    @endif
                </div>
            </div>
            <div id="medicine-container" class="position-relative">
                <div id="medicines-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading medicines...</p>
                </div>

                <div id="medicines-content" class="d-none">
                    @include('pages.medicines.partials.index-partial')
                </div>
            </div>

        </div>
        <div class="mt-3 px-3 d-flex justify-content-center">
            {{ $medicines->links('vendor.pagination.bootstrap-5') }}
        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.medicines.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('medicines-loading').classList.add('d-none');
                document.getElementById('medicines-content').classList.remove('d-none');
            });
        </script>
    @endsection
