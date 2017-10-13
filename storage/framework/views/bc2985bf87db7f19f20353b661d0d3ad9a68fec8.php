<?php $__env->startSection('title', '商家后台'); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .layui-form-label {
            width:65px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!--START 主体-->
    <div class="main">
        <div class="wrapper">
            <?php echo $__env->make('frontend.layouts.account-left', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="right">
                <div class="content">

                    <div class="path"><span>修改权限组</span></div>

                    <div class="layui-tab">
                        
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="<?php echo e(route('rbacgroups.update', ['id' => $rbacGroup->id])); ?>">
                            <?php echo csrf_field(); ?>

                            <input type="hidden" name="_method" value="PUT">
                                <div style="width: 40%">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">权限组名:</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" lay-verify="title" value="<?php echo e(old('name') ?: $rbacGroup->name); ?>" autocomplete="off" placeholder="请输入组名" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">备注:</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="remark" lay-verify="" value="<?php echo e(old('remark') ?: $rbacGroup->remark); ?>" placeholder="备注可为空" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">权限清单</label>
                                        <div class="layui-input-block">
                                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <input type="checkbox" name="permissionIds[]" value="<?php echo e($permission->id); ?>" <?php echo e(in_array($permission->id, $rbacGroup->permissions->pluck('id')->toArray()) ? 'checked' : ''); ?>  title="<?php echo e($permission->alias); ?>">
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--END 主体-->
<?php $__env->stopSection(); ?>
<!--START 底部-->
<?php $__env->startSection('js'); ?>
<script>
layui.use('form', function(){
    var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
    var layer = layui.layer;

    var error = "<?php echo e($errors->count() > 0 ? '组名不可为空！' : ''); ?>";
    var missError = "<?php echo e(session('missError') ?: ''); ?>";

    if (error) {
        layer.msg(error, {icon: 5, time:1500},);
    } else if(missError) {
        layer.msg(missError, {icon: 5, time:1500},);
    }
  
  //……
  
  //但是，如果你的HTML是动态生成的，自动渲染就会失效
  //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
});  
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>