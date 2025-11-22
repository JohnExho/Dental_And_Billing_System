@extends('layout')
@section('title', 'Change Password | Chomply')
@section('content')

<style>

    /* Background */
    body {
        background-color: #1d2f4a;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
        margin: 0;
    }

    /* Card Container Similar to Memory Design */
    .reset-card {
        background: #fff;
        width: 490px;
        padding: 40px 50px;
        border-radius: 14px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        text-align: center;
    }

    /* Circular Icon Holder */
    .icon-circle {
        width: 70px;
        height: 70px;
        background: #2C91C5;
        border-radius: 50%;
        margin: 0 auto 15px auto;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 28px;
        color: #fff;
    }

    /* Title */
    .reset-card h2 {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 3px;
    }

    /* Subtitle */
    .reset-card p {
        font-size: 13px;
        color: #777;
        margin-bottom: 25px;
    }

    /* Label */
    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    /* Input Fields Style */
    .form-control {
        width: 100%;
        padding: 12px 14px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
        margin-top: 5px;
    }

    .form-control:focus {
        border-color: #4aa3df;
        box-shadow: 0 0 5px rgba(74,163,223,0.4);
        outline: none;
    }

    /* Confirm Button */
    .btn-primary {
        background: #248fce;
        padding: 12px;
        border-radius: 6px;
        width: 100%;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        border: none;
        cursor: pointer;
        margin-top: 5px;
    }

    .btn-primary:hover {
        background: #1f7eb6;
    }

    .invalid-feedback {
        font-size: 13px;
        color: red;
    }

</style>

<div class="reset-card">

    {{-- Icon --}}
    <div class="icon-circle">
        🔒
    </div>

    {{-- Title + subtitle --}}
    <h2>Set New Password</h2>
    <p>Enter your New Password</p>

    <form method="POST" action="{{ route('process-reset-password') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-group mb-3" style="text-align: left;">
            <label for="password">New Password</label>
            <input id="password"
                   type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-3" style="text-align: left;">
            <label for="password-confirm">Confirm New Password</label>
            <input id="password-confirm"
                   type="password"
                   name="password_confirmation"
                   class="form-control"
                   required>
        </div>

        <button type="submit" class="btn-primary">
            Confirm
        </button>

    </form>

</div>

@endsection
