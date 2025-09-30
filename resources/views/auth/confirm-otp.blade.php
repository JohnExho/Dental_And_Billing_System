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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            text-align: center;
            animation: fadeInUp 0.6s ease;
        }

        .auth-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #28a745;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .auth-icon i {
            font-size: 2rem;
            color: #fff;
        }

        h5 {
            font-weight: 700;
            color: #22345A;
        }

        p {
            color: #6c757d;
        }

        .otp-input {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 1.5rem 0;
        }

        .otp-input input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .otp-input input:focus {
            border-color: #2c91c5;
            box-shadow: 0 0 0 2px rgba(44, 145, 197, 0.2);
            outline: none;
        }

        .btn-custom {
            background: #2c91c5;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #fff;
        }

        .btn-custom:hover {
            background: #1b6d91;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: #f8f9fa;
            border: 1px solid #2c91c5;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #2c91c5;
        }

        .btn-outline:hover {
            background: #e2e6ea;
        }

        .btn-row {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .btn-row form {
            flex: 1;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #555;
            text-decoration: none;
        }

        .back-link i {
            margin-right: 6px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="auth-card">
        <!-- Icon -->
        <div class="auth-icon">
            <i class="bi bi-shield-lock-fill"></i>
        </div>

        <!-- Title -->
        <h5>Enter OTP Code</h5>
        <p class="mb-4">We’ve sent a verification code to your email. Enter it below to continue.</p>

        <!-- OTP Inputs -->
        <div class="otp-input">
            <input type="text" maxlength="1" name="otp[]" required form="verifyForm">
            <input type="text" maxlength="1" name="otp[]" required form="verifyForm">
            <input type="text" maxlength="1" name="otp[]" required form="verifyForm">
            <input type="text" maxlength="1" name="otp[]" required form="verifyForm">
            <input type="text" maxlength="1" name="otp[]" required form="verifyForm">
            <input type="text" maxlength="1" name="otp[]" required form="verifyForm">
        </div>
        @php
            $remaining = session('cooldown_remaining', 0);
        @endphp

        <!-- Buttons Row -->
        <div class="btn-row">
            <!-- Resend OTP -->
            <form method="POST" action="{{ route('process-resend-otp') }}" id="resendForm">
                @csrf
                <button id="resendBtn" type="submit" class="btn btn-outline w-100 py-2"
                    {{ $remaining > 0 ? 'disabled' : '' }}>
                    {{ $remaining > 0 ? "Resend ({$remaining}s)" : 'Resend' }}
                </button>

            </form>


            <!-- Verify OTP -->
            <form method="POST" action="{{ route('process-verify-otp') }}" id="verifyForm">
                @csrf
                <button type="submit" class="btn btn-custom w-100 py-2">Verify OTP</button>
            </form>
        </div>

        <!-- Back link -->
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            Back to Login
        </a>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let remaining = {{ $remaining }};
            const resendBtn = document.getElementById("resendBtn");

            if (remaining > 0) {
                const timer = setInterval(() => {
                    remaining--;
                    resendBtn.textContent = `Resend (${remaining}s)`;

                    if (remaining <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        resendBtn.textContent = "Resend";
                    }
                }, 1000);
            }
        });
    </script>
@endsection
