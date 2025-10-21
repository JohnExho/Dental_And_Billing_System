@extends('layout')

@section('content')

<style>
    body {
        background: linear-gradient(135deg, #1b2a49, #3a6073);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .auth-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        padding: 2.5rem;
        width: 100%;
        text-align: center;
        animation: fadeInUp 0.6s ease;
    }

    .auth-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #2c91c5;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 1.5rem auto;
        box-shadow: 0 4px 12px rgba(44,145,197,0.4);
    }

    .auth-icon i {
        font-size: 2rem;
        color: #fff;
    }

    h5 {
        font-weight: 700;
        color: #4E4E4E;
    }

    p {
        color: #4E4E4E;
    }

    .form-control {
        border-radius: 10px;
        padding: 0.75rem 1rem;
    }

    .btn-custom {
        background: #2c91c5;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: #FFFEF2;

            transition: 
        background 0.4s ease-in-out,
        transform 0.4s ease-in-out,
        box-shadow 0.4s ease-in-out;
    }

    .btn-custom:hover {
        background: #1558a6;
        color: #FFFEF2;
        transform: translateY(-2px);   /* subtle lift */
        box-shadow: 0 6px 12px rgba(0,0,0,0.2); /* soft shadow */
    }

    .btn-custom:active {
        color: #FFFEF2;
        background: #0f3e73;
        transform: translateY(2px) scale(0.98); /* real press effect */
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        margin-top: 1.5rem;
        font-size: 0.9rem;
        color: #555;
        text-decoration: none;
        margin-left: -300px;

    }

    .back-link i {
        margin-right: 5px;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="auth-card">
    <!-- Icon -->
    <div class="auth-icon">
        <i class="bi bi-envelope-fill"></i>
    </div>

    <!-- Title -->
    <h5>Forgot Password?</h5>
    <p class="mb-4">Enter your registered email address to receive a reset code.</p>

    <!-- Form -->
    <form method="POST" action="{{ route('process-send-otp') }}">
        @csrf
        <div class="mb-3 text-start">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
        </div>
        <button type="submit" class="btn btn-custom w-100 py-2">Send Reset Link</button>
    </form>

    <!-- Back link -->
    <a href="{{ route('login')}}" class="back-link">
        <i class="bi bi-arrow-left"></i>
        Back to Login
    </a>
</div>
@endsection
