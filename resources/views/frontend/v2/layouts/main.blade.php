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
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-2',
                    'name' => '代练发布',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-3',
                    'name' => '待发订单',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '订单投诉',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
            ]
        ],
        [
            'index' => '2',
            'name' => '财务',
            'icon' => '2',
            'display' => '2',
            'submenu' => [
                [
                    'index' => '1-1',
                    'name' => '我的资产',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-2',
                    'name' => '资产日报',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-3',
                    'name' => '资金流水',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '我的提现',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '员工统计',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '员工统计',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '订单统计',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '短信统计',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '财务订单列表',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
            ]
        ],
        [
            'index' => '3',
            'name' => '工作台21',
            'icon' => '2',
            'display' => '2',
            'submenu' => [
                [
                    'index' => '1-1',
                    'name' => '待发订单',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-2',
                    'name' => '代练发布',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-3',
                    'name' => '待发订单',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '订单投诉',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
            ]
        ],
        [
            'index' => '4',
            'name' => '工作台21',
            'icon' => '2',
            'display' => '2',
            'submenu' => [
                [
                    'index' => '1-1',
                    'name' => '待发订单',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-2',
                    'name' => '代练发布',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-3',
                    'name' => '待发订单',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
                [
                    'index' => '1-4',
                    'name' => '订单投诉',
                    'url' => route('order.game-leveling.delete-message'),
                    'display' => '2',
                ],
            ]
        ]
    ])
    ?>
    <layout>
        @yield('content')
    </layout>
</div>
<script>
    var menu = '{!! $menu !!}';
</script>
<script src="{{mix('js/manifest.js', 'frontend/v2')}}"></script>
<script src="{{mix('js/vendor.js', 'frontend/v2')}}"></script>
<script src="{{mix('js/app.js', 'frontend/v2')}}"></script>
</body>
</html>