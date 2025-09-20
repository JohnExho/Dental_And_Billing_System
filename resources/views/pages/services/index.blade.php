@extends('layout')
@section('title', 'Services | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal" data-bs-target="#add-service-modal">
                    <i class="bi bi-plus-circle"></i> Add Service
                </a>
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
        {{-- <div class="mt-3 px-3">
            {{ $services->links('vendor.pagination.bootstrap-5') }}
        </div> --}}
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.services.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('services-loading').classList.add('d-none');
                document.getElementById('services-content').classList.remove('d-none');
            });
        </script>
@endsection