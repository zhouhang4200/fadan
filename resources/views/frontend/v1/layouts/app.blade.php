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
   'frontend.workbench.leveling.complaints',
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
    'home-accounts.edit',
    'station.edit',
    'station.create',
    'staff-management.edit',
    'staff-management.create',
    'hatchet-man-blacklist.edit',
    'hatchet-man-blacklist.create',
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
    'frontend.finance.month-settlement-orders.index',
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
    'frontend.setting.order-send-channel.index',
];

$goodsRoute = [
    'frontend.goods.index',
];

$myAccount = ['home-accounts.edit', 'home-accounts.index'];
$stationManagement = ['station.create', 'station.index', 'station.edit'];
$employeeManagement = ['staff-management.index', 'staff-management.edit', 'staff-management.create'];
$blacklist = ['hatchet-man-blacklist.index', 'hatchet-man-blacklist.create', 'hatchet-man-blacklist.edit'];
$finance = ['frontend.finance.asset', 'frontend.finance.amount-flow', 'frontend.finance.asset-daily', 
    'frontend.finance.withdraw-order', 'frontend.finance.order-report.index', 'frontend.statistic.employee',
    'frontend.statistic.order', 'frontend.statistic.sms'
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
        .layui-side-menu .layui-nav .layui-nav-item .layui-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            margin-top: -20px;
            font-size: 24px;
        }
        .layui-card-body {
            padding-bottom: 30px;
        }
    </style>
    @yield('css')
</head>

<body class="layui-layout-body">

