<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>千手 · 订单集市  @yield('title')</title>
    <link type="image/x-icon" href="/favicon.ico" rel="shortcut icon"/>
    <link rel="stylesheet" type="text/css" href="/backend/css/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="/backend/css/libs/font-awesome.css"/>
    <link rel="stylesheet" type="text/css" href="/backend/css/libs/nanoscroller.css"/>
    <link rel="stylesheet" type="text/css" href="/backend/css/compiled/layout.css"/>
    <link rel="stylesheet" type="text/css" href="/backend/css/compiled/elements.css?v1"/>
    <link rel="stylesheet" type="text/css" href="/backend/css/libs/dropzone.css">
    <link rel="stylesheet" type="text/css" href="/backend/css/libs/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="/backend/css/libs/datepicker.css">
    <link rel="stylesheet" type="text/css" href="/backend/css/compiled/custom.css">
    <link rel="stylesheet" type="text/css" href="/vendor/layui/css/layui.css">
    <link rel="stylesheet" type="text/css" href="/backend/css/globale.css">
    @yield('css')
    <!--[if lt IE 9]>
    <script src="/backend/js/html5shiv.js"></script>
    <script src="/backend/js/respond.min.js"></script>
    <![endif]-->
    <script src="/backend/js/demo-rtl.js"></script>
</head>
<body class="pace-done theme-whbl">
<div class="">
    <header class="navbar" id="header-navbar">
        <div class="container">
            <h2 class="logo">千手 · 订单集市</h2>
            <div class="clearfix">
                <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="fa fa-bars"></span>
                </button>
                <div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
                    <ul class="nav navbar-nav pull-left">
                        <li>
                            <a class="btn" id="make-small-nav">
                                <i class="fa fa-bars"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="nav-no-collapse pull-right" id="header-nav">
                    <ul class="nav navbar-nav pull-right">

                        <li class="hidden-xs">
                            <a class="btn">
                                <i class="fa fa-cog"></i>
                            </a>
                        </li>
                        <li class="dropdown profile-dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="/img/samples/scarlet-159.png" alt=""/>
                                <span class="hidden-xs">管理员</span> <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><i class="fa fa-cog"></i>修改密码</a></li>
                                <li>
                                    <a href="javascript:void(0)" onclick="logout()">
                                        <i class="fa fa-power-off"></i> 注销登录
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="hidden-xxs">
                            <a class="btn">
                                <i class="fa fa-power-off"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div id="page-wrapper" class="container">
        <div class="row">
            @include('backend.layouts.partials.menu')
            <div id="content-wrapper">
                @yield('breadcrumb')
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="/backend/js/jquery.js"></script>
<script src="/backend/js/bootstrap.js"></script>
<script src="/backend/js/jquery.nanoscroller.min.js"></script>
<script src="/backend/js/skin.js"></script>
<script src="/backend/js/bootstrap-datepicker.js"></script>
<script src="/backend/js/scripts.js"></script>
<script src="/backend/js/pace.min.js"></script>
<script src="/backend/js/helper.js"></script>
<script src="/vendor/layui/layui.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function logout() {
        layui.use(['form', 'layedit', 'laydate',], function(){
            var form = layui.form
                    ,layer = layui.layer;
            layer.confirm('确定退出吗?', {icon: 3, title:'提示'}, function(index){
                $.post('/admin/logout', {}, function(str){
                    window.location.href='/admin/login';
                });
                layer.close(index);
            });

        });
    }
</script>
@yield('js')
</body>
</html>