<ul class="seller_center_left_menu">
    <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.goods' ? 'current' : '' }}">
        <a href="{{ route('frontend.goods.index') }}">商品列表</a>
        <div class="arrow"></div>
    </li>
</ul>
