<?php $__env->startSection('title', ' | 平台资产日报'); ?>

<?php $__env->startSection('content'); ?>
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">平台资产日报</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form id="search-flow" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="date-start" name="date_start" value="<?php echo e($dateStart); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="date-end" name="date_end" value="<?php echo e($dateEnd); ?>">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">搜索</button>
                                <button class="btn btn-primary" type="button" id="export-flow">导出</button>
                            </div>
                        </div>
                    </form>

                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>日期</th>
                            <th>平台资金</th>
                            <th>平台托管</th>
                            <th>用户余额</th>
                            <th>用户冻结</th>
                            <th>当日用户加款</th>
                            <th>累计用户加款</th>
                            <th>当日用户提现</th>
                            <th>累计用户提现</th>
                            <th>当日用户消费</th>
                            <th>累计用户消费</th>
                            <th>当日退款给用户</th>
                            <th>累计退款给用户</th>
                            <th>当日用户成交次数</th>
                            <th>累计用户成交次数</th>
                            <th>当日用户成交</th>
                            <th>累计用户成交</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $dataList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($data->getattributes()['date']); ?></td>
                                    <td><?php echo e($data->amount + 0); ?></td>
                                    <td><?php echo e($data->managed + 0); ?></td>
                                    <td><?php echo e($data->balance + 0); ?></td>
                                    <td><?php echo e($data->frozen + 0); ?></td>
                                    <td><?php echo e($data->today_recharge + 0); ?></td>
                                    <td><?php echo e($data->total_recharge + 0); ?></td>
                                    <td><?php echo e($data->today_withdraw + 0); ?></td>
                                    <td><?php echo e($data->total_withdraw + 0); ?></td>
                                    <td><?php echo e($data->today_consume + 0); ?></td>
                                    <td><?php echo e($data->total_consume + 0); ?></td>
                                    <td><?php echo e($data->today_refund + 0); ?></td>
                                    <td><?php echo e($data->total_refund + 0); ?></td>
                                    <td><?php echo e($data->today_trade_quantity + 0); ?></td>
                                    <td><?php echo e($data->total_trade_quantity + 0); ?></td>
                                    <td><?php echo e($data->today_trade_amount + 0); ?></td>
                                    <td><?php echo e($data->total_trade_amount + 0); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo e($dataList->appends(['date_start' => $dateStart, 'date_end' => $dateEnd])->links()); ?>

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

$('#export-flow').click(function () {
    var url = "<?php echo e(route('finance.platform-asset-daily.export')); ?>?" + $('#search-flow').serialize();
    window.location.href = url;
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>