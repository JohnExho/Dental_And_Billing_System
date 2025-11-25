<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Chomply</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-container {
            width: 100%;
            max-width: 1100px;
            z-index: 1;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        /* Left Panel */
        .login-left {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 3px solid #667eea;
        }

        .login-header h3 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #667eea;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .tagline {
            color: #888;
            font-size: 0.85rem;
            font-style: italic;
        }

        /* Form */
        .login-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper .icon {
            position: absolute;
            left: 15px;
            color: #888;
            font-size: 1.1rem;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-wrapper input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-wrapper .toggle {
            position: absolute;
            right: 15px;
            cursor: pointer;
            color: #888;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .input-wrapper .toggle:hover {
            color: #667eea;
        }

        .forgot {
            text-align: right;
            margin-bottom: 1.5rem;
        }

        .forgot a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Right Panel */
        .login-right {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .login-right::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .login-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            position: relative;
            z-index: 1;
        }

        /* Tablet styles */
        @media (max-width: 992px) {
            body {
                padding: 1.5rem;
            }

            .login-card {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .login-left {
                padding: 2.5rem 2rem;
            }

            .login-right {
                min-height: 300px;
                order: -1;
            }

            .logo {
                width: 70px;
                height: 70px;
            }

            .login-header h3 {
                font-size: 1.6rem;
            }
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
                align-items: flex-start;
                padding-top: 2rem;
            }

            .login-card {
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            .login-left {
                padding: 2rem 1.5rem;
            }

            .login-right {
                min-height: 250px;
            }

            .logo {
                width: 60px;
                height: 60px;
            }

            .login-header h3 {
                font-size: 1.4rem;
            }

            .subtitle {
                font-size: 0.95rem;
            }

            .tagline {
                font-size: 0.8rem;
            }

            .input-wrapper input {
                padding: 10px 15px 10px 40px;
                font-size: 0.95rem;
            }

            .btn-login {
                padding: 12px;
            }
        }

        /* Small mobile styles */
        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
            }

            .login-card {
                border-radius: 15px;
            }

            .login-left {
                padding: 1.5rem 1rem;
            }

            .login-header {
                margin-bottom: 1.5rem;
            }

            .logo {
                width: 50px;
                height: 50px;
            }

            .login-header h3 {
                font-size: 1.3rem;
            }

            .form-group {
                margin-bottom: 1.25rem;
            }

            .login-right {
                min-height: 200px;
            }
        }

        /* Landscape mobile */
        @media (max-width: 992px) and (orientation: landscape) {
            .login-right {
                min-height: 200px;
            }

            .login-card {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            
            <!-- Right Panel (Shows first on mobile) -->
            <div class="login-right">
                <img src="https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=800" alt="Dentist">
            </div>

            <!-- Left Panel -->
            <div class="login-left">
                <div class="login-header">
                    <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=200" alt="Logo" class="logo">
                    <h3>Dayao</h3>
                    <p class="subtitle">Dental Home</p>
                    <small class="tagline">A Happy Patient is our Ultimate Goal</small>
                </div>

                <form class="login-form" onsubmit="event.preventDefault(); alert('Login submitted!');">
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
                        <a href="#">Forgot Password?</a>
                    </div>

                    <!-- Button -->
                    <button type="submit" class="btn-login">Login</button>
                </form>
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
</body>
</html>