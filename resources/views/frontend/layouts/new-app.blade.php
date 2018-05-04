<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="_token" content="{{ csrf_token() }}" >
    <!--START 样式表-->
    <link rel="stylesheet" href="/vendor/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/css/style.css">
    <link rel="stylesheet" href="/frontend/css/animate.min.css">
    <link rel="stylesheet" href="/frontend/css/layui-rewrit.css">
    <link rel="stylesheet" href="/frontend/css/iconfont.css">
    @yield('css')
    <!--END 样式表-->
    <script src="/vendor/layui/layui.js"></script>
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/frontend/js/helper.js"></script>
    <script src="/js/encrypt.js"></script>
    <script src="//cdn.bootcss.com/socket.io/1.3.7/socket.io.min.js"></script>
    <script>
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});
        var socket = io('http://js.qsios.com:90');
        layui.use(['form', 'layedit', 'laydate', 'element'], function(){
            var form = layui.form, layer = layui.layer, element = layui.element;

            //监听导航点击
            element.on('nav(demo)', function(elem){
                var current = elem.text();

                if (current == '注销登录') {
                    layer.confirm('确定退出吗?', {icon: 3, title:'提示'}, function(index){
                        $.post('/logout', {}, function(str){
                            window.location.href='/login';
                        });
                        layer.close(index);
                    });
                }
                if (current == '在线') {
                    setStatus(1);
                    $('.current-status').html('在线<span class="layui-nav-more"></span>');
                }
                if (current == '挂起') {
                    setStatus(2);
                    $('.current-status').html('挂起<span class="layui-nav-more"></span>');
                }
                // 设置账号状态
                function setStatus(status) {
                    $.post('{{ route('frontend.workbench.set-status') }}', {status:status}, function () {

                    }, 'json');
                }
            });
        });
    </script>
</head>
<body>
<!--START 顶部菜单-->
@include('frontend.layouts.header')
<!--END 顶部菜单-->

<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-sm1">
            <div class="grid-demo grid-demo-bg1">
                @yield('submenu')
            </div>
        </div>
        <div class="layui-col-sm11">
            <div class="grid-demo">
                <div class="path"><span>@yield('title')</span></div>
                @yield('main')
            </div>
        </div>
    </div>
</div>

<!--START 脚本-->
@yield('js')
<!--END 脚本-->
</body>
</html>

