<?php
$userPermissions = Auth::user()->getUserPermissions()->pluck('name')->toArray();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="_token" content="{{ csrf_token() }}" >
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/frontend/v1/lib/js/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/frontend/v1/lib/css/admin.css" media="all">
    <link rel="stylesheet" href="/frontend/v1/lib/css/new.css">
    <link id="layuicss-layer" rel="stylesheet" href="/frontend/v1/lib/js/layui/css/modules/layer/default/layer.css" media="all">
    <style>
        .layui-layout-admin .layui-body {
            top: 50px;
        }

        .layui-layout-admin .layui-footer {
            height: 52px;
        }

        .footer {
            height: 72px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .main {
            padding: 20px;
        }

        .layui-footer {
            z-index: 999;
        }

        .layui-card-header {
            height: auto;
        }

        .iconfont {
            position: absolute;
            top: 50%;
            left: 20px;
            margin-top: -19px;
        }

        .layui-card .layui-tab {
            margin: 10px 0;
        }
        .layui-form-item {
            margin-bottom: 12px;
        }
        .layui-tab-title li{
            min-width: 50px;
        }
        .qsdate{
            display: inline-block;
            width: 44%;
        }
        .layui-card-header{
            padding: 15px;
        }
    </style>
    @yield('css')
</head>

<body class="layui-layout-body">

<div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" layadmin-event="refresh" title="刷新">
                        <i class="layui-icon layui-icon-refresh-3"></i>
                    </a>
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

                <li class="layui-nav-item" lay-unselect>
                    <a lay-href="app/message/index.html" layadmin-event="message" lay-text="消息中心">
                        <i class="layui-icon layui-icon-notice"></i>

                        <!-- 如果有新消息，则显示小圆点 -->
                        <span class="layui-badge-dot"></span>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect style="margin-right: 30px;">
                    <a href="javascript:;">
                        <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
                        <cite>贤心</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a lay-href="set/user/info.html">基本资料</a>
                        </dd>
                        <dd>
                            <a lay-href="set/user/password.html">修改密码</a>
                        </dd>
                        <hr>
                        <dd layadmin-event="logout" style="text-align: center;">
                            <a>退出</a>
                        </dd>
                    </dl>
                </li>

                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a href="javascript:;" layadmin-event="more">
                        <i class="layui-icon layui-icon-more-vertical"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="home/console.html">
                    <img src="/frontend/v1/images/title.png" alt="">
                </div>

                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    @if(count(array_intersect([
                               'frontend.workbench.index',
                               'frontend.workbench.leveling.wait',
                               'frontend.workbench.leveling.create',
                               'frontend.workbench.leveling.index',
                       ], $userPermissions)))

                    <li data-name="home" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>工作台</cite>
                        </a>
                        <dl class="layui-nav-child">
                            @if(Auth::user()->could('frontend.workbench.index'))
                                <dd data-name="console" class="">
                                    <a href="{{ route('frontend.workbench.index') }}">代充订单</a>
                                </dd>
                            @endif
                            @if(Auth::user()->could('frontend.workbench.leveling.wait'))
                                <dd data-name="console" class="">
                                    <a href="{{ route('frontend.workbench.leveling.wait') }}">代练待发</a>
                                </dd>
                            @endif
                            @if(Auth::user()->could('frontend.workbench.leveling.create'))
                                <dd data-name="console" class="">
                                    <a href="{{ route('frontend.workbench.leveling.create') }}">代练发布</a>
                                </dd>
                            @endif
                            @if(Auth::user()->could('frontend.workbench.leveling.index'))
                                <dd data-name="console" class="">
                                    <a href="{{ route('frontend.workbench.leveling.index') }}">代练订单</a>
                                </dd>
                            @endif
                        </dl>
                    </li>

                    @endif
                    <li data-name="component" class="layui-nav-item">

                        <a href="javascript:;" lay-tips="组件" lay-direction="2">
                            <i class="layui-icon layui-icon-component"></i>
                            <cite>组件</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="button">
                                <a lay-href="">按钮</a>
                            </dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 主体内容 -->
        <div class="layui-body">
            <div class="layadmin-tabsbody-item layui-show">
                <div class="layui-card layadmin-header">
                    @yield('breadcrumb')
                </div>
                <div class="layui-fluid">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <div class="layui-card">
                                @yield('main')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>

<style>
    .layui-side-menu,
    .layadmin-pagetabs .layui-tab-title li:after,
    .layadmin-pagetabs .layui-tab-title li.layui-this:after,
    .layui-layer-admin .layui-layer-title,
    .layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child {
        background-color: #20222A !important;
    }

    .layui-nav-tree .layui-this,
    .layui-nav-tree .layui-this>a,
    .layui-nav-tree .layui-nav-child dd.layui-this,
    .layui-nav-tree .layui-nav-child dd.layui-this a {
        background-color: #F78400 !important;
    }
    .layui-layout-admin .layui-logo {
        background-color: #F78400 !important;
    }
</style>
<script src="/frontend/v1/lib/js/layui/layui.js"></script>
<script src="/js/jquery-1.11.0.min.js"></script>
<script>
    layui.use(['element', 'form', 'laydate'], function () {
        var element = layui.element,
                form = layui.form,
                laydate = layui.laydate;
        var insStart = laydate.render({
            elem: '#test-laydate-start',
            theme: '#ff8500',
            min: 0,
            done: function (value, date) {
                //更新结束日期的最小日期
                insEnd.config.min = lay.extend({}, date, {
                    month: date.month - 1
                });

                //自动弹出结束日期的选择器
                insEnd.config.elem[0].focus();
            }
        });

        //结束日期
        var insEnd = laydate.render({
            elem: '#test-laydate-end',
            theme: '#ff8500',
            min: 0,
            done: function (value, date) {
                //更新开始日期的最大日期
                insStart.config.max = lay.extend({}, date, {
                    month: date.month - 1
                });
            }
        });
    });
</script>
<script>
    layui.config({
        base: '/' //静态资源所在路径
    }).extend({
        index: 'frontend/v1/lib/js/index' //主入口模块
    }).use('index');
</script>
@yield('js')
@yield('pop')
</body>
</html>