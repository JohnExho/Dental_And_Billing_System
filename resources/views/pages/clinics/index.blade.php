@extends('layout')
@section('title', 'Clinics | Chomply')
@section('content')
    <div class="container mt-4">
        {{-- List of clinics or whatever content you have --}}
        @include('pages.clinics.partials.index-partial')
    </div>

    {{-- Modal lives outside the container but still inside content --}}
    @include('pages.clinics.modals.add')
@endsection
