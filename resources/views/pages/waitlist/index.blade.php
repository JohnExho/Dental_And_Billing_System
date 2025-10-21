@extends('layout')
@section('title', 'Waitlist | Chomply')
@section('content')
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        No of Patient # {{ $patientCount }}
                    </div>
                    <a href="#" class="btn btn-light btn-sm d-flex align-items-center gap-1 float-end"
                        data-bs-toggle="modal" data-bs-target="#add-waitlist-modal">
                        <i class="bi bi-plus-circle"></i> Add Patient To Line
                    </a>
                </div>

            </div>
            <div id="waitlist-container" class="position-relative">
                <div id="waitlist-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading waitlist...</p>
                </div>

                <div id="waitlist-content" class="d-none">
                    @include('pages.waitlist.partials.index-partial')
                </div>
            </div>

        </div>
        <div class="mt-3 px-3">
            {{ $waitlist->links('vendor.pagination.bootstrap-5') }}
        </div>

        @include('pages.waitlist.modals.add')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('waitlist-loading').classList.add('d-none');
                document.getElementById('waitlist-content').classList.remove('d-none');
            });
        </script>
    @endsection
