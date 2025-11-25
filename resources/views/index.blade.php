@extends("layout")

@section('title', 'Login | Chomply')

@section("content")


<head>
    <link rel="stylesheet" href="{{ 'resources/css/index.blade.css' }}">
</head>

@php
        session()->forget(['successes', 'clinic_id', 'qr_access']);
        
@endphp

<div class="login-container">
    <div class="login-card">
        
        <!-- Left Panel -->
        <div class="login-left">
            <div class="login-header">
                <img src="{{ asset('public/images/dayao.jpg') }}" alt="Logo" class="logo">
                <h3>Dayao</h3>
                <p class="subtitle">Dental Home</p>
                <small class="tagline">A Happy Patient is our Ultimate Goal</small>
            </div>

            <form method="POST" action="{{ route('process-login') }}" class="login-form">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <span class="icon"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="icon"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <span class="toggle" onclick="togglePassword()">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <!-- Forgot -->
                <div class="forgot">
                    <a href="{{ route('forgot-password') }}">Forgot Password?</a>
                </div>

                <!-- Button -->
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>

        <!-- Right Panel -->
        <div class="login-right">
            <img src="{{ asset('public/images/teethpicture.jpg') }}" alt="Dentist">
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const password = document.getElementById("password");
        const toggleIcon = document.getElementById("toggleIcon");
        if (password.type === "password") {
            password.type = "text";
            toggleIcon.classList.remove("bi-eye-slash");
            toggleIcon.classList.add("bi-eye");
        } else {
            password.type = "password";
            toggleIcon.classList.remove("bi-eye");
            toggleIcon.classList.add("bi-eye-slash");
        }
    }
</script>

@endsection
