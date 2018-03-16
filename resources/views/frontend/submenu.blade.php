<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.index' ? 'current' : '' }}"><a href="{{ route('frontend.index') }}">首页</a><div class="arrow"></div></li>

    @can('frontend.workbench.recharge.index')
    @if(Auth::user()->parent_id == 0)
    	<li class="{{ Route::currentRouteName() == 'data.index' ? 'current' : '' }}"><a href="{{ route('data.index') }}">经营数据</a><div class="arrow"></div></li>
    @endif
    @endcan

    @can('home-punishes.index')
        <li class="{{ Route::currentRouteName() == 'home-punishes.index' ? 'current' : '' }}"><a href="{{ route('home-punishes.index') }}">奖惩记录</a><div class="arrow"></div></li>
    @endcan
</ul>