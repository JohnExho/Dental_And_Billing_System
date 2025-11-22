@extends('layout')
@section(
    'title',
    'Change Password | Chomply'
)
@section('content')
<style>
    body {
        background: linear-gradient(135deg, #1b2a49, #3a6073);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        padding: 2.5rem;
        width: 100%;
        text-align: center;
        animation: fadeInUp 0.6s ease;
    }

    .container h2 {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }

    .container p {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }

    .form-group {
        text-align: left;
        margin-bottom: 18px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 6px rgba(0,123,255,0.4);
    }

    .btn {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-primary {
        background-color: #2196f3;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #1976d2;
    }

    .invalid-feedback {
        color: red;
        font-size: 13px;
        margin-top: 5px;
        display: block;
    }
</style>

<div class="container">
    <h2>Set New Password</h2>
    <p>Enter your new password below</p>

    <form method="POST" action="{{ route('process-reset-password') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-group">
            <label for="password">New Password</label>
            <input id="password" type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   name="password" required>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">Confirm New Password</label>
            <input id="password-confirm" type="password" 
                   class="form-control" 
                   name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">
            Confirm
        </button>
    </form>
</div>
@endsection
