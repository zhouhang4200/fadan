<div class="header">
    <div class="wrapper">
        <a href="" class="logo">
            <div class="t">
                <h1 style="text-align: center; padding-left: 30px;">千手平台</h1>
            </div>
            <div class="en"><img src="/frontend/images/en.png"></div>
        </a>
        <div class="nav">
            <ul>
                <li class="{{ in_array(Route::currentRouteName(), ['frontend.index', 'users.persional']) ? 'current' : '' }}"><a href="{{ route('frontend.index') }}">首页</a><div class="arrow"></div></li>
                @can('frontend.goods.index')
                    <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.goods' ? 'current' : '' }}"><a href="{{ route('frontend.goods.index') }}">商品</a><div class="arrow"></div></li>
                @endcan
                @can('frontend.order.receive')
                    <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.order' ? 'current' : '' }}"><a href="{{ route('frontend.order.receive') }}">订单</a><div class="arrow"></div></li>
                @endcan
                @can('frontend.finance.asset')
                    <li class="{{ substr(Route::currentRouteName(), 0, 16) == 'frontend.finance' ? 'current' : '' }}"><a href="{{ route('frontend.finance.asset') }}">财务</a><div class="arrow"></div></li>
                @endcan
                @can('frontend.workbench.index')
                    <li class="{{ substr(Route::currentRouteName(), 0, 18) == 'frontend.workbench' ? 'current' : '' }}"><a href="{{ route('frontend.workbench.index') }}">工作台</a><div class="arrow"></div></li>
                @endcan
                <li class="{{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'home-accounts.index' || Route::currentRouteName() == 'login.history' ? 'current' : '' || Route::currentRouteName() == 'user-groups.index' ? 'current' : '' || Route::currentRouteName() == 'rbacgroups.index' ? 'current' : '' || Route::currentRouteName() == 'idents.index' ? 'current' : '' || Route::currentRouteName() == 'home-system-logs.index' ? 'current' : '' || in_array(Route::currentRouteName(), ['users.create', 'users.edit', 'home-accounts.edit', 'user-groups.create', 'user-groups.edit', 'rbacgroups.create', 'rbacgroups.edit', 'idents.create']) ? 'current' : '' }}"><a href="{{ route('home-accounts.index') }}">账号</a><div class="arrow"></div></li>
                @can('frontend.setting.receiving-control.index')
                    <li class="{{ substr(Route::currentRouteName(), 0, 16) == 'frontend.setting' ? 'current' : '' }}"><a href="{{ route('frontend.setting.receiving-control.index') }}">设置</a><div class="arrow"></div></li>
                @endcan

                    <li class="{{ substr(Route::currentRouteName(), 0, 18) == 'frontend.statistic' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.employee') }}">统计</a><div class="arrow"></div></li>

                    <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.steam' ? 'current' : '' }}"><a href="{{ route('frontend.steam.goods.index') }}">Steam</a><div class="arrow"></div></li>

            </ul>
        </div>
        <div class="user">
            <ul class="layui-nav layui-bg-blue" lay-filter="demo">
                <li class="layui-nav-item">
                    <a href="#" class="current-status">{{ Auth::user()->online == 1 ? '在线' : '挂起' }}</a>
                    <dl class="layui-nav-child">
                        <dd><a href="#">在线</a></dd>
                        <dd><a href="#">挂起</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item"><a href="#"><i class="iconfont icon-tuichu" style="padding-right: 7px;"></i>注销登录</a></li>
            </ul>
        </div>
    </div>
</div>