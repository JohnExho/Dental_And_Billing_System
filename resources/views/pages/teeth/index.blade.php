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

/* Center pagination */
.pagination-wrapper nav {
    margin: 0 auto;
    margin-right: 705;
}

/* Hide Laravel built-in showing text */
.pagination-wrapper nav p {
    display: none !important;
}

/* Align custom showing text vertically */
.pagination-text {
    display: flex;
    align-items: center;
    height: 38px; /* same height as pagination buttons */
}
</style>

@extends('layout')
@section('title', 'Teeth | Chomply')
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
                        data-bs-target="#add-tooth-modal">
                        <i class="bi bi-plus-circle"></i> Add Tooth
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
                        data-bs-toggle="modal" data-bs-target="#add-tooth-modal">
                        <i class="bi bi-plus-circle"></i> Add Tooth
                    </a>
                @endif
            </div>
        </div>

        <div id="tooth-container" class="position-relative">
            <div id="teeth-loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Loading Teeth...</p>
            </div>

            <div id="teeth-content" class="d-none">
                @include('pages.teeth.partials.index-partial')
            </div>
        </div>

    </div>

    <!-- ⭐ UPDATED PAGINATION SECTION -->
    <div class="d-flex justify-content-between align-items-center mt-3 px-3">

        <!-- Left: Showing text -->
        <span class="text-muted pagination-text">
            Showing {{ $teeth->firstItem() }} to {{ $teeth->lastItem() }} of {{ $teeth->total() }} results
        </span>

        <!-- Center: Pagination -->
        <div class="pagination-wrapper flex-grow-1 d-flex justify-content-center">
            {{ $teeth->links('vendor.pagination.bootstrap-5') }}
        </div>

    </div>
    <!-- END UPDATED PAGINATION SECTION -->

    @include('pages.teeth.modals.add')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('teeth-loading').classList.add('d-none');
            document.getElementById('teeth-content').classList.remove('d-none');
        });
    </script>

@endsection
