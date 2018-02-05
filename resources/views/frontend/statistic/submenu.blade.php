<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.statistic.employee' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.employee') }}">员工统计</a><div class="arrow"></div></li>
	<li class="{{ Route::currentRouteName() == 'frontend.statistic.order' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.order') }}">订单统计</a><div class="arrow"></div></li>
	<li class="{{ in_array(Route::currentRouteName() , ['frontend.statistic.sms', 'frontend.statistic.show']) ? 'current' : '' }}"><a href="{{ route('frontend.statistic.sms') }}">短信统计</a><div class="arrow"></div></li>
</ul>