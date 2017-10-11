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

                    <div class="path"><span>添加子账号</span></div>

                    <div class="layui-tab">
                        
                        <div class="layui-tab-content">
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
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    });
    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function(){
        var element = layui.element;
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>