<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <script src="/bower_components/jquery/dist/jquery.js"></script>
    <script src="/bower_components/bootstrap/js/bootstrap.js"></script>
    <link rel="stylesheet" href="/bower_components/bootstrap/css/bootstrap.css" type="text/css">
</head>
<body>

<h1>Register</h1>
<!--﻿
<form name="form" method="post" action="">
    <div>Username</div>
    <div><input type="text" name="username" size=50></div>
    <br>

    <div>Password</div>
    <div><input type="password" name="password" size=50></div>
    <br>

    <div>Repeat password:</div>
    <div><input type="password" name="password" size=50></div>
    <br>
</form>
-->

<!-- resources/views/auth/register.blade.php -->

<form method="POST" action="/auth/register">
    {!! csrf_field() !!}

    <div class="col-md-6">
        Name
        <input type="text" name="name" value="{{ old('name') }}">
    </div>

    <div class="col-md-6">

        Email
        <input type="email" name="email" value="{{ old('email') }}">
    </div>

    <div class="col-md-6">

        Password
        <input type="password" name="password">
    </div>

    <div class="col-md-6">
        Confirm Password
        <input type="password" name="password_confirmation">
    </div>

    <div class="col-md-6">

        <button type="submit">Register</button>
    </div>
</form>
</body>
</html>