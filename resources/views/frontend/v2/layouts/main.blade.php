<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{mix('/css/app.css', 'frontend/v2')}}">
    <link rel="stylesheet" href="{{mix('/css/theme.css', 'frontend/v2')}}">
</head>
<body>
<div id="app" style="background: #f0f2f5;">
    <?php

    $menu = json_encode([
        [
            'index' => '1',
            'name' => '工作台',
            'icon' => 'el-icon-location',
            'display' => '2',
            'submenu' => [
                [
                    'index' => '1-1',
                    'name' => '待发订单',
                    'url' => route('order.game-leveling.taobao.index'),
                    'display' => '2',
                ],
                [
                    'index' => '1-2',
                    'name' => '代练发布',
                    'url' => route('order.game-leveling.create'),
                    'display' => '2',
                ],
                [
                    'index' => '1-3',
                    'name' => '代练订单',
                    'url' => route('order.game-leveling.index'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '订单投诉',
                    'url' => route('order.game-leveling.businessman-complain.index'),
                    'display' => '2',
                ],
            ]
        ],
        [
            'index' => '2',
            'name' => '财务',
            'icon' => 'el-icon-location',
            'display' => '2',
            'submenu' => [
                [
                    'index' => '2-1',
                    'name' => '我的资产',
                    'url' => route('v2.finance.my-asset'),
                    'display' => '2',
                ],
                [
                    'index' => '2-2',
                    'name' => '资产日报',
                    'url' => route('v2.finance.daily-asset'),
                    'display' => '2',
                ],
                [
                    'index' => '2-3',
                    'name' => '资金流水',
                    'url' => route('v2.finance.amount-flow'),
                    'display' => '2',
                ],
                [
                    'index' => '2-4',
                    'name' => '我的提现',
                    'url' => route('v2.finance.my-withdraw'),
                    'display' => '2',
                ],
                [
                    'index' => '2-5',
                    'name' => '员工统计',
                    'url' => route('v2.statistic.employee'),
                    'display' => '2',
                ],
                [
                    'index' => '2-6',
                    'name' => '订单统计',
                    'url' => route('v2.statistic.order'),
                    'display' => '2',
                ],
                [
                    'index' => '2-7',
                    'name' => '短信统计',
                    'url' => route('v2.statistic.message'),
                    'display' => '2',
                ],
                [
                    'index' => '2-8',
                    'name' => '财务订单列表',
                    'url' => route('v2.finance.order'),
                    'display' => '2',
                ],
            ]
        ],
        [
            'index' => '3',
            'name' => '账号',
            'icon' => 'el-icon-location',
            'display' => '2',
            'submenu' => [
                [
                    'index' => '3-1',
                    'name' => '我的账号',
                    'url' => route('v2.account.mine'),
                    'display' => '2',
                ],
                [
                    'index' => '3-2',
                    'name' => '登录记录',
                    'url' => route('v2.account.login-history'),
                    'display' => '2',
                ],
                [
                    'index' => '3-3',
                    'name' => '实名认证',
                    'url' => route('v2.account.authentication'),
                    'display' => '2',
                ],
                [
                    'index' => '3-4',
                    'name' => '岗位管理',
                    'url' => route('v2.account.station'),
                    'display' => '2',
                ],
                [
                    'index' => '3-5',
                    'name' => '员工管理',
                    'url' => route('v2.account.employee'),
                    'display' => '2',
                ],
                [
                    'index' => '3-6',
                    'name' => '打手黑名单',
                    'url' => route('v2.account.black-list'),
                    'display' => '2',
                ],
            ]
        ],
        [
            'index' => '4',
            'name' => '设置',
            'icon' => 'el-icon-location',
            'display' => '2',
            'submenu' => [
                [
                    'index' => '4-1',
                    'name' => '抓取商品配置',
                    'url' => route('v2.setting.goods'),
                    'display' => '2',
                ],
                [
                    'index' => '4-2',
                    'name' => '短信管理',
                    'url' => route('v2.setting.message'),
                    'display' => '2',
                ],
                [
                    'index' => '4-3',
                    'name' => '店铺授权',
                    'url' => route('v2.setting.authorize'),
                    'display' => '2',
                ],
                [
                    'index' => '4-4',
                    'name' => '代练发单辅助',
                    'url' => route('v2.setting.auxiliary'),
                    'display' => '2',
                ],
            ]
        ]
    ]);
    ?>
    <layout>
        @yield('content')
    </layout>
</div>
<script>
    var menu = '{!! $menu !!}';
    var openMenu = 1;
</script>
<script src="{{mix('js/manifest.js', 'frontend/v2')}}"></script>
<script src="{{mix('js/vendor.js', 'frontend/v2')}}"></script>
<script src="{{mix('js/app.js', 'frontend/v2')}}"></script>
</body>
</html>