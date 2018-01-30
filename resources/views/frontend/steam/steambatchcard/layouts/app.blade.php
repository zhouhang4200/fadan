<?php
$currentRouteName = Route::currentRouteName();
$currentOneLevelMenu = explode('.', Route::currentRouteName())[0];

?>
<!doctype html>
<html lang="zh-cmn-Hans">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta name="_token" content="{{ csrf_token() }}" >
    <title>千手 - @yield('title')</title>

    <link rel="stylesheet" href="{{ asset('frontend/card/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/card/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/card/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/card/css/chongzhi.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/card/css/financial.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/card/css/weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/layui/css/layui.css') }}">

    @yield('css')
</head>
<body class='relative' style='background:#f3f5fb'>
<div class="fixed header-wrap" style="z-index:10">
    <div class="header-left relative left" style="z-index:2;height:55px;">
        <img src="http://qsios.com/statics/client-v1/img/logo.png" alt="" class="left logo">
        <a href="{{url('steam/goods')}}"><span class="left">返回平台</span></a>
    </div>
    <div class="absolute" style="height: 100%;width: 100%;z-index:1;left: 0;">
        <div class="tab-wrap overflow"
             style="height:100%;margin:0 auto;width:100%;font-family: '微软雅黑';text-align: center;">
            <a class="tab relative inline-block @if($currentRouteName == 'frontend.steam.card.recharge') show @endif"
               href="{{url('steam/card/recharge')}}" id="get">账号列表</a>
            <a class="tab relative inline-block @if($currentRouteName == 'frontend.steam.card.list') show @endif" href="{{url('steam/card/list')}}" id="get">取号记录</a>
            <a class="tab relative inline-block @if($currentRouteName == 'frontend.steam.card.show') show @endif" href="{{url('steam/card/show')}}" id="get">赠送记录</a>
            <a class="tab relative inline-block @if($currentRouteName == 'frontend.steam.card.seal') show @endif" href="{{url('steam/card/seal')}}" id="get">封号记录</a>
            {{--<a class="tab relative inline-block @if($currentRouteName == 'frontend.card.zclist') show @endif" href="{{url('card/zclist')}}" id="get">Steam直充</a>--}}
            {{--<a class="tab relative inline-block @if($currentRouteName == 'frontend.card.game') show @endif" href="{{url('card/game')}}" id="get">游戏模板</a>--}}
        </div>
    </div>
</div>
@yield('content')

<script type="text/javascript" src="{{ asset('js/jquery-1.11.0.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/card/js/jquery.html5-fileupload.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/card/js/upload.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/card/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/layui/layui.js') }}" ></script>
<script type="text/javascript" src="{{ asset('vendor/laypage/laypage.js') }}"></script>

@yield('js')

</body>
</html>