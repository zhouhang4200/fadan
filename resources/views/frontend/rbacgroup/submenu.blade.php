<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'rbacgroups.index' ? 'current' : '' }}">
        <a href="{{ route('rbacgroups.index') }}"> 权限组列表 </a>
        <div class="arrow"></div>
    </li>
    <li class="{{ Route::currentRouteName() == 'user-groups.index' ? 'current' : '' }}">
        <a href="{{ route('user-groups.index') }}"> 用户权限组列表 </a>
        <div class="arrow"></div>
    </li>
</ul>