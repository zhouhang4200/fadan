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

	<li class="{{  Route::currentRouteName()  == 'frontend.automatically-grab.goods' ? 'current' : '' }}">
		<a href="{{ route('frontend.automatically-grab.goods') }}">抓取商品配置</a>
		<div class="arrow"></div>
	</li>

	@can('frontend.setting.sms.index')
		<li class="{{  Route::currentRouteName()  == 'frontend.setting.sms.index' ? 'current' : '' }}">
			<a href="{{ route('frontend.setting.sms.index') }}">短信管理</a>
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