<div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
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
                        <img src="{{ auth()->user()->voucher ?? '' }}" class="layui-nav-img">
                        <cite>{{ auth()->user()->username }}</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd style="text-align: center;">
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
                <div class="layui-logo" lay-href="">
                    <img src="/frontend/v1/images/title.png" alt="">
                </div>

                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    @if(count(array_intersect($homeRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $homeRoute)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="主页" lay-direction="2">
                                <i class="layui-icon icon-electronics-o"></i>
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
                            <a href="javascript:;" lay-tips="工作台" lay-direction="2">
                                <i class="layui-icon iconfont  icon-electronics-o"></i>
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
                                @if(Auth::user()->could('frontend.workbench.leveling.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.workbench.leveling.complaints') layui-this  @endif">
                                        <a href="{{ route('frontend.workbench.leveling.complaints') }}">订单投诉</a>
                                    </dd>
                                @endif
                            </dl>
                        </li>
                    @endif

                    @if(count(array_intersect($accountRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $accountRoute)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="账号" lay-direction="2">
                                <i class="layui-icon iconfont  icon-group-o"></i>
                                <cite>账号</cite>
                            </a>
                            <dl class="layui-nav-child">
                                <dd data-name="console" class="@if( in_array(Route::currentRouteName(), $myAccount)) layui-this  @endif">
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
                                    <dd data-name="console" class="@if( in_array(Route::currentRouteName(), $stationManagement)) layui-this  @endif">
                                        <a href="{{ route('station.index') }}">岗位管理</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('staff-management.index'))
                                    <dd data-name="console" class="@if( in_array(Route::currentRouteName(), $employeeManagement)) layui-this  @endif">
                                        <a href="{{ route('staff-management.index') }}">员工管理</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('hatchet-man-blacklist.index'))
                                    <dd data-name="console" class="@if( in_array(Route::currentRouteName(), $blacklist)) layui-this  @endif">
                                        <a href="{{ route('hatchet-man-blacklist.index') }}">打手黑名单</a>
                                    </dd>
                                @endif
                            </dl>
                        </li>
                    @endif

                    @if(count(array_intersect($financeRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $finance)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="财务" lay-direction="2">
                                <i class="layui-icon iconfont  icon-finance-o"></i>
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
                                @if(Auth::user()->could('frontend.finance.order-report.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.finance.order-report.index') layui-this  @endif">
                                        <a href="{{ route('frontend.finance.order-report.index') }}">财务订单列表</a>
                                    </dd>
                                @endif
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.finance.month-settlement-orders.index') layui-this  @endif">
                                        <a href="{{ route('frontend.finance.month-settlement-orders.index') }}">内部欠款订单</a>
                                    </dd>
                            </dl>
                        </li>
                    @endif
                    @if(count(array_intersect($settingRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $settingRoute)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="设置" lay-direction="2">
                                <i class="layui-icon  iconfont    icon-setting-o"></i>
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
                                <?php $route = array_intersect(['frontend.setting.sending-assist.auto-markup', 'frontend.setting.order-send-channel.index'], $userPermissions); ?>
                                @if(count($route) > 0)
                                    <dd data-name="console" class="@if(in_array(Route::currentRouteName(), [ 'frontend.setting.sending-assist.auto-markup', 'frontend.setting.order-send-channel.index'])) layui-this  @endif">
                                        <a href="{{ Auth::user()->could('frontend.setting.sending-assist.auto-markup') ? route('frontend.setting.sending-assist.auto-markup') : (Auth::user()->could('frontend.setting.order-send-channel.index') ?
                                        route('frontend.setting.order-send-channel.index') : '' )
                                          }} ">代练发单辅助</a>
                                    </dd>
                                @endif
                            </dl>
                        </li>
                    @endif

                    @if(count(array_intersect($goodsRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $goodsRoute)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="商品列表" lay-direction="2">
                                <i class="layui-icon  iconfont  icon-add-s"></i>
                                <cite>商品列表</cite>
                            </a>
                            <dl class="layui-nav-child">
                                @if(Auth::user()->could('frontend.goods.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.goods.index') layui-this  @endif">
                                        <a href="{{ route('frontend.goods.index') }}">商品管理</a>
                                    </dd>
                                @endif
                            </dl>
                        </li>
                    @endif
                    
                    <?php $babyRoute = ['frontend.baby.index', 'frontend.baby.show']; ?>
                    @if(count(array_intersect($babyRoute, $userPermissions)))
                        <li data-name="home" class="layui-nav-item @if(in_array(Route::currentRouteName(), $babyRoute)) layui-nav-itemed @endif">
                            <a href="javascript:;" lay-tips="店铺参谋" lay-direction="2">
                                <i class="layui-icon  iconfont  icon-tianmao"></i>
                                <cite>店铺参谋</cite>
                            </a>
                            <dl class="layui-nav-child">
                                @if(Auth::user()->could('frontend.baby.index'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.baby.index') layui-this  @endif">
                                        <a href="{{ route('frontend.baby.index') }}">宝贝订单</a>
                                    </dd>
                                @endif
                                @if(Auth::user()->could('frontend.baby.show'))
                                    <dd data-name="console" class="@if( Route::currentRouteName() == 'frontend.baby.show') layui-this  @endif">
                                        <a href="{{ route('frontend.baby.show') }}">宝贝运营状况</a>
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
<script src="/frontend/js/helper.js"></script>
<script src="//cdn.bootcss.com/socket.io/1.3.7/socket.io.min.js"></script>
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});
    var socket = io('{{ config('app.socket_url') }}');
    layui.use(['element', 'form', 'laydate', 'layer'], function () {
        var element = layui.element,
                form = layui.form,
                layer = layui.layer,
                laydate = layui.laydate;
        var insStart = laydate.render({
            elem: '#test-laydate-start',
            done: function (value, date) {
                insEnd.config.elem[0].focus();
            }
        });

        //结束日期
        var insEnd = laydate.render({
            elem: '#test-laydate-end'
        });

        $('#logout').click(function () {
            layer.confirm('确定退出吗?', {icon: 3, title:'提示'}, function(index){
                $.post('/logout', {}, function(str){
                    window.location.href='/login';
                });
                layer.close(index);
            });
        });

        form.on('radio()', function(data){
            if (data.value == '支付宝') {
                $('#bank').addClass('layui-hide');
                $('#alipay').removeClass('layui-hide');
            } else {
                $('#alipay').addClass('layui-hide');
                $('#bank').removeClass('layui-hide');
            }
        });
        // 充值短信
        form.on('submit(sms-recharge)', function(data){
            $.post('{{ route('frontend.finance.sms.recharge') }}', {amount:data.field.amount}, function(result){
                if (result.status == 0) {
                    layer.msg(result.message);
                } else {
                    layer.closeAll();
                    layer.msg(result.message);

                    $('.balance').html(result.content.balance);
                    $('#sms-balance').html(result.content.sms_balance);
                    $('#sms-rechargeable').html(Math.round(result.content.balance / 0.1));
                }
            }, 'json');
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

    $('body').on('click', '#withdraw', function () {
        layer.open({
            type: 1,
            title: '提现单',
            area: ['350px', '240px'],
            content: $('#withdraw-box')
        });
    });

    $('body').on('click', '#withdraw-submit', function () {
        var loading = layer.load(2, {shade: [0.1, '#000']});
        $.ajax({
            url: "{{ route('frontend.finance.withdraw-order.store') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                fee: $('[name="fee"]').val(),
                remark: $('[name="remark"]').val()
            },
            error: function (data) {
                layer.close(loading);
                var responseJSON = data.responseJSON.errors;
                for (var key in responseJSON) {
                    layer.msg(responseJSON[key][0]);
                    break;
                }
            },
            success: function (data) {
                layer.close(loading);
                if (data.status === 1) {
                    layer.alert('操作成功', function () {
                        location.href = "{{ route('frontend.finance.withdraw-order') }}";
                    });
                } else {
                    layer.alert(data.message);
                }
            }
        });
    });

    $('body').on('click', '#sms-recharge', function () {
        layer.open({
            type: 1,
            title: '短信充值',
            area: ['430px'],
            content: $('#sms-recharge-pop')
        });
    });

    $('body').on('click', '.cancel', function () {
        layer.closeAll();
    });

    // 退款通知
    socket.on('notification:balanceNotice', function (data) {
        if (data.user_id == '{{ auth()->user()->getPrimaryUserId()}}') {
            layer.alert(data.message,{
                title:data.title,
                btnAlign:'c',
                btn: [data.type == 1 ? '余额充值': '短信充值']
            }, function(index){
                layer.open({
                    type: 1,
                    title: '提示',
                    area: '400px;',
                    shade: 0.2,
                    content: data.type == 1 ? $('#charge-pop') : $('#sms-recharge-pop')
                });
            });
        }
    });
</script>
@yield('js')
</body>
<div id="withdraw-box" style="display: none;padding: 20px 60px 20px 0;">
    <div class="layui-form-item" style="margin-bottom: 15px;">
        <label class="layui-form-label">提现金额</label>
        <div class="layui-input-block">
            <input type="text" name="fee" class="layui-input" placeholder="可提现金额 {{ Auth::user()->userAsset->balance }}">
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom: 15px;">
        <label class="layui-form-label">备注说明</label>
        <div class="layui-input-block">
            <input type="text" name="remark" class="layui-input" placeholder="可留空">
        </div>
    </div>
    <div id="template"></div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button id="withdraw-submit" class="qs-btn qs-bg-blue" type="button">提交</button>
        </div>
    </div>
</div>
<div id="sms-recharge-pop" style="display: none;padding: 20px;" class="layui-form">
    <div class="" style="padding: 15px">
        <label class="">当前账户余额：<span class="balance">{{ $userInfo->userAsset->balance + 0  }}</span>元，短信费0.03元/条，最多可充值 <span id="sms-rechargeable">{{ bcdiv($userInfo->userAsset->balance, 0.03, 0) }}</span> 条</label>
    </div>
    <div class="layui-form-item" style="margin-bottom: 15px">
        <label class="layui-form-label">充值短信(条)</label>
        <div class="layui-input-inline">
            <input type="text" name="amount" required lay-verify="required|number" placeholder="请输入" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div id="template"></div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button id="" class="qs-btn qs-bg-blue" type="button" lay-submit lay-filter="sms-recharge">提交</button>
            <button id="" class="qs-btn qs-btn-primary qs-btn-table cancel" type="button" style="">取消</button>
        </div>
    </div>
</div>
<div class="layui-form" id="charge-pop" style="display: none;padding: 20px">
    <div class="layui-form-item">
        <label class="layui-form-label" style="width: 60px;padding:10px;text-align: left">充值方式</label>
        <div class="layui-input-block" style="margin-left:0">
            <input type="radio" name="chargeMode" value="支付宝" title="支付宝" checked>
            <input type="radio" name="chargeMode" value="银行卡" title="银行卡" >
        </div>
    </div>
    <div style="line-height: 22px;" id="bank" class="layui-hide">
        <span style="color:#e51c23">请用您账号绑定的银行卡往以下银行卡转账完成充值，转账金额即充值金额，转账时需要填写“备注”，请保证与下方“转账备注”相同，否则会出现充值失败！</span>
        <br/>转账后可能需要等待几分钟才能充值成功，请耐心等待！<br/><br/>
        账号：{{ optional($userInfo->transferAccountInfo)->bank_card  }}<br/>
        户名：{{ optional($userInfo->transferAccountInfo)->name }}<br/>
        开户行：{{ optional($userInfo->transferAccountInfo)->bank_name }}<br/>
        转账备注：{{ optional($userInfo->transferAccountInfo)->user_id }}<br/>
    </div>
    <div style="line-height: 22px;" id="alipay">
        <p style="color:#e51c23">1. 请先联系运营人员开通“自动加款”功能。</p>
        <p style="color:#e51c23">2. 请用您的支付宝往以下支付宝账号转账完成充值，转账金额即充值金额，转账时需要填写“备注”，请保证与下方“转账备注”相同，否则会出现充值失败！</p>
        转账后可能需要等待几分钟才能充值成功，请耐心等待！<br/><br/>
        账号：{{ optional($userInfo->transferAccountInfo)->alipay  }}<br/>
        户名：{{ optional($userInfo->transferAccountInfo)->name }}<br/>
        转账备注：{{ optional($userInfo->transferAccountInfo)->user_id }}<br/>
    </div>
</div>
@yield('pop')
</html>