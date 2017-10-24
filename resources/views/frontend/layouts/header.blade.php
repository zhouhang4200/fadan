<div class="header">
    <div class="wrapper">
        <a href="" class="logo">
            <div class="t">
                <h1>千手 · 订单集市</h1>
            </div>
            <div class="en"><img src="/frontend/images/en.png"></div>
        </a>
        <div class="nav">
            <ul>
                <li class="{{ Route::currentRouteName() == 'frontend.index' ? 'current' : '' }}"><a href="{{ route('frontend.index') }}">首页</a><div class="arrow"></div></li>
                <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.goods' ? 'current' : '' }}"><a href="{{ route('frontend.goods.index') }}">商品</a><div class="arrow"></div></li>
                <li class="{{ substr(Route::currentRouteName(), 0, 16) == 'frontend.finance' ? 'current' : '' }}"><a href="{{ route('frontend.finance.asset') }}">财务</a><div class="arrow"></div></li>
                <li class="{{ substr(Route::currentRouteName(), 0, 18) == 'frontend.workbench' ? 'current' : '' }}"><a href="{{ route('frontend.workbench.index') }}">工作台</a><div class="arrow"></div></li>
                <li class="{{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'home-accounts.index' || Route::currentRouteName() == 'login.history' ? 'current' : '' || Route::currentRouteName() == 'user-groups.index' ? 'current' : '' || Route::currentRouteName() == 'rbacgroups.index' ? 'current' : '' || Route::currentRouteName() == 'idents.index' ? 'current' : '' || Route::currentRouteName() == 'home-system-logs.index' ? 'current' : '' || in_array(Route::currentRouteName(), ['users.create', 'users.edit', 'home-accounts.edit', 'user-groups.create', 'user-groups.edit', 'rbacgroups.create', 'rbacgroups.edit', 'idents.create']) ? 'current' : '' }}"><a href="{{ route('home-accounts.index') }}">账号</a><div class="arrow"></div></li>
            </ul>
        </div>
        <div class="user">
            <div class="operation">
                <a href=""><i class="iconfont icon-shezhi"></i>设置</a>
                <a href="javascript:(logout())" ><i class="iconfont icon-tuichu" style="font-size: 21px;top:1px"></i>注销登录</a>
            </div>
        </div>
    </div>
</div>