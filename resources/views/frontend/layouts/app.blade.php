<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="_token" content="{{ csrf_token() }}" >
    <!--START 样式表-->
    @include('frontend.layouts.links')
    @yield('css')
    <!--END 样式表-->
</head>
<body>
<!--START 顶部菜单-->
@include('frontend.layouts.header')
<!--END 顶部菜单-->

<!--START 主体-->
<div class="main">
    <div class="wrapper">
        <div class="left">
            <div class="column-menu">
                @yield('submenu')
            </div>
        </div>

        <div class="right">
            <div class="content">
                <div class="path"><span id="main-title"></span></div>
                @yield('main')
            </div>
        </div>
    </div>
</div>
<!--END 主体-->

<!--START 底部-->
@include('frontend.layouts.footer')
<!--END 底部-->

<!--START 脚本-->
@include('frontend.layouts.scripts')
@yield('js')
<!--END 脚本-->
</body>
</html>