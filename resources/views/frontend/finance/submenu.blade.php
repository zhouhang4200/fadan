<ul class="seller_center_left_menu">
    @can('frontend.finance.asset')
        <li class="{{ Route::currentRouteName() == 'frontend.finance.asset' ? 'current' : '' }}">
            <a href="{{ route('frontend.finance.asset') }}">我的资产</a>
            <div class="arrow"></div>
        </li>
    @endcan
    @can('frontend.finance.asset-daily')
        <li class="{{ Route::currentRouteName() == 'frontend.finance.asset-daily' ? 'current' : '' }}">
            <a href="{{ route('frontend.finance.asset-daily') }}">资产日报</a>
            <div class="arrow"></div>
        </li>
    @endcan
    @can('frontend.finance.amount-flow')
        <li class="{{ Route::currentRouteName() == 'frontend.finance.amount-flow' ? 'current' : '' }}">
            <a href="{{ route('frontend.finance.amount-flow') }}">资金流水</a>
            <div class="arrow"></div>
        </li>
    @endcan
    @can('frontend.finance.withdraw-order')
        <li class="{{ Route::currentRouteName() == 'frontend.finance.withdraw-order' ? 'current' : '' }}">
            <a href="{{ route('frontend.finance.withdraw-order') }}">我的提现</a>
            <div class="arrow"></div>
        </li>
    @endcan
</ul>