<?php
$userPermissions = Auth::user()->getUserPermissions()->pluck('name')->toArray();

$homeRoute = [
    'home-punishes.index',
    'frontend.index',
];

$workbenchRoute = [
   'frontend.workbench.index',
   'frontend.workbench.leveling.wait',
   'frontend.workbench.leveling.create',
   'frontend.workbench.leveling.index',
];

$accountRoute = [
    'station.index',
    'home-accounts.index',
    'login.history',
    'idents.index',
    'idents.create',
    'idents.edit',
    'idents.edit',
    'staff-management.index',
    'hatchet-man-blacklist.index',
];

$financeRoute = [
    'frontend.finance.asset',
    'frontend.finance.asset-daily',
    'frontend.finance.amount-flow',
    'frontend.finance.withdraw-order',
    'frontend.statistic.employee',
    'frontend.statistic.order',
    'frontend.statistic.sms',
    'frontend.finance.order-report.index',
];

$settingRoute = [
    'frontend.setting.receiving-control.index',
    'frontend.setting.api-risk-management.index',
    'frontend.setting.skin.index',
    'frontend.setting.automatically-grab.goods',
    'frontend.setting.sms.index',
    'frontend.setting.tb-auth.index',
    'frontend.setting.sending-assist.auto-markup',
    'frontend.setting.tb-auth.store',
];

