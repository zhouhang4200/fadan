<ul class="seller_center_left_menu">
	@if(Auth::user()->could('frontend.setting.sending-control.index'))
	    <li class="{{ substr(Route::currentRouteName(), 0, 32) == 'frontend.setting.sending-control' ? 'current' : '' }}">
	        <a href="{{ route('frontend.setting.sending-control.index') }}">发单设置</a>
	        <div class="arrow"></div>
	    </li>
	@endif
	@if(Auth::user()->could('frontend.setting.receiving-control.index'))
	    <li class="{{ substr(Route::currentRouteName(), 0, 34) == 'frontend.setting.receiving-control' ? 'current' : '' }}">
	        <a href="{{ route('frontend.setting.receiving-control.index') }}">接单设置</a>
	        <div class="arrow"></div>
	    </li>
	@endif
	@if(Auth::user()->could('frontend.setting.api-risk-management.index'))
	<li class="{{ substr(Route::currentRouteName(), 0, 44) == 'frontend.setting.api-risk-management.index' ? 'current' : '' }}">
		<a href="{{ route('frontend.setting.api-risk-management.index') }}">API下单风控</a>
		<div class="arrow"></div>
	</li>
	@endif
	@if(Auth::user()->could('frontend.setting.skin.index'))
		<li class="{{  Route::currentRouteName()  == 'frontend.setting.skin.index' ? 'current' : '' }}">
			<a href="{{ route('frontend.setting.skin.index') }}">皮肤交易QQ</a>
			<div class="arrow"></div>
		</li>
	@endif

	<li class="{{  Route::currentRouteName()  == 'frontend.automatically-grab.goods' ? 'current' : '' }}">
		<a href="{{ route('frontend.automatically-grab.goods') }}">抓取商品配置</a>
		<div class="arrow"></div>
	</li>

	@if(Auth::user()->could('frontend.setting.sms.index'))
		<li class="{{  Route::currentRouteName()  == 'frontend.setting.sms.index' ? 'current' : '' }}">
			<a href="{{ route('frontend.setting.sms.index') }}">短信管理</a>
			<div class="arrow"></div>
		</li>
	@endif

	@if(Auth::user()->could('frontend.setting.tb-auth.store'))
		<li class="{{  Route::currentRouteName()  == 'frontend.setting.tb-auth.store' ? 'current' : '' }}">
			<a href="{{ route('frontend.setting.tb-auth.store') }}">店铺旺旺绑定</a>
			<div class="arrow"></div>
		</li>
	@endif

	@if(Auth::user()->could('frontend.setting.sending-assist.require'))
		<li class="{{  substr(Route::currentRouteName(), 0, 31)  == 'frontend.setting.sending-assist' ? 'current' : '' }}">
			<a href="{{ route('frontend.setting.sending-assist.require') }}">代练发单辅助</a>
			<div class="arrow"></div>
		</li>
	@endif
</ul>
