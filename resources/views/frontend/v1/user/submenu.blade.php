<ul class="seller_center_left_menu">
	<li class="{{ in_array(Route::currentRouteName(), ['home-accounts.index', 'home-accounts.edit']) ? 'current' : '' }}"><a href="{{ route('home-accounts.index') }}"> 我的账号 </a><div class="arrow"></div></li>
    @if(Auth::user()->parent_id == 0)
        @if(App\Models\RealNameIdent::where('user_id', Auth::id())->first())
            <li class="{{ in_array(Route::currentRouteName(), ['idents.index', 'idents.create', 'idents.edit']) ? 'current' : '' }}"><a href="{{ route('idents.index') }}"> 实名认证 </a><div class="arrow"></div></li>
        @else
            <li class="{{ in_array(Route::currentRouteName(), ['idents.index', 'idents.create', 'idents.edit']) ? 'current' : '' }}"><a href="{{ route('idents.create') }}"> 实名认证 </a><div class="arrow"></div></li>
        @endif
    @endif
        <li class="{{ in_array(Route::currentRouteName(), ['login.history']) ? 'current' : '' }}"><a href="{{ route('login.history') }}"> 登录记录 </a><div class="arrow"></div></li>
    @if(Auth::user()->could('station.index'))
        <li class="{{ in_array(Route::currentRouteName(), ['station.index', 'station.create', 'station.edit']) ? 'current' : '' }}"><a href="{{ route('station.index') }}"> 岗位管理 </a><div class="arrow"></div></li>
    @endif
    @if(Auth::user()->could('staff-management.index'))
        <li class="{{ in_array(Route::currentRouteName(), ['staff-management.index', 'staff-management.edit', 'staff-management.create']) ? 'current' : '' }}"><a href="{{ route('staff-management.index') }}"> 员工管理 </a><div class="arrow"></div></li>
    @endif
    @if(Auth::user()->could('hatchet-man-blacklist.index'))
        <li class="{{ in_array(Route::currentRouteName(), ['hatchet-man-blacklist.index', 'hatchet-man-blacklist.edit', 'hatchet-man-blacklist.create']) ? 'current' : '' }}"><a href="{{ route('hatchet-man-blacklist.index') }}"> 打手黑名单 </a><div class="arrow"></div></li>
    @endif
</ul>