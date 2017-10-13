<div class="left">
    <div class="column-menu">
        <ul class="seller_center_left_menu">
            <li class="<?php echo e(Route::currentRouteName() == 'rbacgroups.index' ? 'current' : ''); ?>"><a href="<?php echo e(route('rbacgroups.index')); ?>"> 权限列表 </a><div class="arrow"></div></li>
            <!-- <li class="<?php echo e(Route::currentRouteName() == 'rbacgroups.create' ? 'current' : ''); ?>"><a href="<?php echo e(route('rbacgroups.create')); ?>"> 添加权限组 </a><div class="arrow"></div></li> -->
            <li class="<?php echo e(Route::currentRouteName() == 'roles.index' ? 'current' : ''); ?>"><a href="<?php echo e(route('roles.index')); ?>"> 角色列表 </a><div class="arrow"></div></li>
            <li class="<?php echo e(Route::currentRouteName() == 'roles.create' ? 'current' : ''); ?>"><a href="<?php echo e(route('roles.create')); ?>"> 添加角色 </a><div class="arrow"></div></li>
            <li class="<?php echo e(Route::currentRouteName() == 'permissions.index' ? 'current' : ''); ?>"><a href="<?php echo e(route('permissions.index')); ?>"> 权限列表 </a><div class="arrow"></div></li>
            <li class="<?php echo e(Route::currentRouteName() == 'permissions.create' ? 'current' : ''); ?>"><a href="<?php echo e(route('permissions.create')); ?>"> 添加权限 </a><div class="arrow"></div></li>
        </ul>
    </div>
</div>