<ul class="seller_center_left_menu">
    <li class="<?php echo e(Route::currentRouteName() == 'frontend.asset' ? 'current' : ''); ?>">
        <a href="<?php echo e(route('frontend.asset')); ?>">资产明细</a>
        <div class="arrow"></div>
    </li>
    <li class="<?php echo e(Route::currentRouteName() == 'frontend.asset.flow' ? 'current' : ''); ?>">
        <a href="<?php echo e(route('frontend.asset.flow')); ?>">资金流水</a>
        <div class="arrow"></div>
    </li>
</ul>