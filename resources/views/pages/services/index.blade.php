<style>
.btn.btn-light.btn-sm {
    transition:
        background 0.4s ease-in-out,
        transform 0.4s ease-in-out,
        box-shadow 0.4s ease-in-out;
}

.btn.btn-light.btn-sm i {
    transition: transform 0.3s ease-in-out;
}

.btn.btn-light.btn-sm:hover i {
    transform: rotate(90deg);
}

.btn.btn-light.btn-sm:hover {
    background: #e2e6ea;
    color: #000;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
</style>

@extends('layout')
@section('title', 'Services | Chomply')
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
                        <a href="#" class="btn btn-light btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal"
                            data-bs-target="#add-service-modal">
                            <i class="bi bi-plus-circle"></i> Add Service
                        </a>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('clinics') }}"
                                class="badge rounded-pill bg-secondary d-flex align-items-center px-3 py-2 shadow-sm text-decoration-none">
                                <i class="fa-solid fa-tooth me-2 text-white"></i>
                                <span class="fw-semibold">Showing default tooth prices</span>
                            </a>
                        </div>
                        <a href="#" class="btn btn-light btn-sm d-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#add-service-modal">
                            <i class="bi bi-plus-circle"></i> Add Service
                        </a>
                    @endif
                </div>
            </div>
            <div id="service-container" class="position-relative">
                <div id="services-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading services...</p>
                </div>

                <div id="services-content" class="d-none">
                    @include('pages.services.partials.index-partial')
                </div>
            </div>


        </div>
        <div class="mt-3 px-3">
            {{ $services->links('vendor.pagination.bootstrap-5') }}
        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.services.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('services-loading').classList.add('d-none');
                document.getElementById('services-content').classList.remove('d-none');
            });
        </script>
    @endsection
