<ul class="seller_center_left_menu">
    @if(Auth::user()->could('frontend.finance.asset'))
        <li class="{{ Route::currentRouteName() == 'frontend.finance.asset' ? 'current' : '' }}">
            <a href="{{ route('frontend.finance.asset') }}">我的资产</a>
            <div class="arrow"></div>
        </li>
    @endif
    @if(Auth::user()->could('frontend.finance.asset-daily'))
        <li class="{{ Route::currentRouteName() == 'frontend.finance.asset-daily' ? 'current' : '' }}">
            <a href="{{ route('frontend.finance.asset-daily') }}">资产日报</a>
            <div class="arrow"></div>
        </li>
    @endif
    @if(Auth::user()->could('frontend.finance.amount-flow'))
        <li class="{{ Route::currentRouteName() == 'frontend.finance.amount-flow' ? 'current' : '' }}">
            <a href="{{ route('frontend.finance.amount-flow') }}">资金流水</a>
            <div class="arrow"></div>
        </li>
    @endif
    @if(Auth::user()->could('frontend.finance.withdraw-order'))
        <li class="{{ Route::currentRouteName() == 'frontend.finance.withdraw-order' ? 'current' : '' }}">
            <a href="{{ route('frontend.finance.withdraw-order') }}">我的提现</a>
            <div class="arrow"></div>
        </li>
    @endif
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