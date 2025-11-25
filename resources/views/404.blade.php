<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
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
            overflow: hidden;
            position: relative;
        }

        .stars {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s infinite;
        }

        @keyframes twinkle {
            0%, 100% {
                opacity: 0.3;
            }
            50% {
                opacity: 1;
            }
        }

        .container {
            text-align: center;
            color: white;
            z-index: 10;
            padding: 2rem;
            max-width: 600px;
        }

        .error-code {
            font-size: 150px;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: float 3s ease-in-out infinite;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .btn-home {
            display: inline-block;
            padding: 15px 40px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            background: #f8f9fa;
        }

        .tooth-fleet {
            display: inline-block;
            margin-bottom: 1rem;
            position: relative;
            width: 500px;
            height: 250px;
        }

        .tooth {
            position: absolute;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
            transform: rotate(-45deg);
        }

        .tooth-main {
            width: 110px;
            height: 110px;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            animation: float-main 3s ease-in-out infinite;
            z-index: 10;
        }

        .tooth-1 {
            width: 50px;
            height: 50px;
            left: 8%;
            top: 15%;
            animation: float-1 4s ease-in-out infinite;
            opacity: 0.75;
        }

        .tooth-2 {
            width: 50px;
            height: 50px;
            right: 8%;
            bottom: 15%;
            animation: float-2 3.5s ease-in-out infinite;
            opacity: 0.75;
        }

        .tooth-3 {
            width: 45px;
            height: 45px;
            left: 20%;
            top: 45%;
            animation: float-3 3.8s ease-in-out infinite;
            opacity: 0.7;
        }

        .tooth-4 {
            width: 45px;
            height: 45px;
            right: 20%;
            top: 35%;
            animation: float-4 4.2s ease-in-out infinite;
            opacity: 0.7;
        }

        .tooth-5 {
            width: 40px;
            height: 40px;
            left: 15%;
            bottom: 20%;
            animation: float-5 3.6s ease-in-out infinite;
            opacity: 0.65;
        }

        .tooth-6 {
            width: 40px;
            height: 40px;
            right: 15%;
            top: 10%;
            animation: float-6 4.4s ease-in-out infinite;
            opacity: 0.65;
        }

        .tooth-7 {
            width: 30px;
            height: 30px;
            left: 5%;
            bottom: 25%;
            animation: float-7 3.2s ease-in-out infinite;
            opacity: 0.5;
        }

        .tooth-8 {
            width: 30px;
            height: 30px;
            right: 5%;
            top: 25%;
            animation: float-8 3.9s ease-in-out infinite;
            opacity: 0.5;
        }

        @keyframes float-main {
            0%, 100% {
                transform: translate(-50%, -50%) rotate(-45deg) translateY(0px);
            }
            50% {
                transform: translate(-50%, -50%) rotate(-45deg) translateY(-15px);
            }
        }

        @keyframes float-1 {
            0%, 100% {
                transform: rotate(-45deg) translateY(0px);
            }
            50% {
                transform: rotate(-45deg) translateY(-10px);
            }
        }

        @keyframes float-2 {
            0%, 100% {
                transform: rotate(-45deg) translateY(0px);
            }
            50% {
                transform: rotate(-45deg) translateY(-12px);
            }
        }

        @keyframes float-3 {
            0%, 100% {
                transform: rotate(-45deg) translateY(0px);
            }
            50% {
                transform: rotate(-45deg) translateY(-11px);
            }
        }

        @keyframes float-4 {
            0%, 100% {
                transform: rotate(-45deg) translateY(0px);
            }
            50% {
                transform: rotate(-45deg) translateY(-13px);
            }
        }

        @keyframes float-5 {
            0%, 100% {
                transform: rotate(-45deg) translateY(0px);
            }
            50% {
                transform: rotate(-45deg) translateY(-9px);
            }
        }

        @keyframes float-6 {
            0%, 100% {
                transform: rotate(-45deg) translateY(0px);
            }
            50% {
                transform: rotate(-45deg) translateY(-14px);
            }
        }

        @keyframes float-7 {
            0%, 100% {
                transform: rotate(-45deg) translateY(0px);
            }
            50% {
                transform: rotate(-45deg) translateY(-8px);
            }
        }

        @keyframes float-8 {
            0%, 100% {
                transform: rotate(-45deg) translateY(0px);
            }
            50% {
                transform: rotate(-45deg) translateY(-7px);
            }
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 100px;
            }

            h1 {
                font-size: 2rem;
            }

            p {
                font-size: 1rem;
            }

            .tooth-fleet {
                width: 300px;
                height: 150px;
            }

            .tooth-main {
                width: 70px;
                height: 70px;
            }

            .tooth-1, .tooth-2 {
                width: 35px;
                height: 35px;
            }

            .tooth-3, .tooth-4, .tooth-5, .tooth-6 {
                width: 30px;
                height: 30px;
            }

            .tooth-7, .tooth-8 {
                width: 20px;
                height: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="stars" id="stars"></div>

    <div class="container">
        <div class="tooth-fleet">
            <!-- Main tooth -->
            <svg class="tooth tooth-main" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="white" stroke="#E0E0E0" stroke-width="0.5"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FFD700"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FFD700"/>
                <circle cx="10" cy="8" r="1.5" fill="#87CEEB" opacity="0.6"/>
                <line x1="10" y1="6.5" x2="10" y2="9.5" stroke="white" stroke-width="0.8" opacity="0.8"/>
                <line x1="8.5" y1="8" x2="11.5" y2="8" stroke="white" stroke-width="0.8" opacity="0.8"/>
            </svg>

            <!-- Supporting teeth -->
            <svg class="tooth tooth-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="rgba(255,255,255,0.9)" stroke="#E0E0E0" stroke-width="0.4"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FFA500"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FFA500"/>
                <circle cx="10" cy="8" r="1" fill="#87CEEB" opacity="0.5"/>
            </svg>

            <svg class="tooth tooth-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="rgba(255,255,255,0.9)" stroke="#E0E0E0" stroke-width="0.4"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FFA500"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FFA500"/>
                <circle cx="10" cy="8" r="1" fill="#87CEEB" opacity="0.5"/>
            </svg>

            <svg class="tooth tooth-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="rgba(255,255,255,0.85)" stroke="#E0E0E0" stroke-width="0.3"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FF8C00"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FF8C00"/>
            </svg>

            <svg class="tooth tooth-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="rgba(255,255,255,0.85)" stroke="#E0E0E0" stroke-width="0.3"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FF8C00"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FF8C00"/>
            </svg>

            <svg class="tooth tooth-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="rgba(255,255,255,0.8)"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FF7F00"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FF7F00"/>
            </svg>

            <svg class="tooth tooth-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="rgba(255,255,255,0.8)"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FF7F00"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FF7F00"/>
            </svg>

            <svg class="tooth tooth-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="rgba(255,255,255,0.7)"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FF6B00"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FF6B00"/>
            </svg>

            <svg class="tooth tooth-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C9 3 7 4 6 6C5 8 5 10 5 12C5 14 5 16 6 18C7 19 8 20 9 20C9.5 19.5 10 19 10.5 18.5C11 18 11.5 18 12 18C12.5 18 13 18 13.5 18.5C14 19 14.5 19.5 15 20C16 20 17 19 18 18C19 16 19 14 19 12C19 10 19 8 18 6C17 4 15 3 12 3Z" fill="rgba(255,255,255,0.7)"/>
                <path d="M8.5 18C7.5 19 7 19.5 6.5 20C7 20.5 7.5 21 8 21C8.5 20.5 9 20 9 19.5C9 19 8.5 18.5 8.5 18Z" fill="#FF6B00"/>
                <path d="M15.5 18C16.5 19 17 19.5 17.5 20C17 20.5 16.5 21 16 21C15.5 20.5 15 20 15 19.5C15 19 15.5 18.5 15.5 18Z" fill="#FF6B00"/>
            </svg>
        </div>

        <div class="error-code">404</div>
        <h1>Lost in the Dental Galaxy</h1>
        <p>Oops! This page has drifted away like a lost tooth in space. It might have been extracted, relocated, or never existed in this dental universe.</p>
        @if(Auth::guard('account')->check())
            @php $role = Auth::guard('account')->user()->role ?? null; @endphp

            @if($role === 'admin')
                <a id="btn-home" href="{{ route('admin.dashboard') }}" class="btn-home">Go to Admin Dashboard</a>
            @elseif($role === 'staff')
                <a id="btn-home" href="{{ route('staff.dashboard') }}" class="btn-home">Go to Staff Dashboard</a>
            @else
                <a id="btn-home" href="{{ url('/') }}" class="btn-home">Return to Home</a>
            @endif
        @else
            <a id="btn-home" href="{{ route('login') }}" class="btn-home">Return to Login</a>
        @endif

        <script>
            // Remove the return button on mobile devices (<=768px)
            (function() {
                var btn = document.getElementById('btn-home');
                if (!btn) return;
                if (window.innerWidth <= 768) {
                    btn.remove();
                }
            })();
        </script>
    </div>

    <script>
        // Create random stars
        const starsContainer = document.getElementById('stars');
        for (let i = 0; i < 100; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.animationDelay = Math.random() * 3 + 's';
            starsContainer.appendChild(star);
        }
    </script>
</body>

</html>