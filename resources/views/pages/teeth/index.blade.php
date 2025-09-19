@extends('layout')
@section('title', 'Teeth | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal" data-bs-target="#add-tooth-modal">
                    <i class="bi bi-plus-circle"></i> Add Tooth
                </a>
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
        <div class="mt-3 px-3">
            {{ $teeth->links('vendor.pagination.bootstrap-5') }}
        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.teeth.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('teeth-loading').classList.add('d-none');
                document.getElementById('teeth-content').classList.remove('d-none');
            });
        </script>
@endsection