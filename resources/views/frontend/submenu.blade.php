<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.index' ? 'current' : '' }}"><a href="{{ route('frontend.index') }}">首页</a><div class="arrow"></div></li>

    @if(Auth::user()->could('frontend.workbench.recharge.index'))
    	<li class="{{ Route::currentRouteName() == 'data.index' ? 'current' : '' }}"><a href="{{ route('data.index') }}">经营数据</a><div class="arrow"></div></li>
    @endif

    @if(Auth::user()->could('home-punishes.index') && Auth::user()->parent_id == 0)
        <li class="{{ Route::currentRouteName() == 'home-punishes.index' ? 'current' : '' }}"><a href="{{ route('home-punishes.index') }}">奖惩记录</a><div class="arrow"></div></li>
    @endif
</ul>