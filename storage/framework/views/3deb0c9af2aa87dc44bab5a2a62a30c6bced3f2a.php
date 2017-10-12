<div class="left">
    <div class="column-menu">
        <ul class="seller_center_left_menu">
            <li class="<?php echo e(Route::currentRouteName() == 'accounts.index' ? 'current' : ''); ?>"><a href="<?php echo e(route('accounts.index')); ?>"> 子账号列表 </a><div class="arrow"></div></li>
            <li class="<?php echo e(Route::currentRouteName() == 'accounts.create' ? 'current' : ''); ?>"><a href="<?php echo e(route('accounts.create')); ?>"> 添加子账号 </a><div class="arrow"></div></li>
            <li class="<?php echo e(Route::currentRouteName() == 'loginrecord.index' ? 'current' : ''); ?>"><a href="<?php echo e(route('loginrecord.index')); ?>"> 登录历史记录 </a><div class="arrow"></div></li>
        </ul>
    </div>
</div>