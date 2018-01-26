<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="_token" content="{{ csrf_token() }}" >
    <link rel="stylesheet" href="/frontend/exchange/js/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/exchange/css/common.css">
    <link rel="stylesheet" href="/frontend/exchange/less/phone.css">
    <title>Steam游戏代购</title>
</head>

<body>
<div class="main">
    <div class="title">
        <img src="/frontend/exchange/images/phone/title_01.png" alt="">
    </div>
    <div class="key">
        <form class="layui-form" action="{{route('exchange.info')}}" method="post">
            <input type='hidden' name="_token" value="{{ csrf_token() }}">
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input style="text-align: center;" type="text" name="cdkey" align="right" lay-verify="cdkey" autocomplete="off" placeholder="请输入您的兑换码"
                           value="" class="layui-input"><br>
                    <button class="exchange" type="submit" lay-submit="" lay-filter="exchange">兑换</button>
                    <button class="fr query" type="submit" lay-submit="" lay-filter="query">查询</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="/frontend/client-v1/js/jquery-1.11.0.min.js"></script>
<script src="/frontend/exchange/js/layui/layui.js"></script>
<script>
    if (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.split(";")[1].replace(/[ ]/g, "") ==
        "MSIE8.0") {
        $(".layui-input").css({"padding":"0","height":"40px","text-indent":"10px","color":"#000"})
    }
    if (document.all && window.XMLHttpRequest && !document.querySelector) {
        $(".layui-input").css({"padding":"0","height":"40px","text-indent":"10px","color":"#000"})
        $(".title").css({"height":"230px","width":"485px"})
    }
</script>
<script>
    @if (session('msg'))

        layui.use(['form', 'layedit', 'laydate'], function () {
            var layer = layui.layer;
            layer.alert("{{session('msg')}}", {
                title: '提示',
                icon: 2
            })
        });

    @endif
</script>

<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});
    //设置页面主题高度为可视高度
    // $('.main').height($(window).height())
    layui.use(['form', 'layedit'], function () {
        var form = layui.form,
            layer = layui.layer,
            layedit = layui.layedit;


        //自定义验证规则
        form.verify({
            cdkey: [/[a-zA-Z0-9]{16}$/, '兑换码必须为16位字符']
        });
    });
</script>
</body>

</html>