<ul class="seller_center_left_menu">
	@can('frontend.goods.index')
	    <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.goods' ? 'current' : '' }}">
	        <a href="{{ route('frontend.goods.index') }}">商品列表</a>
	        <div class="arrow"></div>
	    </li>
		<li class="{{ substr(Route::currentRouteName(), 0, 19) == 'frontend.goods.auth' ? 'current' : '' }}">
			<a href="{{ route('frontend.goods.auth.index') }}">接单权限</a>
			<div class="arrow"></div>
		</li>
	@endcan
</ul>
