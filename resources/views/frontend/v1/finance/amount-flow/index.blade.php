@extends('frontend.v1.layouts.app')

@section('title', '工作台-代练订单')

@section('breadcrumb')
    <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
        <a>
            <cite>财务</cite>
        </a>
        <span lay-separator="">/</span>
        <a>
            <cite>资金流水</cite>
        </a>
    </div>
@endsection

@section('css')

@endsection

@section('main')
    <div class="layui-card-header" style="padding-top: 20px;">
        <div class="layui-row layui-col-space5">
            <form class="layui-form" action="">
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="">订单单号</label>
                        <div class="layui-input-block" style="">
                            <input type="text" name="no" lay-verify="title" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">玩家旺旺</label>
                        <div class="layui-input-block">
                            <input type="text" name="wang_wang" lay-verify="title" autocomplete="off" placeholder="请输入玩家旺旺" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">代练游戏</label>
                        <div class="layui-input-block">
                            <select name="game_id" lay-search="">
                                <option value="">请选择游戏</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label"  style="">发单客服</label>
                        <div class="layui-input-block" style="">
                            <select name="customer_service_name" lay-search="">
                                <option value="">请选择</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">代练平台</label>
                        <div class="layui-input-block">
                            <select name="platform">
                                <option value="">全部</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md4">
                    <div class="layui-form-item">
                        <label class="layui-form-label">发布时间</label>
                        <div class="layui-input-block">
                            <input type="text"  class="layui-input qsdate" id="test-laydate-start" placeholder="开始日期">
                            <div class="layui-form-mid" style="float:none;display: inline-block;width: 8%;text-align: center;margin:0;">
                                -
                            </div>
                            <input type="text" class="layui-input qsdate" id="test-laydate-end" placeholder="结束日期">
                        </div>
                    </div>
                </div>
                <div class="layui-col-md2">
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin-left: 40px;">
                            <button class="qs-btn" lay-submit="" lay-filter="search">搜索</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="layui-card-body">
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
@endsection

@section('pop')

@endsection

@section('js')

@endsection