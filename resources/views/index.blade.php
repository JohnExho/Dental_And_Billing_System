@extends("layout")

@section('title', 'Login | Chomply')

@section("content")

<div class="d-flex justify-content-center align-items-center min-vh-100" style="background-color:#0d2244;">
    <div class="card login-card shadow-lg" style="border-radius:15px; overflow:hidden; max-width:900px; width:100%; background:#0d2244; color:#fff;">
        <div class="row g-0">
            <!-- Left Side -->
            <div class="col-md-6 p-5 d-flex flex-column justify-content-center">
                <div class="text-center mb-3">
                    <img src="https://placehold.co/400x400?text=placeholder" alt="Logo" class="mb-3 border-round rounded-3" style="width:80px;">
                    <h3 class="fw-bold">Dayao</h3>
                    <p class="text-light">Dental Home</p>
                    <small class="text-light">A Happy Patient is our Ultimate Goal</small>
                </div>

                <form method="POST" action="{{ route('process-login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required autofocus>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                            <span class="input-group-text password-toggle" onclick="togglePassword()" style="cursor:pointer;">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <a href="{{ route('forgot-password') }}" class="small text-light">Forgot Password</a>
                    </div>

                    <button type="submit" class="btn w-100 text-white" style="background-color:#1a73e8; border-radius:8px;">Login</button>
                </form>
            </div>

            <!-- Right Side -->
            <div class="col-md-6 d-none d-md-block">
                <img src="https://placehold.co/400x400?text=placeholder" alt="Dentist" class="w-100 h-100" style="object-fit:cover;">
            </div>
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
