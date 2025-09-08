@extends('layout')
@section('title', 'Staffs | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal" data-bs-target="#add-staff-modal">
                    <i class="bi bi-plus-circle"></i> Add Staff
                </a>
            </div>
            <div id="staff-container" class="position-relative">
                <div id="staffs-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading staffs...</p>
                </div>

                <div id="staffs-content" class="d-none">
                    @include('pages.staffs.partials.index-partial')
                </div>
            </div>

        </div>
        <div class="mt-3 px-3">
            {{ $staffs->links('vendor.pagination.bootstrap-5') }}
        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.staffs.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('staffs-loading').classList.add('d-none');
                document.getElementById('staffs-content').classList.remove('d-none');
            });
        </script>
@endsection