<!DOCTYPE html>
<html>
<head>
<title>Forgot Password Email</title>
</head>
<body>
<h2>Hi {{$user['name']}}</h2>
<br/>
    You have just request to change password, Please click on the below link to reset your password
<br/>
<a href="{{url('api/password/reset', $user->remember_token)}}">Reset Password</a>
        
</body>
</html>
