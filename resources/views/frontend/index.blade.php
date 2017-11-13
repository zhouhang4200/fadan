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
        background-size: 102%;
        background-image: url('/frontend/images/3.png');
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
    @include('frontend.submenu')
@endsection

@section('main')
<div class="user-info">
    <div src="" alt="" class="info-img fl"></div>
    <div class="info-left">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">账号 ：</label>
                <div class="layui-input-inline">
                    {{ $user->name }}
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">主账号ID ：</label>
                <div class="layui-input-inline">
                    {{ Auth::user()->getPrimaryUserId() }}
                </div>
            </div>

        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">类型 ：</label>
                <div class="layui-input-inline">
                    @if ($user->parent_id == 0)
                        主账号
                    @else
                        子账号
                    @endif
                </div>
            </div>
            <div class="layui-inline" style="width:270px;">
                <label class="layui-form-label">最后登录 ：</label>
                <div class="layui-input-inline" style="margin-right:0;">
                    {{ $loginHistoryTime }}
                </div>
            </div>
        </div>
        <div class="layui-form-item icon">
        @if ($ident && $ident->status == 1)
            <span><i class="layui-icon">&#xe612;</i> 已实名认证</span>
        @elseif ($ident && $ident->status == 2)
            <span><i class="layui-icon">&#xe612;</i> 实名认证未通过</span>
        @elseif (! $ident && $user->parent_id == 0)
            <span><i class="layui-icon">&#xe612;</i> <a href="{{ route('idents.create') }}">未实名认证</a></span>
        @else
            <span><i class="layui-icon">&#xe612;</i>未实名认证</span>
        @endif
        </div>
    </div>
    <div class="info-balance ">
        <div class="available-balance">可用余额：
            {{ $user->userAsset->balance + 0 }}
        </div>
        <div class="blocked-balances">冻结余额：
            {{ $user->userAsset->frozen + 0 }}
         </div>

        <button class="layui-btn layui-btn-normal layui-btn-custom-mini">余额充值</button>
        @inject('withdraw', 'App\Services\Views\WithdrawService')
        {{ $withdraw->button('余额提现', 'layui-btn layui-btn-normal layui-btn-custom-mini') }}
    </div>
</div>
<div class="layui-tab layui-hide">
    <ul class="layui-tab-title">
        <li class="layui-this">昨日接单数据</li>
        <li class="">网站设置</li>
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show" >

        </div>
        <div class="layui-tab-item" >
            <table class="layui-table" lay-size="sm">
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

@section('js')
@endsection