<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{mix('/css/app.css', 'frontend/v2')}}">
</head>
<body>
<div id="app">
    <app></app>
</div>
<script src="{{mix('js/manifest.js', 'frontend/v2')}}"></script>
<script src="{{mix('js/vendor.js', 'frontend/v2')}}"></script>
<script src="{{mix('js/app.js', 'frontend/v2')}}"></script>
</body>
</html>