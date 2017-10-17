<?php $__env->startSection('title', ' | 修改前台账号角色'); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .layui-tab-content input {
            width:800px;
        }
        .table {
            width:800px;
        }
        .layui-form-item .layui-input-inline {
            float: left;
            width: 135px;
            margin-right: 10px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">修改前台账号角色</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="<?php echo e(route('groups.update', ['userId' => $user->id])); ?>">
                            <?php echo csrf_field(); ?>

                            <input type="hidden" name="_method" value="PUT">
                                <div style="width: 40%">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">账号</label>
                                        <div class="layui-input-block">
                                            <input type="text" lay-verify="" value="<?php echo e($user->name); ?>" autocomplete="off" placeholder="请输入角色名" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">邮箱</label>
                                        <div class="layui-input-block">
                                            <input type="text" lay-verify="" value="<?php echo e($user->email); ?>" autocomplete="off" placeholder="请输入别名" class="layui-input">
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">前台角色</label>
                                            <div class="layui-input-block">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="col-md-1 text-center">前台角色名</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="layui-form-item" pane="">
                                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="layui-input-inline">
                                                              <input type="checkbox" name="roles[]" lay-skin="primary" title="<?php echo e($role->alias); ?>" value="<?php echo e($role->id); ?>" <?php echo e(in_array($role->id, $user->roles->pluck('id')->toArray()) ? 'checked' : ''); ?>>
                                                            </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        layui.use('form', function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;

        var error = "<?php echo e($errors->count() > 0 ? '用户名或别名已经存在！' : ''); ?>";
        var updateError = "<?php echo e(session('updateError') ?: ''); ?>";
        var missRole = "<?php echo e(session('missRole') ?: ''); ?>";

        if (error) {
            layer.msg(error, {icon: 5, time:1500},);
        } else if(updateError) {
            layer.msg(updateError, {icon: 5, time:1500},);
        } else if(missRole) {
            layer.msg(missRole, {icon: 5, time:1500},);
        }
  
      //……
      
      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
    });  


    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>