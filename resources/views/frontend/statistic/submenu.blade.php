<ul class="seller_center_left_menu">
@if(Auth::user()->could('frontend.statistic.employee'))
    <li class="{{ Route::currentRouteName() == 'frontend.statistic.employee' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.employee') }}">员工统计</a><div class="arrow"></div></li>
@endif
@if(Auth::user()->could('frontend.statistic.order'))
	<li class="{{ Route::currentRouteName() == 'frontend.statistic.order' ? 'current' : '' }}"><a href="{{ route('frontend.statistic.order') }}">订单统计</a><div class="arrow"></div></li>
@endif
@if(Auth::user()->could('frontend.statistic.sms'))
	<li class="{{ in_array(Route::currentRouteName() , ['frontend.statistic.sms', 'frontend.statistic.show']) ? 'current' : '' }}"><a href="{{ route('frontend.statistic.sms') }}">短信统计</a><div class="arrow"></div></li>
@endif
</ul>