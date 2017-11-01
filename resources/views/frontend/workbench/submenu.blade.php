<ul class="seller_center_left_menu">
	@can('frontend.workbench.index')
	    <li class="{{ Route::currentRouteName() == 'frontend.workbench.index' ? 'current' : '' }}">
	        <a href="{{ route('frontend.workbench.index') }}">订单集市</a>
	        <div class="arrow"></div>
	    </li>
    @endcan
</ul>