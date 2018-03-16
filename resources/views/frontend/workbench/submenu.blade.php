<ul class="seller_center_left_menu">
	@can('frontend.workbench.index')
	<li class="{{ Route::currentRouteName() == 'frontend.workbench.index' ? 'current' : '' }}">
		<a href="{{ route('frontend.workbench.index') }}">代充订单</a>
		<div class="arrow"></div>
	</li>
	{{--<li class="{{ Route::currentRouteName() == 'frontend.workbench.recharge.get' ? 'current' : '' }}">--}}
		{{--<a href="{{ route('frontend.workbench.index') }}">代充发布</a>--}}
		{{--<div class="arrow"></div>--}}
	{{--</li>--}}
	@endcan

	@can('frontend.workbench.leveling.index')
	<li class="{{ Route::currentRouteName() == 'frontend.workbench.leveling.index' ? 'current' : '' }}">
		<a href="{{ route('frontend.workbench.leveling.index') }}">代练订单</a>
		<div class="arrow"></div>
	</li>
	<li class="{{ Route::currentRouteName() == 'frontend.workbench.leveling.create' ? 'current' : '' }}">
		<a href="{{ route('frontend.workbench.leveling.create') }}">代练发布</a>
		<div class="arrow"></div>
	</li>
	<li class="{{ Route::currentRouteName() == 'frontend.workbench.leveling.wait' ? 'current' : '' }}">
		<a href="{{ route('frontend.workbench.leveling.wait') }}">代练待发</a>
		<div class="arrow"></div>
	</li>
	@endcan
</ul>