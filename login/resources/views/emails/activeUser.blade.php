<!DOCTYPE html>
<html>
<head>
<title>Activation Email</title>
</head>
<body>
<h2>Welcome to the site {{$user['name']}}</h2>
<br/>
Your registered email-id is {{$user['email']}} , Please click on the below link to verify your email account
<br/>
<a href="{{url('api/user/verify', $user->remember_token)}}">Verify Account</a>
</body>
</html>
