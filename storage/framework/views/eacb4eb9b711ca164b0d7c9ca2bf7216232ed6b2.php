<?php $__env->startSection('title', '商家后台'); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('/css/index.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!--START 主体-->
    <div class="main">
        <div class="wrapper">
            <?php echo $__env->make('frontend.layouts.account-left', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <div class="right">
                <div class="content">

                    <div class="path"><span>子账号列表</span></div>

                    <div class="layui-tab">
                        <div class="layui-tab-content">
                        <form class="layui-form" method="" action="">
                            <div class="layui-inline" >
                                <div class="layui-form-item" style="float: left">
                                    <label class="layui-form-label">用户名</label>
                                    <div class="layui-input-inline">
                                    <input type="text" name="name" value="<?php echo e($name ?: ''); ?>" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                                    </div> 

                                      <label class="layui-form-label">开始时间</label>
                                      <div class="layui-input-inline">
                                            <input type="text" class="layui-input" value="<?php echo e($startDate ?: null); ?>" name="startDate" id="test1" placeholder="年-月-日">
                                      </div>

                                      <label class="layui-form-label">结束时间</label>
                                      <div class="layui-input-inline">
                                            <input type="text" class="layui-input" value="<?php echo e($endDate ?: null); ?>"  name="endDate" id="test2" placeholder="年-月-日">
                                      </div>
                                </div>
                                <div style="float: left">
                                <button class="layui-btn" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                                <button  class="layui-btn"><a href="<?php echo e(route('accounts.index')); ?>" style="color:#fff">返回</a></button></div>
                            </div>                     
                        </form>
                            <div class="layui-tab-item layui-show" lay-size="sm">
                                <table class="layui-table" lay-size="sm">
                                    <colgroup>
                                        <col width="150">
                                        <col width="200">
                                        <col>
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>用户ID</th>
                                        <th>用户名</th>
                                        <th>邮箱</th>
                                        <th>注册时间</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="account-td">
                                            <td><?php echo e($account->id); ?></td>
                                            <td><?php echo e($account->name); ?></td>
                                            <td><?php echo e($account->email); ?></td>
                                            <td><?php echo e($account->created_at); ?></td>
                                            <td>
                                                <div style="text-align: center">
                                                <button class="layui-btn edit"><a href="<?php echo e(route('accounts.edit', ['id' => $account->id])); ?>" style="color: #fff">编辑</a></button>
                                                <button class="layui-btn delete" onclick="del(<?php echo e($account->id); ?>)">删除</button>
                                                <button class="layui-btn rbac"><a href="<?php echo e(route('rbacgroups.create')); ?>" style="color: #fff">权限</a></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>                               
                            </div>
                        </div>
                        <?php echo $accounts->appends([
                            'name' => $name,
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                        ])->render(); ?>

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
    // 时间插件
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        //常规用法
        laydate.render({
        elem: '#test1'
        });

        //常规用法
        laydate.render({
        elem: '#test2'
        });
    });
    
    // 删除
    function del(id)
    {
         layui.use(['form', 'layedit', 'laydate',], function(){
            var form = layui.form
            ,layer = layui.layer;
            layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                $.ajax({
                    type: 'DELETE',
                    url: '/accounts/'+id,
                    success: function (data) {
                        console.log(data);
                        var obj = eval('(' + data + ')');
                        if (obj.code == 1) {
                            window.location.href = '/accounts';                    
                        } else {
                            layer.msg('删除失败', {icon: 5, time:1500},);
                        }
                    }
                });
                layer.close(index);
            });        
           
        });
    };

    // 权限

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>