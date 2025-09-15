@extends('layout')
@section('title', 'Associates | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal" data-bs-target="#add-associate-modal">
                    <i class="bi bi-plus-circle"></i> Add associate
                </a>
            </div>
            <div id="associate-container" class="position-relative">
                <div id="associates-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading associates...</p>
                </div>

                <div id="associates-content" class="d-none">
                    @include('pages.associates.partials.index-partial')
                </div>
            </div>

        </div>
        <div class="mt-3 px-3">
            {{-- {{ $associates->links('vendor.pagination.bootstrap-5') }} --}}
        </div>
        {{-- Modal lives outside the container but still inside content --}}
        @include('pages.associates.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('associates-loading').classList.add('d-none');
                document.getElementById('associates-content').classList.remove('d-none');
            });
        </script>
@endsection