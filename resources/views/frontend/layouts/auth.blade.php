<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>千手平台 @yield('title')</title>
    <meta name="_token" content="{{ csrf_token() }}" >
    <link rel="stylesheet" href="/vendor/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/css/layui-rewrit.css">
    <link rel="stylesheet" href="/frontend/css/login.css">
    @yield('css')
</head>
<body>
@yield('content')
</body>
<script src="/vendor/layui/layui.js"></script>
<script src="/js/encrypt.js"></script>
@yield('js')
</html>