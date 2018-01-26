<ul class="seller_center_left_menu">
	<li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.goods' ? 'current' : '' }}">
		<a href="{{ route('frontend.goods.index') }}">审核查询</a>
		<div class="arrow"></div>
	</li>

	<li class="{{ substr(Route::currentRouteName(), 0, 16) == 'frontend.examine' ? 'current' : '' }}">
		<a href="{{ route('frontend.examine.examine-goods') }}">商品列表</a>
		<div class="arrow"></div>
	</li>

</ul>
