<!DOCTYPE html>
<html>
<head>
    <title>Reset Password Notification</title>
</head>
<body>
<h1>{{ $details['title'] }}</h1>
<p>{{ $details['body'] }}</p>
<p>
    To reset your password, click the link below:
</p>
<p>{{ $token }}</p>
</body>
</html>
