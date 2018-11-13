<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    {{--<link rel="stylesheet" href="{{mix('/css/theme.css', 'channel')}}">--}}
</head>
<body>
<div id="app">
    <router-view></router-view>
</div>
<script src="{{mix('js/manifest.js', 'channel')}}"></script>
<script src="{{mix('js/vendor.js', 'channel')}}"></script>
<script src="{{mix('js/app.js', 'channel')}}"></script>
</body>
</html>