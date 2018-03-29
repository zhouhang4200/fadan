<ul class="seller_center_left_menu">
	<li class="{{  Route::currentRouteName()  == 'frontend.order.receive' ? 'current' : '' }}">
		<a href="{{ route('frontend.order.receive') }}">接单列表</a>
		<div class="arrow"></div>
	</li>
	<li class="{{  Route::currentRouteName()  == 'frontend.order.send' ? 'current' : '' }}">
		<a href="{{ route('frontend.order.send') }}">发单列表</a>
		<div class="arrow"></div>
	</li>
</ul>
