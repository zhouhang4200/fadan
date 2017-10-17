<?php $__env->startSection('title', ' | 平台资金流水'); ?>

<?php $__env->startSection('content'); ?>
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">平台资金流水</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form id="search-flow" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="time-start" name="time_start" value="<?php echo e($timeStart); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="time-end" name="time_end" value="<?php echo e($timeEnd); ?>">
                                </div>
                            </div>
                            <div class="form-group col-md-1">
                                <select class="form-control" name="trade_type">
                                    <option value="">所有类型</option>
                                    <?php $__currentLoopData = config('tradetype.platform'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e($key == $tradeType ? 'selected' : ''); ?>><?php echo e($key); ?>. <?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <select class="form-control" name="trade_subtype">
                                    <option value="">所有子类型</option>
                                    <?php $__currentLoopData = config('tradetype.platform_sub'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e($key == $tradeSubtype ? 'selected' : ''); ?>><?php echo e($key); ?>. <?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="相关单号" name="trade_no" value="<?php echo e($tradeNo); ?>">
                            </div>
                            <div class="col-md-1">
                                <input type="text" class="form-control" placeholder="用户ID" name="user_id" value="<?php echo e($userId); ?>">
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
                            <th>流水号</th>
                            <th>用户</th>
                            <th>管理员</th>
                            <th>类型</th>
                            <th>子类型</th>
                            <th>相关单号</th>
                            <th>金额</th>
                            <th>备注</th>
                            <th>平台资金</th>
                            <th>平台托管</th>
                            <th>用户余额</th>
                            <th>用户冻结</th>
                            <th>累计用户加款</th>
                            <th>累计用户提现</th>
                            <th>累计用户消费</th>
                            <th>累计退款给用户</th>
                            <th>累计用户成交次数</th>
                            <th>累计用户成交金额</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $dataList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($data->id); ?></td>
                                    <td><?php echo e($data->user_id); ?></td>
                                    <td><?php echo e($data->admin_user_id); ?></td>
                                    <td><?php echo e(config('tradetype.platform')[$data->trade_type] ?? $data->trade_type); ?></td>
                                    <td><?php echo e(config('tradetype.platform_sub')[$data->trade_subtype] ?? $data->trade_subtype); ?></td>
                                    <td><?php echo e($data->trade_no); ?></td>
                                    <td><?php echo e($data->fee + 0); ?></td>
                                    <td><?php echo e($data->remark + 0); ?></td>
                                    <td><?php echo e($data->amount + 0); ?></td>
                                    <td><?php echo e($data->managed + 0); ?></td>
                                    <td><?php echo e($data->balance + 0); ?></td>
                                    <td><?php echo e($data->frozen + 0); ?></td>
                                    <td><?php echo e($data->total_recharge + 0); ?></td>
                                    <td><?php echo e($data->total_withdraw + 0); ?></td>
                                    <td><?php echo e($data->total_consume + 0); ?></td>
                                    <td><?php echo e($data->total_refund + 0); ?></td>
                                    <td><?php echo e($data->total_trade_quantity); ?></td>
                                    <td><?php echo e($data->total_trade_amount + 0); ?></td>
                                    <td><?php echo e($data->created_at); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo e($dataList->appends([
                        'user_id'       => $userId,
                        'trade_no'      => $tradeNo,
                        'trade_type'    => $tradeType,
                        'trade_subtype' => $tradeSubtype,
                        'time_start'    => $timeStart,
                        'time_end'      => $timeEnd,
                        ])->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
$('#time-start').datepicker();
$('#time-end').datepicker();

$('#export-flow').click(function () {
    var url = "<?php echo e(route('finance.platform-amount-flow.export')); ?>?" + $('#search-flow').serialize();
    window.location.href = url;
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>