@extends('frontend.layouts.app')

@section('title', '首页 - 日常数据')

@section('css')
    <style>
        .layui-form-label {
            width:200px;
            text-align: left;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.submenu')
@endsection

@section('main')
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
        <table class="layui-table" lay-size="sm">
        <div  style="float: left"><label class="layui-form-label">发单+接单数量和占比</label></div>
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
            @if($datas && $mostGame)
                <tr>
                    <td>所有</td>
                    <td>{{ $datas->total }}</td>
                    <td>{{ $datas->waite_user_receive }} ({{ @bcdiv($datas->waite_user_receive, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->distributing }} ({{ @bcdiv($datas->distributing, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->received }} ({{ @bcdiv($datas->received, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->sended }} ({{ @bcdiv($datas->sended, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->failed }} ({{ @bcdiv($datas->failed, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->after_saling }} ({{ @bcdiv($datas->after_saling, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->after_saled }} ({{ @bcdiv($datas->after_saled, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->successed }} ({{ @bcdiv($datas->successed, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->canceled }} ({{ @bcdiv($datas->canceled, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $datas->waite_pay }} ({{ @bcdiv($datas->waite_pay, $datas->total, 3) * 100 }}%)</td>
                    <td>{{ $mostGame->most_game_name }} ({{ $mostGame->total }}, {{ @bcdiv($mostGame->total, $datas->total, 3) * 100 }}%)</td>
                </tr>
            @endif
            </tbody>
        </table>

        <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">发单+接单数量和占比</label>
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
            @if ($resourceGame)
            @forelse($sourceDatas as $k => $sourceData)
                <tr>
                    <td>{{ config('order.source')[$sourceData->source] }}</td>
                    <td>{{ $sourceData->total }}</td>
                    <td>{{ $sourceData->waite_user_receive }} ({{ @bcdiv($sourceData->waite_user_receive, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->distributing }} ({{ @bcdiv($sourceData->distributing, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->received }} ({{ @bcdiv($sourceData->received, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->sended }} ({{ @bcdiv($sourceData->sended, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->failed }} ({{ @bcdiv($sourceData->failed, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->after_saling }} ({{ @bcdiv($sourceData->after_saling, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->after_saled }} ({{ @bcdiv($sourceData->after_saled, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->successed }} ({{ @bcdiv($sourceData->successed, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->canceled }} ({{ @bcdiv($sourceData->canceled, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $sourceData->waite_pay }} ({{ @bcdiv($sourceData->waite_pay, $sourceData->total, 3) * 100 }}%)</td>
                    <td>{{ $resourceGame[$k]->most_game_name }} ({{ $resourceGame[$k]->max }}, {{ @bcdiv($resourceGame[$k]->max, $sourceData->total, 3) * 100 }}%)</td>
                </tr>
            @empty
            @endforelse
            @endif
            </tbody>
        </table>

        <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">接单数量和占比</label>
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
            @if($receiveGame)
            @forelse($receiveDatas as $k => $receiveData)
                <tr>
                    <td>{{ config('order.source')[$receiveData->source] }}</td>
                    <td>{{ $receiveData->total }}</td>
                    <td>{{ $receiveData->waite_user_receive }} ({{ @bcdiv($receiveData->waite_user_receive, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->distributing }} ({{ @bcdiv($receiveData->distributing, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->received }} ({{ @bcdiv($receiveData->received, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->sended }} ({{ @bcdiv($receiveData->sended, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->failed }} ({{ @bcdiv($receiveData->failed, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->after_saling }} ({{ @bcdiv($receiveData->after_saling, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->after_saled }} ({{ @bcdiv($receiveData->after_saled, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->successed }} ({{ @bcdiv($receiveData->successed, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->canceled }} ({{ @bcdiv($receiveData->canceled, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveData->waite_pay }} ({{ @bcdiv($receiveData->waite_pay, $receiveData->total, 3) * 100 }}%)</td>
                    <td>{{ $receiveGame[$k]->most_game_name }} ({{ $receiveGame[$k]->max }}, {{ @bcdiv($receiveGame[$k]->max, $receiveData->total, 3) * 100 }}%)</td>                            
                </tr>
            @empty
            @endforelse
            @endif
            </tbody>
        </table>

        <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">发单数量和占比</label>
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
            @if($sendGame)
            @forelse($sendDatas as $k => $sendData)
                <tr>
                    <td>{{ config('order.source')[$sendData->source] }}</td>
                    <td>{{ $sendData->total }}</td>
                    <td>{{ $sendData->waite_user_receive }} ({{ @bcdiv($sendData->waite_user_receive, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->distributing }} ({{ @bcdiv($sendData->distributing, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->received }} ({{ @bcdiv($sendData->received, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->sended }} ({{ @bcdiv($sendData->sended, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->failed }} ({{ @bcdiv($sendData->failed, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->after_saling }} ({{ @bcdiv($sendData->after_saling, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->after_saled }} ({{ @bcdiv($sendData->after_saled, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->successed }} ({{ @bcdiv($sendData->successed, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->canceled }} ({{ @bcdiv($sendData->canceled, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendData->waite_pay }} ({{ @bcdiv($sendData->waite_pay, $sendData->total, 3) * 100 }}%)</td>
                    <td>{{ $sendGame[$k]->most_game_name }} ({{ $sendGame[$k]->max }}, {{ @bcdiv($sendGame[$k]->max, $sendData->total, 3) * 100 }}%)</td>                         
                </tr>
            @empty
            @endforelse
            @endif
            </tbody>
        </table>


        <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">发单+接单下的金额统计</label>
            <thead>
            <tr>
                <th>来源</th>
                <th>总金额</th>
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
            @if($moneyDatas && $moneyDatas->total)
                <tr>
                    <td>所有</td>
                    <td>{{ number_format($moneyDatas->total, 2) }}</td>
                    <td>{{ number_format($moneyDatas->waite_user_receive, 2) }}</td>
                    <td>{{ number_format($moneyDatas->distributing, 2) }}</td>
                    <td>{{ number_format($moneyDatas->received, 2) }}</td>
                    <td>{{ number_format($moneyDatas->sended, 2) }}</td>
                    <td>{{ number_format($moneyDatas->failed, 2) }}</td>
                    <td>{{ number_format($moneyDatas->after_saling, 2) }}</td>
                    <td>{{ number_format($moneyDatas->after_saled, 2) }}</td>
                    <td>{{ number_format($moneyDatas->successed, 2) }}</td>
                    <td>{{ number_format($moneyDatas->canceled, 2) }}</td>
                    <td>{{ number_format($moneyDatas->waite_pay, 2) }}</td>                         
                </tr>
            @endif
            </tbody>
        </table>

         <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">发单+接单下的金额统计</label>
            <thead>
            <tr>
                <th>来源</th>
                <th>总金额</th>
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
            @forelse($sourceMoneyDatas as $k => $sourceMoneyData)
                <tr>
                    <td>{{ config('order.source')[$sourceMoneyData->source] }}</td>
                    <td>{{ number_format($sourceMoneyData->total, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->waite_user_receive, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->distributing, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->received, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->sended, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->failed, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->after_saling, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->after_saled, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->successed, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->canceled, 2) }}</td>
                    <td>{{ number_format($sourceMoneyData->waite_pay, 2) }}</td>                        
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>

         <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">接单下的金额统计</label>
            <thead>
            <tr>
                <th>来源</th>
                <th>总金额</th>
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
            @forelse($receiveMoneyDatas as $k => $receiveMoneyData)
                <tr>
                    <td>{{ config('order.source')[$receiveMoneyData->source] }}</td>
                    <td>{{ number_format($receiveMoneyData->total, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->waite_user_receive, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->distributing, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->received, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->sended, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->failed, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->after_saling, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->after_saled, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->successed, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->canceled, 2) }}</td>
                    <td>{{ number_format($receiveMoneyData->waite_pay, 2) }}</td>                        
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>

        <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">发单下的金额统计</label>
            <thead>
            <tr>
                <th>来源</th>
                <th>总金额</th>
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
            @forelse($sendMoneyDatas as $k => $sendMoneyData)
                <tr>
                    <td>{{ config('order.source')[$sendMoneyData->source] }}</td>
                    <td>{{ number_format($sendMoneyData->total, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->waite_user_receive, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->distributing, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->received, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->sended, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->failed, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->after_saling, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->after_saled, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->successed, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->canceled, 2) }}</td>
                    <td>{{ number_format($sendMoneyData->waite_pay, 2) }}</td>                        
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>


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