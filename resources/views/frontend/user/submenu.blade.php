<ul class="seller_center_left_menu">
	<li class="{{ Route::currentRouteName() == 'home-accounts.index' ? 'current' : '' }}"><a href="{{ route('home-accounts.index') }}"> 我的账号 </a><div class="arrow"></div></li>
    <li class="{{ Route::currentRouteName() == 'login.history' ? 'current' : '' }}"><a href="{{ route('login.history') }}"> 登录记录 </a><div class="arrow"></div></li>
    @if (Auth::user()->pid == 0)
    <li class="{{ Route::currentRouteName() == 'users.index' ? 'current' : '' }}"><a href="{{ route('users.index') }}"> 子账号管理 </a><div class="arrow"></div></li>
    <li class="{{ Route::currentRouteName() == 'rbacgroups.index' ? 'current' : '' }}"><a href="{{ route('rbacgroups.index') }}"> 权限组管理 </a><div class="arrow"></div></li>
    <li class="{{ Route::currentRouteName() == 'user-groups.index' ? 'current' : '' }}"><a href="{{ route('user-groups.index') }}"> 子账号分组 </a><div class="arrow"></div></li>
    @endif
</ul>