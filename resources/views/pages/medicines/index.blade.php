@extends('layout')
@section('title', 'Medicines | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal" data-bs-target="#add-medicine-modal">
                    <i class="bi bi-plus-circle"></i> Add Medicine
                </a>
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
        <div class="mt-3 px-3">
            {{ $medicines->links('vendor.pagination.bootstrap-5') }}
        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.medicines.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('medicines-loading').classList.add('d-none');
                document.getElementById('medicines-content').classList.remove('d-none');
            });
        </script>
@endsection