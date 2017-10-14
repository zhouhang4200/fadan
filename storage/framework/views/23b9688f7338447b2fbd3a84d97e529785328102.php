<?php $__env->startSection('title', '财务 - 资产明细'); ?>

<?php $__env->startSection('submenu'); ?>
<?php echo $__env->make('frontend.asset.submenu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
<table class="layui-table">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>名称</th>
            <th>金额</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>账户余额</td>
            <td><?php echo e($asset->balance + 0); ?></td>
        </tr>
        <tr>
            <td>冻结金额</td>
            <td><?php echo e($asset->frozen + 0); ?></td>
        </tr>
        <tr>
            <td>累计加款</td>
            <td><?php echo e($asset->total_recharge + 0); ?></td>
        </tr>
        <tr>
            <td>累计提现</td>
            <td><?php echo e($asset->total_withdraw + 0); ?></td>
        </tr>
        <tr>
            <td>累计收入</td>
            <td><?php echo e($asset->total_refund + $asset->total_income); ?></td>
        </tr>
        <tr>
            <td>累计支出</td>
            <td><?php echo e($asset->total_consume + $asset->total_expend); ?></td>
        </tr>
    </tbody>
</table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>