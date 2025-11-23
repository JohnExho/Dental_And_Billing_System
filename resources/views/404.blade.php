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

            0%,
            100% {
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

            0%,
            100% {
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

        .rocket-container {
            display: inline-block;
            margin-bottom: 1rem;
            position: relative;
            width: 200px;
            height: 120px;
        }

        .rocket-svg {
            position: absolute;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
        }

        .rocket-main {
            width: 100px;
            height: 100px;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            animation: float-main 3s ease-in-out infinite;
        }

        .rocket-small-1 {
            width: 50px;
            height: 50px;
            left: 10%;
            top: 20%;
            animation: float-small-1 4s ease-in-out infinite;
            opacity: 0.8;
        }

        .rocket-small-2 {
            width: 50px;
            height: 50px;
            right: 10%;
            top: 30%;
            animation: float-small-2 3.5s ease-in-out infinite;
            opacity: 0.8;
        }

        @keyframes float-main {
            0%, 100% {
                transform: translate(-50%, -50%) translateY(0px) rotate(0deg);
            }
            50% {
                transform: translate(-50%, -50%) translateY(-15px) rotate(5deg);
            }
        }

        @keyframes float-small-1 {
            0%, 100% {
                transform: translateY(0px) rotate(-10deg);
            }
            50% {
                transform: translateY(-10px) rotate(5deg);
            }
        }

        @keyframes float-small-2 {
            0%, 100% {
                transform: translateY(0px) rotate(10deg);
            }
            50% {
                transform: translateY(-12px) rotate(-5deg);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .stars-trail {
            position: absolute;
            width: 3px;
            height: 3px;
            background: white;
            border-radius: 50%;
            opacity: 0;
            animation: trail 2s ease-out infinite;
        }

        @keyframes trail {
            0% {
                opacity: 0.8;
                transform: translateX(0);
            }
            100% {
                opacity: 0;
                transform: translateX(-30px);
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

            .rocket-svg {
                width: 60px;
                height: 60px;
            }

            .rocket-main {
                width: 70px;
                height: 70px;
            }

            .rocket-small-1,
            .rocket-small-2 {
                width: 35px;
                height: 35px;
            }

            .rocket-container {
                height: 100px;
            }
        }
    </style>
</head>

<body>
    <div class="stars" id="stars"></div>

    <div class="container">
        <div class="rocket-container">
            <!-- Main large rocket -->
            <svg class="rocket-svg rocket-main" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.5 14C8.67157 14 8 13.3284 8 12.5C8 11.6716 8.67157 11 9.5 11C10.3284 11 11 11.6716 11 12.5C11 13.3284 10.3284 14 9.5 14Z" fill="white"/>
                <path d="M12 2C12 2 5 3.5 5 11C5 11 3.5 12.5 3 14C3 14 4 15 6 15C6 15 6 18 6 20C6 20 7 21 8 21C8 21 8.5 20.5 9 19C10.5 19 11 20.5 12 22C13 20.5 13.5 19 15 19C15.5 20.5 16 21 16 21C17 21 18 20 18 20C18 18 18 15 18 15C20 15 21 14 21 14C20.5 12.5 19 11 19 11C19 3.5 12 2 12 2Z" fill="white"/>
                <path d="M8 17C6.5 17 5.5 16.5 5.5 16.5C5.5 16.5 6 16 6.5 15.5C7 15 7.5 15 7.5 15C7.5 15 7.5 16.5 8 17Z" fill="#FFD700"/>
                <path d="M16 17C17.5 17 18.5 16.5 18.5 16.5C18.5 16.5 18 16 17.5 15.5C17 15 16.5 15 16.5 15C16.5 15 16.5 16.5 16 17Z" fill="#FFD700"/>
                <circle cx="9.5" cy="12.5" r="1" fill="#667eea"/>
                <path d="M12 5C12 5 13.5 6 13.5 8.5C13.5 11 12 12 12 12C12 12 10.5 11 10.5 8.5C10.5 6 12 5 12 5Z" fill="#FFD700" opacity="0.7"/>
            </svg>

            <!-- Small rocket 1 (left) -->
            <svg class="rocket-svg rocket-small-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.5 14C8.67157 14 8 13.3284 8 12.5C8 11.6716 8.67157 11 9.5 11C10.3284 11 11 11.6716 11 12.5C11 13.3284 10.3284 14 9.5 14Z" fill="rgba(255,255,255,0.9)"/>
                <path d="M12 2C12 2 5 3.5 5 11C5 11 3.5 12.5 3 14C3 14 4 15 6 15C6 15 6 18 6 20C6 20 7 21 8 21C8 21 8.5 20.5 9 19C10.5 19 11 20.5 12 22C13 20.5 13.5 19 15 19C15.5 20.5 16 21 16 21C17 21 18 20 18 20C18 18 18 15 18 15C20 15 21 14 21 14C20.5 12.5 19 11 19 11C19 3.5 12 2 12 2Z" fill="rgba(255,255,255,0.9)"/>
                <path d="M8 17C6.5 17 5.5 16.5 5.5 16.5C5.5 16.5 6 16 6.5 15.5C7 15 7.5 15 7.5 15C7.5 15 7.5 16.5 8 17Z" fill="#FFA500"/>
                <path d="M16 17C17.5 17 18.5 16.5 18.5 16.5C18.5 16.5 18 16 17.5 15.5C17 15 16.5 15 16.5 15C16.5 15 16.5 16.5 16 17Z" fill="#FFA500"/>
                <circle cx="9.5" cy="12.5" r="1" fill="#764ba2"/>
            </svg>

            <!-- Small rocket 2 (right) -->
            <svg class="rocket-svg rocket-small-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.5 14C8.67157 14 8 13.3284 8 12.5C8 11.6716 8.67157 11 9.5 11C10.3284 11 11 11.6716 11 12.5C11 13.3284 10.3284 14 9.5 14Z" fill="rgba(255,255,255,0.9)"/>
                <path d="M12 2C12 2 5 3.5 5 11C5 11 3.5 12.5 3 14C3 14 4 15 6 15C6 15 6 18 6 20C6 20 7 21 8 21C8 21 8.5 20.5 9 19C10.5 19 11 20.5 12 22C13 20.5 13.5 19 15 19C15.5 20.5 16 21 16 21C17 21 18 20 18 20C18 18 18 15 18 15C20 15 21 14 21 14C20.5 12.5 19 11 19 11C19 3.5 12 2 12 2Z" fill="rgba(255,255,255,0.9)"/>
                <path d="M8 17C6.5 17 5.5 16.5 5.5 16.5C5.5 16.5 6 16 6.5 15.5C7 15 7.5 15 7.5 15C7.5 15 7.5 16.5 8 17Z" fill="#FFA500"/>
                <path d="M16 17C17.5 17 18.5 16.5 18.5 16.5C18.5 16.5 18 16 17.5 15.5C17 15 16.5 15 16.5 15C16.5 15 16.5 16.5 16 17Z" fill="#FFA500"/>
                <circle cx="9.5" cy="12.5" r="1" fill="#764ba2"/>
            </svg>
        </div>
        <div class="error-code">404</div>
        <h1>Lost in Space</h1>
        <p>Oops! The page you're looking for has drifted into the cosmic void. It might have been moved, deleted, or
            never existed in this universe.</p>
        <a href="#" class="btn-home">Return to Home</a>
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