@extends('frontend.v1.layouts.app')

@section('title', '工作台-代练订单')

@section('css')
    <link rel="stylesheet" href="/frontend/css/bootstrap-fileinput.css">
    <style>
        .layui-layout-admin .layui-body {
            top: 50px;
        }

        .layui-layout-admin .layui-footer {
            height: 52px;
        }

        .footer {
            height: 72px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .main {
            padding: 20px;
        }

        .layui-footer {
            z-index: 999;
        }

        .layui-card-header {
            height: auto;
            border-bottom: none;
            padding-bottom: 0;
        }

        .layui-card .layui-tab {
            margin-top: 3px;
            margin-bottom: 12px;
        }
        .layui-form-item {
            margin-bottom: 12px;
        }
        .last-item{
            margin-bottom: 5px;
        }
        .layui-tab-title li{
            min-width: 50px;
            font-size: 12px;
        }
        .qsdate{
            float: left;
            width: 40% !important;
        }
        .layui-card-header{
            padding: 15px 15px 0 15px;;

        }
        .layui-card-body{
            padding-top: 0;
        }
        .layui-card .layui-tab-brief .layui-tab-content {
            padding: 0px;
        }
        /* 修改同意字体为12px */
        .last-item .last-item-btn {
            margin-left: 0;
        }
        @media screen and (max-width: 990px){
            .layui-col-md12 .layui-card .layui-card-header .layui-row .layui-form .first .layui-form-label{
                width: 80px;
                padding: 5px 10px;
                text-align: right;
            }
            .layui-col-md12 .first .layui-input-block{
                margin-left: 110px;
            }
            .last-item .last-item-btn {
                margin-left: 40px;
            }
        }
        /* 改写header高度 */
        .layui-card-header {
            font-size:12px;
        }
        .layui-table-edit {
            height: 50px;
        }

        .layui-layer-btn .layui-layer-btn0 {
            border-color: #ff8500;
            background-color: #ff8500;
            color: #fff;
        }
        .layui-table-edit:focus {
            border-color: #e6e6e6 !important
        }
    </style>
@endsection

@section('main')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">
                <div class="layui-row layui-col-space5">
                    <form class="layui-form" action="">
                        <div class="layui-col-md3 first">
                            <div class="layui-form-item">
                                <label class="layui-form-label" >订单单号</label>
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
                                    <select name="game_id" lay-search="" lay-filter="game">
                                        <option value="">请选择游戏</option>
                                        @foreach($game as  $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">代练类型</label>
                                <div class="layui-input-block">
                                    <select name="game_leveling_type" lay-search="">
                                        <option value="">请选择代练类型</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3 first">
                            <div class="layui-form-item ">
                                <label class="layui-form-label"  style="">发单客服</label>
                                <div class="layui-input-block" style="">
                                    <select name="customer_service_name" lay-search="">
                                        <option value="">请选择</option>
                                        @forelse($employee as $item)
                                            <option value="{{ $item->username }}">{{ $item->username }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3 ">
                            <div class="layui-form-item last-item">
                                <label class="layui-form-label">代练平台</label>
                                <div class="layui-input-block">
                                    <select name="platform">
                                        <option value="">全部</option>
                                        @foreach (config('partner.platform') as $key => $value)
                                            <option value="{{ $key }}">{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md4">
                            <div class="layui-form-item last-item">
                                <label class="layui-form-label">发布时间</label>
                                <div class="layui-input-block">
                                    <input type="text"  class="layui-input qsdate" id="test-laydate-start" name="start_date" placeholder="开始日期">
                                    <div class="layui-form-mid">
                                        -
                                    </div>
                                    <input type="text" class="layui-input qsdate" id="test-laydate-end" name="end_date" placeholder="结束日期">
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md2">
                            <div class="layui-form-item last-item">
                                <div class="layui-input-block last-item-btn">
                                    <button class="qs-btn" lay-submit="" lay-filter="search" style="height: 30px;line-height: 30px;float: left;font-size: 12px;">搜索</button>
                                    <button class="qs-btn" lay-submit="" lay-filter="export" style="margin-left:10px;height: 30px;line-height: 30px;float: left;font-size: 12px;">导出</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="layui-card-body">
                <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="0">全部 <span  class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span></li>
                        <li class="" lay-id="1">未接单
                            <span class="qs-badge quantity-1 layui-hide"></span>
                        </li>
                        <li class="" lay-id="13">代练中
                            <span class="qs-badge quantity-13 layui-hide"></span>
                        </li>
                        <li class="" lay-id="14">待验收
                            <span class="qs-badge quantity-14 layui-hide"></span>
                        </li>
                        <li class="" lay-id="15">撤销中
                            <span class="qs-badge quantity-15 layui-hide"></span>
                        </li>
                        <li class="" lay-id="16">仲裁中
                            <span class="qs-badge quantity-16 layui-hide"></span>
                        </li>
                        <li class="" lay-id="100">淘宝退款中
                            <span class="qs-badge quantity-100 layui-hide"></span>
                        </li>
                        <li class="" lay-id="17">异常
                            <span class="qs-badge quantity-17 layui-hide"></span>
                        </li>
                        <li class="" lay-id="18">锁定
                            <span class="qs-badge quantity-18 layui-hide"></span>
                        </li>
                        <li class="" lay-id="19">已撤销
                        </li>
                        <li class="" lay-id="20">已结算
                        </li>
                        <li class="" lay-id="21">已仲裁
                        </li>
                        <li class="" lay-id="22">已下架
                        </li>
                        <li class="" lay-id="24">已撤单
                        </li>
                    </ul>
                </div>
                <div id="order-list" lay-filter="order-list">
                </div>
            </div>
        </div>
    </div>
@endsection