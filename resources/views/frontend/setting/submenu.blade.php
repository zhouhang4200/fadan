<ul class="seller_center_left_menu">
	@can('frontend.goods.index')
	    <li class="{{ substr(Route::currentRouteName(), 0, 34) == 'frontend.setting.receiving-control' ? 'current' : '' }}">
	        <a href="{{ route('frontend.setting.receiving-control.index') }}">接单设置</a>
	        <div class="arrow"></div>
	    </li>
	@endcan
</ul>
