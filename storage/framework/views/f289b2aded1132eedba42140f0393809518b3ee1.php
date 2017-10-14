<?php $__env->startSection('title', '账号 - 登陆记录'); ?>

<?php $__env->startSection('submenu'); ?>
<?php echo $__env->make('frontend.account.submenu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<form class="layui-form" method="" action="">
    <div class="layui-inline" style="float:left">
        <div class="layui-form-item">
            <label class="layui-form-label">账号名</label>
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
    </div>
    <div style="float: left">
        <div class="layui-inline" >
            <button class="layui-btn" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
            <button  class="layui-btn"><a href="<?php echo e(route('loginrecord.index')); ?>" style="color:#fff">返回</a></button>
        </div>
    </div>
</form>

<div class="layui-tab-item layui-show" lay-size="sm">
    <table class="layui-table">
        <colgroup>
            <col width="150">
            <col width="200">
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>ID</th>
            <th>用户ID</th>
            <th>用户名</th>
            <th>登录IP</th>
            <th>登录城市</th>
            <th>登录时间</th>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $loginRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loginRecord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loginRecord->id); ?></td>
                <td><?php echo e($loginRecord->user_id); ?></td>
                <td><?php echo e($loginRecord->user->name); ?></td>
                <td><?php echo e(long2ip($loginRecord->ip)); ?></td>
                <td><?php echo e($loginRecord->city ? $loginRecord->city->name : ''); ?></td>
                <td><?php echo e($loginRecord->created_at); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<?php echo $loginRecords->appends([
'name' => $name,
'startDate' => $startDate,
'endDate' => $endDate,
])->render(); ?>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
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

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>