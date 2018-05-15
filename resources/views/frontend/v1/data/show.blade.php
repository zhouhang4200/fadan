@extends('frontend.v1.layouts.app')

@section('title', '首页 - 日常数据')

@section('css')
    <style>
        .layui-form-label {
            width:150px;
            text-align: left;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
        <form class="layui-form" method="" action="">
            <div class="layui-inline" style="float:left">

                <div class="layui-form-item">               
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="开始时间">
                    </div>

                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="结束时间">
                    </div>
                </div>
            </div>
            <div class="layui-inline" >
                <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1">查找</button>
                <a href="{{ route('data.index') }}" class="layui-btn layui-btn-normal layui-btn-small">返回</a>
            </div>
        </form>

        <div class="layui-tab-item layui-show" style="margin-top: 30px;">

            @forelse($datas as $data) 
                <table class="layui-table" lay-size="sm">
                    @if($data->type == 3)
                        <div  style="float: left"><label class="layui-form-label">发单+接单数量和占比</label></div>
                    @elseif($data->type == 1)
                        <div  style="float: left"><label class="layui-form-label">接单数量和占比</label></div>
                    @elseif($data->type == 2)
                        <div  style="float: left"><label class="layui-form-label">发单数量和占比</label></div>
                    @endif
                    <thead>
                    <tr>
                        <th style="width:12px">来源</th>
                        <th style="width:12px">订单数</th>
                        <th>等待商户接单</th>
                        <th>系统分配中</th>
                        <th>商户已接单</th>
                        <th>已发货</th>
                        <th>已失败</th>
                        <th>售后中</th>
                        <th>售后完成</th>
                        <th>订单完成</th>
                        <th>已取消</th>
                        <th>未付款</th>
                        <th>最多游戏</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($data->type == 3 && $data->source === 0)
                        <tr>
                            <td>所有</td>
                            <td>{{ $data->total }}</td>
                            <td>{{ $data->waite_user_receive }} ({{ @bcdiv($data->waite_user_receive, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->distributing }} ({{ @bcdiv($data->distributing, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->received }} ({{ @bcdiv($data->received, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->sended }} ({{ @bcdiv($data->sended, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->failed }} ({{ @bcdiv($data->failed, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->after_saling }} ({{ @bcdiv($data->after_saling, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->after_saled }} ({{ @bcdiv($data->after_saled, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->successed }} ({{ @bcdiv($data->successed, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->canceled }} ({{ @bcdiv($data->canceled, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->waite_pay }} ({{ @bcdiv($data->waite_pay, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->most_game_name }} ({{ $data->most_game_amount }}, {{ @bcdiv($data->most_game_amount, $data->total, 3) * 100 }}%)</td>
                        </tr>
                    @elseif($data->type == 3 && $data->source != 0 && $data->source)
                        <tr>
                            <td>{{ config('order.source')[$data->source] }}</td>
                            <td>{{ $data->total }}</td>
                            <td>{{ $data->waite_user_receive }} ({{ @bcdiv($data->waite_user_receive, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->distributing }} ({{ @bcdiv($data->distributing, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->received }} ({{ @bcdiv($data->received, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->sended }} ({{ @bcdiv($data->sended, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->failed }} ({{ @bcdiv($data->failed, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->after_saling }} ({{ @bcdiv($data->after_saling, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->after_saled }} ({{ @bcdiv($data->after_saled, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->successed }} ({{ @bcdiv($data->successed, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->canceled }} ({{ @bcdiv($data->canceled, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->waite_pay }} ({{ @bcdiv($data->waite_pay, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->most_game_name }} ({{ $data->most_game_amount }}, {{ @bcdiv($data->most_game_amount, $data->total, 3) * 100 }}%)</td>
                        </tr>
                    @elseif($data->type == 1 && $data->source != 0 && $data->source)
                        <tr>
                            <td>{{ config('order.source')[$data->source] }}</td>
                            <td>{{ $data->total }}</td>
                            <td>{{ $data->waite_user_receive }} ({{ @bcdiv($data->waite_user_receive, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->distributing }} ({{ @bcdiv($data->distributing, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->received }} ({{ @bcdiv($data->received, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->sended }} ({{ @bcdiv($data->sended, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->failed }} ({{ @bcdiv($data->failed, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->after_saling }} ({{ @bcdiv($data->after_saling, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->after_saled }} ({{ @bcdiv($data->after_saled, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->successed }} ({{ @bcdiv($data->successed, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->canceled }} ({{ @bcdiv($data->canceled, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->waite_pay }} ({{ @bcdiv($data->waite_pay, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->most_game_name }} ({{ $data->most_game_amount }}, {{ @bcdiv($data->most_game_amount, $data->total, 3) * 100 }}%)</td>                           
                        </tr>
                    @elseif($data->type == 2 && $data->source != 0 && $data->source)
                        <tr>
                            <td>{{ config('order.source')[$data->source] }}</td>
                            <td>{{ $data->total }}</td>
                            <td>{{ $data->waite_user_receive }} ({{ @bcdiv($data->waite_user_receive, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->distributing }} ({{ @bcdiv($data->distributing, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->received }} ({{ @bcdiv($data->received, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->sended }} ({{ @bcdiv($data->sended, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->failed }} ({{ @bcdiv($data->failed, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->after_saling }} ({{ @bcdiv($data->after_saling, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->after_saled }} ({{ @bcdiv($data->after_saled, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->successed }} ({{ @bcdiv($data->successed, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->canceled }} ({{ @bcdiv($data->canceled, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->waite_pay }} ({{ @bcdiv($data->waite_pay, $data->total, 3) * 100 }}%)</td>
                            <td>{{ $data->most_game_name }} ({{ $data->most_game_amount }}, {{ @bcdiv($data->most_game_amount, $data->total, 3) * 100 }}%)</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @empty
            <table class="layui-table" lay-size="sm">
                <thead>
                <tr>
                    <th>来源</th>
                    <th>订单数</th>
                    <th>等待商户接单</th>
                    <th>系统分配中</th>
                    <th>商户已接单</th>
                    <th>已发货</th>
                    <th>已失败</th>
                    <th>售后中</th>
                    <th>售后完成</th>
                    <th>订单完成</th>
                    <th>已取消</th>
                    <th>未付款</th>
                    <th>最多游戏</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            @endforelse
        </div>

        <div class="layui-tab-item layui-show" style="margin-top: 30px;">

            @forelse($moneys as $money) 
                <table class="layui-table" lay-size="sm">
                    @if($money->type == 3)
                        <div  style="float: left"><label class="layui-form-label">发单+接单下的金额统计</label></div>
                    @elseif($money->type == 1)
                        <div  style="float: left"><label class="layui-form-label">接单下的金额统计</label></div>
                    @elseif($money->type == 2)
                        <div  style="float: left"><label class="layui-form-label">发单下的金额统计</label></div>
                    @endif
                    <thead>
                    <tr>
                        <th style="width:12px">来源</th>
                        <th>总额</th>
                        <th>等待商户接单</th>
                        <th>系统分配中</th>
                        <th>商户已接单</th>
                        <th>已发货</th>
                        <th>已失败</th>
                        <th>售后中</th>
                        <th>售后完成</th>
                        <th>订单完成</th>
                        <th>已取消</th>
                        <th>未付款</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($money->type == 3 && $money->source === 0)
                        <tr>
                            <td>所有</td>
                            <td>{{ $money->total }}</td>
                            <td>{{ $money->waite_user_receive }}</td>
                            <td>{{ $money->distributing }}</td>
                            <td>{{ $money->received }}</td>
                            <td>{{ $money->sended }}</td>
                            <td>{{ $money->failed }}</td>
                            <td>{{ $money->after_saling }}</td>
                            <td>{{ $money->after_saled }}</td>
                            <td>{{ $money->successed }}</td>
                            <td>{{ $money->canceled }}</td>
                            <td>{{ $money->waite_pay }}</td>
                        </tr>
                    @elseif($money->type == 3 && $money->source != 0 && $money->source)
                        <tr>
                            <td>{{ config('order.source')[$money->source] }}</td>
                            <td>{{ $money->total }}</td>
                            <td>{{ $money->waite_user_receive }}</td>
                            <td>{{ $money->distributing }}</td>
                            <td>{{ $money->received }}</td>
                            <td>{{ $money->sended }}</td>
                            <td>{{ $money->failed }}</td>
                            <td>{{ $money->after_saling }}</td>
                            <td>{{ $money->after_saled }}</td>
                            <td>{{ $money->successed }}</td>
                            <td>{{ $money->canceled }}</td>
                            <td>{{ $money->waite_pay }}</td>
                        </tr>
                    @elseif($money->type == 1 && $money->source != 0 && $money->source)
                        <tr>
                            <td>{{ config('order.source')[$money->source] }}</td>
                            <td>{{ $money->total }}</td>
                            <td>{{ $money->waite_user_receive }}</td>
                            <td>{{ $money->distributing }}</td>
                            <td>{{ $money->received }}</td>
                            <td>{{ $money->sended }}</td>
                            <td>{{ $money->failed }}</td>
                            <td>{{ $money->after_saling }}</td>
                            <td>{{ $money->after_saled }}</td>
                            <td>{{ $money->successed }}</td>
                            <td>{{ $money->canceled }}</td>
                            <td>{{ $money->waite_pay }}</td>                           
                        </tr>
                    @elseif($money->type == 2 && $money->source != 0 && $money->source)
                        <tr>
                            <td>{{ config('order.source')[$money->source] }}</td>
                            <td>{{ $money->total }}</td>
                            <td>{{ $money->waite_user_receive }}</td>
                            <td>{{ $money->distributing }}</td>
                            <td>{{ $money->received }}</td>
                            <td>{{ $money->sended }}</td>
                            <td>{{ $money->failed }}</td>
                            <td>{{ $money->after_saling }}</td>
                            <td>{{ $money->after_saled }}</td>
                            <td>{{ $money->successed }}</td>
                            <td>{{ $money->canceled }}</td>
                            <td>{{ $money->waite_pay }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @empty
            <table class="layui-table" lay-size="sm">
                <thead>
                <tr>
                    <th>来源</th>
                    <td>总额</td>
                    <th>等待商户接单</th>
                    <th>系统分配中</th>
                    <th>商户已接单</th>
                    <th>已发货</th>
                    <th>已失败</th>
                    <th>售后中</th>
                    <th>售后完成</th>
                    <th>订单完成</th>
                    <th>已取消</th>
                    <th>未付款</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            var form = layui.form, layer = layui.layer;
            //常规用法
            laydate.render({
                elem: '#test1'
            });

            //常规用法
            laydate.render({
                elem: '#test2'
            });

            var timeError = "{{ session('timeError') ?: '' }}";

            if (timeError) {

                layer.msg(timeError, {icon: 5, time:1500});
            }
        });
    </script>
@endsection