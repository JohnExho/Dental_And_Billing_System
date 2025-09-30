<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 480px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        .otp {
            font-size: 32px;
            font-weight: bold;
            margin: 20px 0;
            letter-spacing: 4px;
            background: rgba(34, 34, 185, 0.267) ;
        }
        .footer {
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your One-Time Password</h2>
        <p>Use the code below to verify your identity:</p>
        <div class="otp">{{ $otp }}</div>
        <p>This code will expire in 5 minutes.</p>
        <div class="footer">
            If you didn't request this code, you can safely ignore this message.
        </div>
    </div>
</body>
</html>
