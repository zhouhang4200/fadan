<?php $__env->startSection('title', ' | 后端账号'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">后端账号</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>账号ID</th>
                                    <th>名称</th>
                                    <th>邮箱</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $adminUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adminUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($adminUser->id); ?></td>
                                            <td><?php echo e($adminUser->name); ?></td>
                                            <td><?php echo e($adminUser->email); ?></td>
                                            <td><?php echo e($adminUser->created_at); ?></td>
                                            <td style="text-align: center;"><a href="<?php echo e(route('admin-groups.create', ['id' => $adminUser->id])); ?>"><button class="layui-btn layui-btn layui-btn-normal layui-btn-small">添加权限</button></a></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <?php echo $adminUsers->render(); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        //Demo
        layui.use('form', function(){
            var form = layui.form;

            //监听提交
            form.on('submit(formDemo)', function(data){
                layer.msg(JSON.stringify(data.field));
                return false;
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>