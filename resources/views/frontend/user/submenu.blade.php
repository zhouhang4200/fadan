<ul class="seller_center_left_menu">
	<li class="{{ in_array(Route::currentRouteName(), ['home-accounts.index', 'home-accounts.edit']) ? 'current' : '' }}"><a href="{{ route('home-accounts.index') }}"> 我的账号 </a><div class="arrow"></div></li>
    <li class="{{ in_array(Route::currentRouteName(), ['login.history']) ? 'current' : '' }}"><a href="{{ route('login.history') }}"> 登录记录 </a><div class="arrow"></div></li>
    @if(Auth::user()->parent_id == 0)
            @if(App\Models\RealNameIdent::where('user_id', Auth::id())->first())
                @can('idents.index')
                <li class="{{ in_array(Route::currentRouteName(), ['idents.index', 'idents.create', 'idents.edit']) ? 'current' : '' }}"><a href="{{ route('idents.index') }}"> 实名认证 </a><div class="arrow"></div></li>
                @endcan
            @else
                @can('idents.create')
                <li class="{{ in_array(Route::currentRouteName(), ['idents.index', 'idents.create', 'idents.edit']) ? 'current' : '' }}"><a href="{{ route('idents.create') }}"> 实名认证 </a><div class="arrow"></div></li>
                @endcan
            @endif
        @can('home-punishes.index')
            <li class="{{ in_array(Route::currentRouteName(), ['home-punishes.index']) ? 'current' : '' }}"><a href="{{ route('home-punishes.index') }}"> 违规管理 </a><div class="arrow"></div></li>
        @endcan
        @can('home-system-logs.index')
            <li class="{{ in_array(Route::currentRouteName(), ['home-system-logs.index']) ? 'current' : '' }}"><a href="{{ route('home-system-logs.index') }}"> 系统日志 </a><div class="arrow"></div></li>
        @endcan
        @can('login.history')
            <li class="{{ in_array(Route::currentRouteName(), ['login.history']) ? 'current' : '' }}"><a href="{{ route('login.history') }}"> 登录记录 </a><div class="arrow"></div></li>
        @endcan
        @can('users.index')
            <li class="{{ in_array(Route::currentRouteName(), ['users.index', 'users.create', 'users.edit', 'user-groups.create']) ? 'current' : '' }}"><a href="{{ route('users.index') }}"> 子账号管理 </a><div class="arrow"></div></li>
        @endcan
        @can('rbacgroups.index')
            <li class="{{ in_array(Route::currentRouteName(), ['rbacgroups.index', 'rbacgroups.create', 'rbacgroups.edit']) ? 'current' : '' }}"><a href="{{ route('rbacgroups.index') }}"> 权限组管理 </a><div class="arrow"></div></li>
        @endcan
    @endif
</ul>