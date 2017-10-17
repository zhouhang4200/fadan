<?php $__env->startSection('title', ' | 平台当前资产'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">平台当前资产</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th width="135px">类目</th>
                                    <th>金额</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>平台资金</td>
                                        <td><?php echo e($platformAsset->amount + 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td>平台托管资金</td>
                                        <td><?php echo e($platformAsset->managed + 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td>用户总余额</td>
                                        <td><?php echo e($platformAsset->balance + 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td>用户总冻结</td>
                                        <td><?php echo e($platformAsset->frozen + 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td>累计用户加款</td>
                                        <td><?php echo e($platformAsset->total_recharge + 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td>累计用户提现</td>
                                        <td><?php echo e($platformAsset->total_withdraw + 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td>累计用户消费</td>
                                        <td><?php echo e($platformAsset->total_consume + 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td>累计退款给用户</td>
                                        <td><?php echo e($platformAsset->total_refund + 0); ?></td>
                                    </tr>
                                    <tr>
                                        <td>累计用户成交次数</td>
                                        <td><?php echo e($platformAsset->total_trade_quantity); ?></td>
                                    </tr>
                                    <tr>
                                        <td>累计用户成交金额</td>
                                        <td><?php echo e($platformAsset->total_trade_amount + 0); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>