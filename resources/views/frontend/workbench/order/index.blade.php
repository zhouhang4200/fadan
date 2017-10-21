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

    .left-menu {
        width: 300px;
        background-color: #F5F5F5;
        border-left: solid 1px #D7D7D7;
        height: 100%;
        padding: 0 20px 10px 0 ;
        position: fixed;
        z-index: 99;
        top: 0;
        bottom: 0;
        left: -321px;
        box-shadow: 0 0 10px 0 rgba(100, 100, 100, 0.5);
        min-height: 650px;
    }
    .left-menu > .open-btn {
        color: #FFF;
        background-color: #1E9FFF;
        width: 16px;
        padding: 8px 6px 8px 7px;
        margin-top: -80px;
        border: solid 1px #2588e5;
        border-right: 0 none;
        position: absolute;
        z-index: 99;
        top: 50%;
        right: -30px;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 0 5px 0 rgba(204, 204, 204, 0.5);
        border-radius: 0 5px 5px 0;
    }
    .left-menu > .close-btn {
        color: #FFF;
        background-color: #1E9FFF;
        width: 16px;
        padding: 8px 6px 8px 7px;
        margin-top: -80px;
        border: solid 1px #2588e5;
        border-right: 0 none;
        position: absolute;
        z-index: 99;
        top: 50%;
        right: -30px;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 0 5px 0 rgba(204, 204, 204, 0.5);
        border-radius: 0 5px 5px 0;
    }
    </style>
    <!--END 样式表-->
</head>
<body>
<!--START 顶部菜单-->
@include('frontend.layouts.header')
<!--END 顶部菜单-->



<!--START 主体-->
<div class="main">

    <div class="left-menu" id="left-menu">
        <div class="open-btn block">
            打开
        </div>
        <div class="close-btn none">
            关闭
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
    $(".open-btn").click(function () {
        $("#left-menu").animate({left:"0"});
        $(".open-btn").addClass("layui-hide").removeClass("layui-show");
        $(".close-btn").addClass("layui-show").removeClass("layui-hide");
    });
    $(".close-btn").click(function () {
        $("#left-menu").animate({left:"-321"});
        $(".close-btn").addClass("layui-hide").removeClass("layui-show");
        $(".open-btn").addClass("layui-show").removeClass("layui-hide");
    });
</script>
<!--END 脚本-->
</body>
</html>