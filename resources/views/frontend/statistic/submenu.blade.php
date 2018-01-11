<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.statistic.employee' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.employee') }}">员工统计</a><div class="arrow"></div></li>
	<li class="{{ Route::currentRouteName() == 'frontend.statistic.order' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.order') }}">订单统计</a><div class="arrow"></div></li>
	<li class="{{ Route::currentRouteName() == 'frontend.statistic.price' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.price') }}">加价统计</a><div class="arrow"></div></li>
	<li class="{{ Route::currentRouteName() == 'frontend.statistic.message' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.message') }}">短信统计</a><div class="arrow"></div></li>
</ul>