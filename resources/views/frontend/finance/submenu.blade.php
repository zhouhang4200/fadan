<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.finance.asset' ? 'current' : '' }}">
        <a href="{{ route('frontend.finance.asset') }}">我的资产</a>
        <div class="arrow"></div>
    </li>
    <li class="{{ Route::currentRouteName() == 'frontend.finance.asset-daily' ? 'current' : '' }}">
        <a href="{{ route('frontend.finance.asset-daily') }}">资产日报</a>
        <div class="arrow"></div>
    </li>
    <li class="{{ Route::currentRouteName() == 'frontend.finance.amount-flow' ? 'current' : '' }}">
        <a href="{{ route('frontend.finance.amount-flow') }}">资金流水</a>
        <div class="arrow"></div>
    </li>
    <li class="{{ Route::currentRouteName() == 'frontend.finance.widthdraw-order' ? 'current' : '' }}">
        <a href="{{ route('frontend.finance.widthdraw-order') }}">我的提现</a>
        <div class="arrow"></div>
    </li>
</ul>