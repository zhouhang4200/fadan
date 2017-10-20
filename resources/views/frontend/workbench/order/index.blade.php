<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>工作台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="_token" content="{{ csrf_token() }}" >
    <!--START 样式表-->
    @include('frontend.layouts.links')
    <style>
    html, body, .main, .creator, .orders-box {height: 100%;}
    .main {background-color: #fff; position: absolute; top: 0; width: 100%;}
    .h-60 {height: 60px;}
    .creator {float:left; width:290px; background-color: #52565a;}
    .orders-box {margin-left: 290px;}
    .orders {margin: 20px;}
    </style>
    <!--END 样式表-->
</head>
<body>
<!--START 顶部菜单-->
@include('frontend.layouts.header')
<!--END 顶部菜单-->

<!--START 主体-->
<div class="main">
    <div class="creator">
        <div class="h-60"></div>
        <div>
            下单
        </div>
    </div>

    <div class="orders-box">
        <div class="h-60"></div>
        <div class="orders">
            <table class="layui-table">
                <colgroup>
                    <col width="150">
                    <col>
                </colgroup>
                <thead>
                    <tr>
                        <th>流水号</th>
                        <th>相关单号</th>
                        <th>类型</th>
                        <th>金额</th>
                        <th>说明</th>
                        <th>时间</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<!--END 主体-->

<!--START 脚本-->
@include('frontend.layouts.scripts')
<script>
// js
</script>
<!--END 脚本-->
</body>
</html>