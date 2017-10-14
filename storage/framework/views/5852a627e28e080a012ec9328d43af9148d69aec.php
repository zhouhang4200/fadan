<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="_token" content="<?php echo e(csrf_token()); ?>" >
    <link rel="stylesheet" href="/vendor/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/css/style.css">
    <style>
        .pagination > li{
            float: left;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border: 1px dotted #ccc;
        }
        .pagination > .active {
            color: #fff;
            background: #139ff0;
        }
        .pagination > li a {
            display: block;
        }
    </style>
    <?php echo $__env->yieldContent('css'); ?>

</head>
<body>
<!--START 顶部菜单-->
<div class="header">
    <div class="wrapper">
        <a href="" class="logo">
            <div class="t">
                <h1>千手 · 订单集市</h1>
            </div>
            <div class="en"><img src="/frontend/images/en.png"></div>
        </a>
        <div class="nav">
            <ul>
                <li class="<?php echo e(Route::currentRouteName() == 'frontend.index' ? 'current' : ''); ?>" ><a href="<?php echo e(route('frontend.index')); ?>">首页</a><div class="arrow"></div></li>
                <li class=""><a href="">商品</a><div class="arrow"></div></li>
                <li class=""><a href="">财务</a><div class="arrow"></div></li>
                <li class="<?php echo e(in_array(Route::currentRouteName(), ['rbacgroups.index', 'rbacgroups.create']) ? 'current' : ''); ?>"><a href="<?php echo e(route('rbacgroups.index')); ?>">权限</a><div class="arrow"></div></li>
                <li class=""><a href="">工作台</a><div class="arrow"></div></li>
                <li class="<?php echo e(Route::currentRouteName() == 'accounts.index' || Route::currentRouteName() == 'accounts.create' || Route::currentRouteName() == 'loginrecord.index' ? 'current' : ''); ?>"><a href="<?php echo e(route('accounts.index')); ?>">账号</a><div class="arrow"></div></li>
            </ul>
        </div>
        <div class="user">
            <div class="operation">
                <a href=""><i class="iconfont icon-shezhi"></i>设置</a>
                <a href="javascript:(logout())" ><i class="iconfont icon-tuichu" style="font-size: 21px;top:1px"></i>注销登录</a>
            </div>
        </div>
    </div>
</div>
<!--END 顶部菜单-->
<!--START 主体-->
<?php echo $__env->yieldContent('content'); ?>

<!--END 主体-->

<!--START 底部-->
<div class="footer">
    <p>©&nbsp;2017-2018  福禄络科技有限公司，并保留所有权利。Powered by <span class="vol">s.dai.dev</span></p>
</div>
<!--END 底部-->

<script src="/vendor/layui/layui.js"></script>
<script src="/js/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    });

    function logout() {   
        layui.use(['form', 'layedit', 'laydate',], function(){
            var form = layui.form
            ,layer = layui.layer;
            layer.confirm('确定退出吗?', {icon: 3, title:'提示'}, function(index){
                $.post('/logout', {}, function(str){
                    window.location.href='/login';
                });  
                layer.close(index);
            });        
           
        });
    }
</script>
<?php echo $__env->yieldContent('js'); ?>
</body>
</html>