@extends('layout')
@section('title', 'laboratories | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal" data-bs-target="#add-laboratory-modal">
                    <i class="bi bi-plus-circle"></i> Add laboratory
                </a>
            </div>
            <div id="laboratory-container" class="position-relative">
                <div id="laboratories-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading laboratories...</p>
                </div>

                <div id="laboratories-content" class="d-none">
                    @include('pages.laboratories.partials.index-partial')
                </div>
            </div>

        </div>
        <div class="mt-3 px-3">
            {{-- {{ $laboratories->links('vendor.pagination.bootstrap-5') }} --}}
        </div>
        {{-- Modal lives outside the container but still inside content --}}
        {{-- @include('pages.laboratories.modals.add') --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('laboratories-loading').classList.add('d-none');
                document.getElementById('laboratories-content').classList.remove('d-none');
            });
        </script>
@endsection