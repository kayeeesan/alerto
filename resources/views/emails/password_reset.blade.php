<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Dear {{ $username }},</p>

        <p>We received a request to reset your password. Please use the temporary password below to log in and set a new password at your earliest convenience:</p>

        <p><strong>{{ $password }}</strong></p>

        <p class="footer">
            If you did not request this password reset, please disregard this email or contact our support team for assistance.
        </p>
    </div>
</body>
</html>
