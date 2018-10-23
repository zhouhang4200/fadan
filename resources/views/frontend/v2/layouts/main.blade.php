<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{mix('/v2/css/app.css', 'frontend')}}">
    <link rel="stylesheet" href="{{mix('/v2/css/theme.css', 'frontend')}}">
</head>
<body>
<div id="app">
    <layout game-api="s">
        @yield('content')
    </layout>
</div>
<script src="{{mix('/v2/js/manifest.js', 'frontend')}}"></script>
<script src="{{mix('/v2/js/vendor.js', 'frontend')}}"></script>
<script src="{{mix('/v2/js/app.js', 'frontend')}}"></script>
</body>
</html>