$goodsRoute = [
    'frontend.goods.index',
];

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
            width: 42%;
        }
        /* 改写header高度 */
        .layui-card-header {
            height: 56px;
            line-height: 32px;
            color: #303133;
            font-size: 14px;
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
                    <a  id="leveling-message">
                        <i class="layui-icon layui-icon-notice"></i>
                        <!-- 如果有新消息，则显示小圆点 -->
                        <span class="layui-badge-dot @if(levelingMessageCount(auth()->user()->getPrimaryUserId(), 4) == 0) layui-hide  @endif"></span>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect style="margin-right: 30px;">
                    <a href="javascript:;">
                        <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
                        <cite>贤心</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a href="{{ route('frontend.index') }}">基本资料</a>
                        </dd>
                        <hr>
                        <dd style="text-align: center;">
                            <a href="#" id="logout">退出</a>
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
                    @if(count(array_intersect($homeRoute, $userPermissions)))
                    <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $homeRoute)) layui-nav-itemed @endif">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>首页</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.index') layui-this  @endif">
                                <a href="{{ route('frontend.index') }}">首页</a>
                            </dd>
                            @if(Auth::user()->could('data.index'))
                                <dd data-name="console" class="@if( Route::currentRouteName() == 'data.index') layui-this  @endif">
                                    <a href="{{ route('data.index') }}">经营数据</a>
                                </dd>
                            @endif
                            @if(Auth::user()->could('home-punishes.index'))
                                <dd data-name="console" class="@if( Route::currentRouteName() == 'home-punishes.index') layui-this  @endif">
                                    <a href="{{ route('home-punishes.index') }}">奖惩记录</a>
                                </dd>
                            @endif
                        </dl>
                    </li>
                    @endif

                    @if(count(array_intersect($workbenchRoute, $userPermissions)))
                    <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $workbenchRoute)) layui-nav-itemed @endif">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>工作台</cite>
                        </a>
                        <dl class="layui-nav-child">
                            @if(Auth::user()->could('frontend.workbench.index'))
                                <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.workbench.index') layui-this  @endif">
                                    <a href="{{ route('frontend.workbench.index') }}">代充订单</a>
                                </dd>
                            @endif
                            @if(Auth::user()->could('frontend.workbench.leveling.wait'))
                                <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.workbench.leveling.wait') layui-this  @endif">
                                    <a href="{{ route('frontend.workbench.leveling.wait') }}">代练待发</a>
                                </dd>
                            @endif
                            @if(Auth::user()->could('frontend.workbench.leveling.create'))
                                <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.workbench.leveling.create') layui-this  @endif">
                                    <a href="{{ route('frontend.workbench.leveling.create') }}">代练发布</a>
                                </dd>
                            @endif
                            @if(Auth::user()->could('frontend.workbench.leveling.index'))
                                <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.workbench.leveling.index') layui-this  @endif">
                                    <a href="{{ route('frontend.workbench.leveling.index') }}">代练订单</a>
                                </dd>
                            @endif
                        </dl>
                    </li>
                    @endif

                    @if(count(array_intersect($accountRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $accountRoute)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="账号" lay-direction="2">
                                <i class="layui-icon layui-icon-home"></i>
                                <cite>账号</cite>
                            </a>
                            <dl class="layui-nav-child">
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'home-accounts.index') layui-this  @endif">
                                        <a href="{{ route('home-accounts.index') }}">我的账号</a>
                                    </dd>
                                    @if(Auth::user()->parent_id == 0)
                                        @if(App\Models\RealNameIdent::where('user_id', Auth::id())->first())
                                            <dd data-name="console" class="@if( Route::currentRouteName() == 'idents.index') layui-this  @endif">
                                                <a href="{{ route('idents.index') }}">实名认证</a>
                                            </dd>
                                        @else
                                            <dd data-name="console" class="@if( Route::currentRouteName() == 'idents.create') layui-this  @endif">
                                                <a href="{{ route('idents.create') }}">实名认证</a>
                                            </dd>
                                        @endif
                                    @endif
                                    
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'login.history') layui-this  @endif">
                                        <a href="{{ route('login.history') }}">登录记录</a>
                                    </dd>

                                @if(Auth::user()->could('station.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'station.index') layui-this  @endif">
                                        <a href="{{ route('station.index') }}">岗位管理</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('staff-management.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'staff-management.index') layui-this  @endif">
                                        <a href="{{ route('staff-management.index') }}">员工管理</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('hatchet-man-blacklist.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'hatchet-man-blacklist.index') layui-this  @endif">
                                        <a href="{{ route('hatchet-man-blacklist.index') }}">打手黑名单</a>
                                    </dd>
                                @endif
                            </dl>
                        </li>
                    @endif

                    @if(count(array_intersect($financeRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $financeRoute)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="财务" lay-direction="2">
                                <i class="layui-icon layui-icon-home"></i>
                                <cite>财务</cite>
                            </a>
                            <dl class="layui-nav-child">
                                @if(Auth::user()->could('frontend.finance.asset'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.finance.asset') layui-this  @endif">
                                        <a href="{{ route('frontend.finance.asset') }}">我的资产</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.finance.asset-daily'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.finance.asset-daily') layui-this  @endif">
                                        <a href="{{ route('frontend.finance.asset-daily') }}">资产日报</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.finance.amount-flow'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.finance.amount-flow') layui-this  @endif">
                                        <a href="{{ route('frontend.finance.amount-flow') }}">资金流水</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.finance.withdraw-order'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.finance.withdraw-order') layui-this  @endif">
                                        <a href="{{ route('frontend.finance.withdraw-order') }}">我的提现</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.statistic.employee'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.statistic.employee') layui-this  @endif">
                                        <a href="{{ route('frontend.statistic.employee') }}">员工统计</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.statistic.order'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.statistic.order') layui-this  @endif">
                                        <a href="{{ route('frontend.statistic.order') }}">订单统计</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.statistic.sms'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.statistic.sms') layui-this  @endif">
                                        <a href="{{ route('frontend.statistic.sms') }}">短信统计</a>
                                    </dd>
                                @endif
                                {{--@if(Auth::user()->could('frontend.finance.order-report'))--}}
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.finance.order-report.index') layui-this  @endif">
                                        <a href="{{ route('frontend.finance.order-report.index') }}">财务订单列表</a>
                                    </dd>
                                {{--@endif--}}
                            </dl>
                        </li>
                    @endif

                    @if(count(array_intersect($settingRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $settingRoute)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="主页" lay-direction="2">
                                <i class="layui-icon layui-icon-home"></i>
                                <cite>设置</cite>
                            </a>
                            <dl class="layui-nav-child">
                                @if(Auth::user()->could('frontend.setting.receiving-control.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.setting.receiving-control.index') layui-this  @endif">
                                        <a href="{{ route('frontend.setting.receiving-control.index') }}">接单设置</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.setting.api-risk-management.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.setting.api-risk-management.index') layui-this  @endif">
                                        <a href="{{ route('frontend.setting.api-risk-management.index') }}">API下单风控</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.setting.skin.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.setting.skin.index') layui-this  @endif">
                                        <a href="{{ route('frontend.setting.skin.index') }}">皮肤交易QQ</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.setting.automatically-grab.goods'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.setting.automatically-grab.goods') layui-this  @endif">
                                        <a href="{{ route('frontend.setting.automatically-grab.goods') }}">抓取商品配置</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.setting.sms.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.setting.sms.index') layui-this  @endif">
                                        <a href="{{ route('frontend.setting.sms.index') }}">短信管理</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.setting.tb-auth.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.setting.tb-auth.store') layui-this  @endif">
                                        <a href="{{ route('frontend.setting.tb-auth.store') }}">店铺授权</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.setting.sending-assist.auto-markup'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.setting.sending-assist.auto-markup') layui-this  @endif">
                                        <a href="{{ route('frontend.setting.sending-assist.auto-markup') }}">代练发单辅助</a>
                                    </dd>
                                @endif
                            </dl>
                        </li>
                    @endif

                    @if(count(array_intersect($goodsRoute, $userPermissions)))
                    <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $goodsRoute)) layui-nav-itemed @endif">
                        <a href="javascript:;" lay-tips="商品列表" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>商品列表</cite>
                        </a>
                        <dl class="layui-nav-child">
                            @if(Auth::user()->could('frontend.goods.index'))
                                <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.goods.index') layui-this  @endif">
                                    <a href="{{ route('frontend.goods.index') }}">代充订单</a>
                                </dd>
                            @endif
                        </dl>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- 主体内容 -->
        <div class="layui-body">
            <div class="layadmin-tabsbody-item layui-show">
                <div class="layui-fluid">
                    <div class="layui-row layui-col-space15">
                        @yield('main')
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
<script src="/js/encrypt.js"></script>
<script>
    layui.use(['element', 'form', 'laydate', 'layer'], function () {
        var element = layui.element,
                form = layui.form,
                layer = layui.layer,
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

        $('#logout').click(function () {
            layer.confirm('确定退出吗?', {icon: 3, title:'提示'}, function(index){
                $.post('/logout', {}, function(str){
                    window.location.href='/login';
                });
                layer.close(index);
            });
        });
    });
    layui.config({
        base: '/frontend/v1/' //静态资源所在路径
    }).extend({
        index: 'lib/js/index' //主入口模块
    }).use('index');

    $('#leveling-message').click(function () {
        layer.open({
            title:'代练留言',
            type: 2,
            move: false,
            resize:false,
            scrollbar: false,
            area: ['800px', '500px'],
            content: '{{ route('frontend.message-list') }}'
        });
        return false;
    });
</script>
@yield('js')
</body>
@yield('pop')
</html>