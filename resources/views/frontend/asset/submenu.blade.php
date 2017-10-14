<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.asset' ? 'current' : '' }}">
        <a href="{{ route('frontend.asset') }}">资产明细</a>
        <div class="arrow"></div>
    </li>
    <li class="{{ Route::currentRouteName() == 'frontend.asset.flow' ? 'current' : '' }}">
        <a href="{{ route('frontend.asset.flow') }}">资金流水</a>
        <div class="arrow"></div>
    </li>
</ul>