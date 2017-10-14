<?php $__env->startSection('title', '账号 - 添加子账号'); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .layui-form-label {
            width:65px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('submenu'); ?>
<?php echo $__env->make('frontend.account.submenu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main'); ?>
<form class="layui-form" method="POST" action="<?php echo e(route('accounts.store')); ?>">
    <?php echo csrf_field(); ?>

    <div style="width: 40%">
        <div class="layui-form-item">
            <label class="layui-form-label">账号:</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="title" value="<?php echo e(old('name')); ?>" autocomplete="off" placeholder="请输入账号" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">邮箱:</label>
            <div class="layui-input-block">
                <input type="text" name="email" lay-verify="required" value="<?php echo e(old('email')); ?>" placeholder="邮箱可为空" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码:</label>
            <div class="layui-input-block">
                <input type="password" name="password" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码:</label>
            <div class="layui-input-block">
                <input type="password" name="password_confirmation" lay-verify="required" placeholder="请确认密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>
<!--START 底部-->
<?php $__env->startSection('js'); ?>
<script>

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>