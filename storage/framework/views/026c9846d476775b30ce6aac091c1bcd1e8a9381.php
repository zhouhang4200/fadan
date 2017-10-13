<?php $__env->startSection('title', '注册'); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .input-container input {
            height:40px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form method="POST" action="<?php echo e(route('register')); ?>"  class="layui-form">
    <?php echo csrf_field(); ?>

        <div class="header">
            <div class="content">
                <div style="font-size: 23px;color:#2196f3;font-weight: 400">千手 · 订单集市</div>
            </div>
        </div>
        <div class="main">
            <div class="container">
                <div class="input-container">
                    <div class="title">注册</div>
                    <div class="layui-form-item">
                        <input type="text" name="name" required="" lay-verify="required" placeholder="请输入账号" value="<?php echo e(old('name')); ?>" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>
                    <div class="layui-form-item">
                        <input type="email" name="email" required="" lay-verify="required" placeholder="请输入邮箱" value="<?php echo e(old('email')); ?>" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password" required="" lay-verify="required" placeholder="请输入最少6位数密码" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon"> &#x1005;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password_confirmation" required="" lay-verify="required" placeholder="再次输入密码" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon"> &#x1005;</i>
                    </div>
                    <div class="layui-form-item ">
                        <?php echo Geetest::render(); ?>

                    </div>
                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" style="width: 100%">注 册</button>
                    </div>
                    <div class="register-and-forget-password">
                        <a class="register" target="_blank" href="<?php echo e(route('login')); ?>">登录</a>
                        <a class="forget-password" href="<?php echo e(route('password.request')); ?>">忘记密码？</a>
                        <div class="layui-clear"></div>
                    </div>
                </div>
                <?php echo $__env->make('frontend.layouts.domain', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
            ,layer = layui.layer;
          
            var errorName = "<?php echo e($errors->count() > 0 && array_key_exists('name', $errors->toArray()) && $errors->toArray()['name'] ? '用户名已经存在!' : ''); ?>";
            var errorPassword = "<?php echo e($errors->count() > 0 && array_key_exists('password', $errors->toArray()) && $errors->toArray()['password'] ? '请按要求填写密码!' : ''); ?>";
            var loginError = "<?php echo e(session('loginError') ? '异地登录异常！' : ''); ?>";
            var errorEmail = "<?php echo e($errors->count() > 0 && array_key_exists('email', $errors->toArray()) && $errors->toArray()['email'] ? '邮箱已经存在!' : ''); ?>";

            if (errorName) {
                layer.msg(errorName, {icon: 5, time:1500},);
            } else if(errorPassword) {
                layer.msg(errorPassword, {icon: 5, time:1500},);
            } else if (errorEmail) {
                layer.msg(errorEmail, {icon: 5, time:1500},);
            }else if (loginError) {
                layer.msg(loginError, {icon: 5, time:1500},);
            }

            //监听提交
            // form.on('submit(formDemo)', function(data){
                // var token=$('meta[name="_token"]').attr('content');
                // $.ajax({
                //     url: "<?php echo e(route('login')); ?>",
                //     data: {'_token':token} ,
                //     type: "post",
                //     dataType: "json",
                //     success: function (data) {
                //         console.log(1);
                //     },
                // });
            // }); 
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>