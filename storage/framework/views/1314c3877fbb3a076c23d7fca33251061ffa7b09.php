<?php $__env->startSection('title', ' | 权限列表'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">权限列表</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>权限ID</th>
                                    <th>名称</th>
                                    <th>别名</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($permission->id); ?></td>
                                            <td><?php echo e($permission->name); ?></td>
                                            <td><?php echo e($permission->alias); ?></td>
                                            <td><?php echo e($permission->created_at); ?></td>
                                            <td style="text-align: center"><a href="<?php echo e(route('permissions.edit', ['id' => $permission->id])); ?>"><button class="layui-btn layui-btn layui-btn-normal layui-btn-small">编缉</button></a>
                                            <button class="layui-btn layui-btn layui-btn-normal layui-btn-small" click="del(<?php echo e($permission->id); ?>)">删除</button></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
            // 删除
        function del(id)
        {
            console.log(1);
             layui.use(['form', 'layedit', 'laydate',], function(){
                var form = layui.form
                ,layer = layui.layer;
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'DELETE',
                        url: '/admin/rbac/permissions/'+id,
                        success: function (data) {
                            console.log(data);
                            var obj = eval('(' + data + ')');
                            if (obj.code == 1) {
                                layer.msg('删除成功!', {icon: 6, time:1500},);
                                window.location.href = <?php echo e(route('permissions.index')); ?>;                    
                            } else {
                                layer.msg('删除失败!', {icon: 5, time:1500},);
                            }
                        }
                    });
                    layer.close(index);
                });        
               
            });
        }; 
        //Demo
        layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            var succ = "<?php echo e(session('succ') ?: ''); ?>";

            if(succ) {
                layer.msg(succ, {icon: 6, time:1500},);
            }

            //监听提交
            form.on('submit(formDemo)', function(data){
                layer.msg(JSON.stringify(data.field));
                return false;
            });
        });

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>