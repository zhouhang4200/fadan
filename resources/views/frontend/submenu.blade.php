<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.index' ? 'current' : '' }}"><a href="{{ route('frontend.index') }}">首页</a><div class="arrow"></div></li>
    @can('home-punishes.index')
        <li class="{{ Route::currentRouteName() == 'home-punishes.index' ? 'current' : '' }}"><a href="{{ route('home-punishes.index') }}">违规记录</a><div class="arrow"></div></li>
    @endcan
</ul>