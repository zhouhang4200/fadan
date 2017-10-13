<div class="left">
    <div class="column-menu">
        <ul class="seller_center_left_menu">
            <li class="{{ Route::currentRouteName() == 'rbacgroups.index' ? 'current' : '' }}"><a href="{{ route('rbacgroups.index') }}"> 权限列表 </a><div class="arrow"></div></li>
            <!-- <li class="{{ Route::currentRouteName() == 'rbacgroups.create' ? 'current' : '' }}"><a href="{{ route('rbacgroups.create') }}"> 添加权限组 </a><div class="arrow"></div></li> -->
            <li class="{{ Route::currentRouteName() == 'roles.index' ? 'current' : '' }}"><a href="{{ route('roles.index') }}"> 角色列表 </a><div class="arrow"></div></li>
            <li class="{{ Route::currentRouteName() == 'roles.create' ? 'current' : '' }}"><a href="{{ route('roles.create') }}"> 添加角色 </a><div class="arrow"></div></li>
            <li class="{{ Route::currentRouteName() == 'permissions.index' ? 'current' : '' }}"><a href="{{ route('permissions.index') }}"> 权限列表 </a><div class="arrow"></div></li>
            <li class="{{ Route::currentRouteName() == 'permissions.create' ? 'current' : '' }}"><a href="{{ route('permissions.create') }}"> 添加权限 </a><div class="arrow"></div></li>
        </ul>
    </div>
</div>