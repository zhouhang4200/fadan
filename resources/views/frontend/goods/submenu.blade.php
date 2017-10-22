<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.goods.index' ? 'current' : '' }}">
        <a href="{{ route('frontend.goods.index') }}">商品列表</a>
        <div class="arrow"></div>
    </li>
</ul>