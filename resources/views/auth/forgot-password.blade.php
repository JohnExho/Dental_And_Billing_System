@extends('layout')

@section('content')

    <style>
        body {
            background-color: #22345A;
            /* Navy blue background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: #fafaf0;
            /* Light cream background */
            border-radius: 12px;
            padding: 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            background-color: #ff6b6b;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 1rem auto;
        }

        .icon-circle i {
            font-size: 2rem;
            color: #000;
        }

        .btn-custom {
            background-color: #2c91c5;
            color: #fff;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #1b6d91;
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1rem;
            color: #555;
            text-decoration: none;
        }

        .back-link i {
            margin-right: 6px;
        }
    </style>

    <div class="card w-100 shadow">
        <!-- Icon -->
        <div class="icon-circle">
            <i class="bi bi-lock-fill"></i>
        </div>
        <!-- Title -->
        <h5 class="fw-bold">Forgot Your Password?</h5>
        <p class="text-muted">Enter your email</p>

        <!-- Form -->
        <form method="POST" action="{{ route('send-otp') }}">
            @csrf
            <div class="mb-3 text-start">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email">
            </div>
            <button type="submit" class="btn btn-custom w-100 py-2">Verify</button>
        </form>

        <!-- Back link -->
        <a href="{{ route('login')}}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            Return back to Login page
        </a>
    </div>
@endsection