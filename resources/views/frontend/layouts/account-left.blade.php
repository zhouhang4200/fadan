<div class="left">
    <div class="column-menu">
        <ul class="seller_center_left_menu">
            <li class="{{ Route::currentRouteName() == 'accounts.index' ? 'current' : '' }}"><a href="{{ route('accounts.index') }}"> 子账号列表 </a><div class="arrow"></div></li>
            <li class="{{ Route::currentRouteName() == 'accounts.create' ? 'current' : '' }}"><a href="{{ route('accounts.create') }}"> 添加子账号 </a><div class="arrow"></div></li>
            <li class="{{ Route::currentRouteName() == 'loginrecord.index' ? 'current' : '' }}"><a href="{{ route('loginrecord.index') }}"> 登录历史 </a><div class="arrow"></div></li>
        </ul>
    </div>
</div>