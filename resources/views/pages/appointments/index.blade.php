@extends('layout')
@section('title', 'Appointment | Chomply')
@section('styles')
<style>
    table {
        table-layout: fixed;
        width: 100%;
    }
    td {
        height: 100px;
        vertical-align: top;
        padding: 8px;
        position: relative;
    }
    td div.badge {
        font-size: 0.75rem;
        white-space: normal;
        line-height: 1.1rem;
    }
</style>
@endsection
@section('content')


<div class="container py-5">
    <div class="card shadow border-0 rounded-4">

        @include('pages.appointments.partials.index-partial')
        </div>
    </div>
</div>
@endsection