<ul class="seller_center_left_menu">
	@can('frontend.setting.receiving-control.index')
	    <li class="{{ substr(Route::currentRouteName(), 0, 34) == 'frontend.setting.receiving-control' ? 'current' : '' }}">
	        <a href="{{ route('frontend.setting.receiving-control.index') }}">接单设置</a>
	        <div class="arrow"></div>
	    </li>
	@endcan
	@can('frontend.setting.api-risk-management.index')
	<li class="{{ substr(Route::currentRouteName(), 0, 44) == 'frontend.setting.api-risk-management.index' ? 'current' : '' }}">
		<a href="{{ route('frontend.setting.api-risk-management.index') }}">API下单风控</a>
		<div class="arrow"></div>
	</li>
	@endcan
	@can('frontend.setting.skin.index')
		<li class="{{  Route::currentRouteName()  == 'frontend.setting.skin.index' ? 'current' : '' }}">
			<a href="{{ route('frontend.setting.skin.index') }}">皮肤交易QQ</a>
			<div class="arrow"></div>
		</li>
	@endcan
	@can('frontend.setting.tb-auth.index')
		<li class="{{  Route::currentRouteName()  == 'frontend.setting.tb-auth.index' ? 'current' : '' }}">
			<a href="{{ route('frontend.setting.tb-auth.index') }}">加款旺旺绑定</a>
			<div class="arrow"></div>
		</li>
	@endcan
	@can('frontend.setting.tb-auth.store')
		<li class="{{  Route::currentRouteName()  == 'frontend.setting.tb-auth.store' ? 'current' : '' }}">
			<a href="{{ route('frontend.setting.tb-auth.store') }}">店铺旺旺绑定</a>
			<div class="arrow"></div>
		</li>
	@endcan
</ul>
