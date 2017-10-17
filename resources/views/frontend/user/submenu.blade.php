<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'users.index' ? 'current' : '' }}"><a href="{{ route('users.index') }}"> 子账号列表 </a><div class="arrow"></div></li>
    <li class="{{ Route::currentRouteName() == 'login.history' ? 'current' : '' }}"><a href="{{ route('login.history') }}"> 登录记录 </a><div class="arrow"></div></li>
    <li class="{{ Route::currentRouteName() == 'login.child' ? 'current' : '' }}"><a href="{{ route('login.child') }}"> 子账号登录记录 </a><div class="arrow"></div></li>
</ul>