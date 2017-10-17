<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.finance.asset' ? 'current' : '' }}">
        <a href="{{ route('frontend.finance.asset') }}">我的资产</a>
        <div class="arrow"></div>
    </li>
    <li class="{{ Route::currentRouteName() == 'frontend.finance.amount-flow' ? 'current' : '' }}">
        <a href="{{ route('frontend.finance.amount-flow') }}">资金流水</a>
        <div class="arrow"></div>
    </li>
</ul>