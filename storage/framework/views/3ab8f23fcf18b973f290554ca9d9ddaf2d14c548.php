<?php $__env->startSection('title', '| 图表'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb">
                        <li><a href="#">主页</a></li>
                        <li class="active"><span>图表</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="main-box infographic-box">
                <i class="fa fa-user red-bg"></i>
                <span class="headline">Users</span>
                <span class="value">2.562</span>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="main-box infographic-box">
                <i class="fa fa-shopping-cart emerald-bg"></i>
                <span class="headline">Purchases</span>
                <span class="value">658</span>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="main-box infographic-box">
                <i class="fa fa-money green-bg"></i>
                <span class="headline">Income</span>
                <span class="value">$8.400</span>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="main-box infographic-box">
                <i class="fa fa-eye yellow-bg"></i>
                <span class="headline">Monthly Visits</span>
                <span class="value">12.526</span>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('js'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>