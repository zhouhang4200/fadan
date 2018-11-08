<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="_token" content="{{ csrf_token() }}" >
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="代练发布">
    <link rel="stylesheet" href="/mobile/lib/css/weui.min.css">
    <link rel="stylesheet" href="/mobile/lib/css/jquery-weui.css">
    <link rel="stylesheet" href="/mobile/lib/css/font.css">
    <link rel="stylesheet" href="/mobile/lib/css/reset.css">
    <link rel="stylesheet" href="/mobile/lib/css/common.css">
    @yield('css')
</head>

<body ontouchstart>
<!-- header -->
@yield('header')
<!-- header -->
<!-- main -->
<div class="main">
    @yield('content')
</div>
<script src="/mobile/lib/js/jquery-2.1.4.js"></script>
<script src="/mobile/lib/js/fastclick.js"></script>
<script src="/mobile/lib/js/jquery-weui.js"></script>
@yield('js')
</body>

</html>