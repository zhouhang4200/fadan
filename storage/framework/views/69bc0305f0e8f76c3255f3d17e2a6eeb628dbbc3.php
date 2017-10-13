<?php $__env->startSection('title', '登录'); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .input-container input {
            height:40px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form method="POST" action="<?php echo e(route('login')); ?>"  class="layui-form">
    <?php echo csrf_field(); ?>

        <div class="header">
            <div class="content">
                <div style="font-size: 23px;color:#2196f3;font-weight: 400">千手 · 订单集市</div>
            </div>
        </div>
        <div class="main">
            <div class="container">
                <div class="input-container">
                    <div class="title">登录</div>
                    <div class="layui-form-item">
                        <input type="text" name="name" required="" lay-verify="required" placeholder="请输入账号" value="<?php echo e(old('name')); ?>" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password" required="" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon"> &#x1005;</i>
                    </div>
                    <div class="layui-form-item ">
                        <?php echo Geetest::render(); ?>

                    </div>
                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" style="width: 100%">登 录</button>
                    </div>
                    <div class="register-and-forget-password">
                        <a class="register" target="_blank" href="<?php echo e(route('register')); ?>">新用户注册</a>
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
          
            var error = "<?php echo e($errors->count() > 0 ? '账号或密码错误！' : ''); ?>";
            var loginError = "<?php echo e(session('loginError') ? '异地登录异常！' : ''); ?>";

            if (error) {
                layer.msg(error, {icon: 5, time:1500},);
            } else if(loginError) {
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