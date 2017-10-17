<?php $__env->startSection('title', ' | 用户资产列表'); ?>

<?php $__env->startSection('content'); ?>
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">用户资产列表</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form id="search-flow" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="用户ID" name="user_id" value="<?php echo e($userId); ?>">
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">搜索</button>
                            </div>
                        </div>
                    </form>

                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>用户ID</th>
                            <th>剩余金额</th>
                            <th>冻结金额</th>
                            <th>累计平台加款</th>
                            <th>累计平台提现</th>
                            <th>累计平台消费</th>
                            <th>累计平台退款</th>
                            <th>累计交易支出</th>
                            <th>累计交易收入</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $dataList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($data->user_id); ?></td>
                                    <td><?php echo e($data->balance + 0); ?></td>
                                    <td><?php echo e($data->frozen + 0); ?></td>
                                    <td><?php echo e($data->total_recharge + 0); ?></td>
                                    <td><?php echo e($data->total_withdraw + 0); ?></td>
                                    <td><?php echo e($data->total_consume + 0); ?></td>
                                    <td><?php echo e($data->total_refund + 0); ?></td>
                                    <td><?php echo e($data->total_expend + 0); ?></td>
                                    <td><?php echo e($data->total_income + 0); ?></td>
                                    <td><?php echo e($data->created_at); ?></td>
                                    <td><?php echo e($data->updated_at); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo e($dataList->appends(['user_id' => $userId])->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
$('#date-start').datepicker();
$('#date-end').datepicker();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>