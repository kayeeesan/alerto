<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <p>Dear {{ $username }},</p>
    <p>We have received a request to reset your password. Please use the following temporary password to log in:</p>
    <p><strong>{{ $password }}</strong></p>
    <p>Once logged in, you can update your password.</p>
    <p>Click <a href="{{ $loginUrl }}">here</a> to go to the login page.</p>
</body>
</html>
