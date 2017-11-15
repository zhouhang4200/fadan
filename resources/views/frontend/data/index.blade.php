@extends('frontend.layouts.app')

@section('title', '首页 - 日常数据')

@section('css')
    <style>
        .layui-form-label {
            width:65px;
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
        <div  style="float: left"><label class="layui-form-label">发单+接单</label></div>
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
            @if($alls && $game)
                <tr>
                    <td>所有</td>
                    <td>{{ $alls->orderCount }}</td>
                    <td>{{ $alls->waite }} ({{ @bcdiv($alls->waite, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->fenpei }} ({{ @bcdiv($alls->fenpei, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->jiedan }} ({{ @bcdiv($alls->jiedan, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->fahuo }} ({{ @bcdiv($alls->fahuo, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->shibai }} ({{ @bcdiv($alls->shibai, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->shouhou }} ({{ @bcdiv($alls->shouhou, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->shouhouwancheng }} ({{ @bcdiv($alls->shouhouwancheng, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->dingdanwancheng }} ({{ @bcdiv($alls->dingdanwancheng, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->quxiao }} ({{ @bcdiv($alls->quxiao, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $alls->weifukuan }} ({{ @bcdiv($alls->weifukuan, $alls->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $game->game_name }} ({{ @bcdiv($game->count, $alls->orderCount, 3) * 100 }}%)</td>
                </tr>
            @endif
            </tbody>
        </table>

        <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">发单+接单</label>
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
            @forelse($datas as $k => $data)
                <tr>
                    <td>{{ config('order.source')[$data->source] }}</td>
                    <td>{{ $data->statusCount }}</td>
                    <td>{{ $data->waite }} ({{ @bcdiv($data->waite, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->fenpei }} ({{ @bcdiv($data->fenpei, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->jiedan }} ({{ @bcdiv($data->jiedan, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->fahuo }} ({{ @bcdiv($data->fahuo, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->shibai }} ({{ @bcdiv($data->shibai, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->shouhou }} ({{ @bcdiv($data->shouhou, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->shouhouwancheng }} ({{ @bcdiv($data->shouhouwancheng, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->dingdanwancheng }} ({{ @bcdiv($data->dingdanwancheng, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->quxiao }} ({{ @bcdiv($data->quxiao, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $data->weifukuan }} ({{ @bcdiv($data->weifukuan, $data->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $resourceGame[$k]->game_name }} ({{ @bcdiv($resourceGame[$k]->max, $data->orderCount, 3) * 100 }}%)</td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>

        <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">接单</label>
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
            @forelse($dataReceives as $k => $dataReceive)
                <tr>
                    <td>{{ config('order.source')[$dataReceive->source] }}</td>
                    <td>{{ $dataReceive->statusCount }}</td>
                    <td>{{ $dataReceive->waite }} ({{ @bcdiv($dataReceive->waite, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->fenpei }} ({{ @bcdiv($dataReceive->fenpei, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->jiedan }} ({{ @bcdiv($dataReceive->jiedan, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->fahuo }} ({{ @bcdiv($dataReceive->fahuo, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->shibai }} ({{ @bcdiv($dataReceive->shibai, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->shouhou }} ({{ @bcdiv($dataReceive->shouhou, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->shouhouwancheng }} ({{ @bcdiv($dataReceive->shouhouwancheng, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->dingdanwancheng }} ({{ @bcdiv($dataReceive->dingdanwancheng, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->quxiao }} ({{ @bcdiv($dataReceive->quxiao, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataReceive->weifukuan }} ({{ @bcdiv($dataReceive->weifukuan, $dataReceive->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $receiveGame[$k]->game_name }} ({{ @bcdiv($receiveGame[$k]->max, $dataReceive->orderCount, 3) * 100 }}%)</td>                           
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>

        <table class="layui-table" lay-size="sm">
        <label class="layui-form-label">发单</label>
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
            @forelse($dataSends as $k => $dataSend)
                <tr>
                    <td>{{ config('order.source')[$dataSend->source] }}</td>
                    <td>{{ $dataSend->statusCount }}</td>
                    <td>{{ $dataSend->waite }} ({{ @bcdiv($dataSend->waite, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->fenpei }} ({{ @bcdiv($dataSend->fenpei, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->jiedan }} ({{ @bcdiv($dataSend->jiedan, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->fahuo }} ({{ @bcdiv($dataSend->fahuo, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->shibai }} ({{ @bcdiv($dataSend->shibai, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->shouhou }} ({{ @bcdiv($dataSend->shouhou, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->shouhouwancheng }} ({{ @bcdiv($dataSend->shouhouwancheng, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->dingdanwancheng }} ({{ @bcdiv($dataSend->dingdanwancheng, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->quxiao }} ({{ @bcdiv($dataSend->quxiao, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $dataSend->weifukuan }} ({{ @bcdiv($dataSend->weifukuan, $dataSend->orderCount, 3) * 100 }}%)</td>
                    <td>{{ $sendGame[$k]->game_name }} ({{ @bcdiv($sendGame[$k]->max, $dataSend->orderCount, 3) * 100 }}%)</td>                         
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