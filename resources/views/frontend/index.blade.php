@extends('frontend.layouts.app')

@section('title', '首页')

@section('css')
<style>
    .user-info{
        width: 970px;
        height: 120px;
        border: 1px solid #ddd;
        display: flex;
        margin: auto;
        font-size: 13px;
    }
    .info-img{
        width: 80px;
        height: 80px;
        margin: 20px 0 20px 20px;
        border: 1px solid #ddd;
    }
    .info-left{
        flex: 3;
        margin-top: 15px;
    }
    .layui-form-item{
        margin: 0;
    }
    .info-left .layui-form-item  .layui-inline .layui-input-inline{
        width: auto;
        text-indent: 20px;
    }
    .info-left .layui-form-item .layui-form-label{
        width: 80px;
        padding: 0;
        height: 30px;
        line-height: 30px;
    }
    .info-left .layui-form-item  .layui-inline{
        width: 250px;
        height: 30px;
        line-height: 30px;
    }
    .info-balance{
        flex: 1.2;
        height: 100px;
        margin: 8px 0 0 30px;
        position: relative;
    }
    .info-balance .available-balance{
        height: 33px;
        line-height: 34px;
    }
    .info-balance .blocked-balances{
        height: 33px;
        line-height: 33px;
    }
    .info-balance::before{
        content: "";
        position: absolute;
        left: -20px;
        top:20px;
        width: 1px;
        height: 70px;
        background-color: #ddd;
    }
    .icon{
        margin-left: 34px;
    }
    .icon > span + span{
        margin-left: 10px;
    }
</style>
@endsection


@section('submenu')
<ul class="seller_center_left_menu">
    <li class="{{ Route::currentRouteName() == 'frontend.index' ? 'current' : '' }}"><a href="{{ route('frontend.index') }}">首页</a><div class="arrow"></div></li>
</ul>
@endsection

@section('main')
<div class="user-info">
    <img src="" alt="" class="info-img fl">
    <div class="info-left">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">账号 ：</label>
                <div class="layui-input-inline">
                    13123213
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">账号&nbsp;&nbsp;&nbsp;ID ：</label>
                <div class="layui-input-inline">
                    111
                </div>
            </div>

        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">类型 ：</label>
                <div class="layui-input-inline">
                    111
                </div>
            </div>
            <div class="layui-inline" style="width:270px;">
                <label class="layui-form-label">最后登录 ：</label>
                <div class="layui-input-inline" style="margin-right:0;">
                    2017年10月24日11:57:01
                </div>
            </div>
        </div>
        <div class="layui-form-item icon">
            <span><i class="layui-icon">&#xe612;</i> 未实名认证</span>
            <span><i class="layui-icon">&#xe64c;</i> 交易密已设置</span>
        </div>
    </div>
    <div class="info-balance ">
        <div class="available-balance">可用余额： 2017</div>
        <div class="blocked-balances">冻结余额： 10086</div>

            <button class="layui-btn layui-btn-normal layui-btn-custom-mini">余额充值</button>
            <button class="layui-btn layui-btn-normal layui-btn-custom-mini">余额提现</button>
    </div>
</div>
<div class="layui-tab">
    <ul class="layui-tab-title">
        <li class="layui-this">网站设置</li>
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show" lay-size="sm">
            <table class="layui-table">
                <colgroup>
                    <col width="150">
                    <col width="200">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>昵称</th>
                    <th>加入时间</th>
                    <th>签名</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>贤心</td>
                    <td>2016-11-29</td>
                    <td>人生就像是一场修行</td>
                </tr>
                <tr>
                    <td>许闲心</td>
                    <td>2016-11-28</td>
                    <td>于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
