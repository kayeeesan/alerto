<!DOCTYPE html>
<html>
<head>
    <title>Contact Us Message</title>
</head>
<body>
    <h2>New Contact Us Message</h2>
    <p><strong>Name:</strong> {{ $data['name'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Phone:</strong> {{ $data['contact_number'] ?? 'N/A' }}</p>
    <p><strong>Message:</strong></p>
    <p>{{ $data['message'] }}</p>
</body>
</html>
