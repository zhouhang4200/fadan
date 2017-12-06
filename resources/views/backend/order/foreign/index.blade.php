@extends('backend.layouts.main')

@section('title', ' | 外部订单')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>外部订单</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="form-group col-xs-1">
                                <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                            </div>
                            <div class="form-group col-xs-1">
                                <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ $endDate }}">
                            </div>

                            <div class="form-group col-xs-1">
                                <select class="layui-input" name="source_id" lay-search="">
                                    <option value="0">请选择来源</option>
                                    @foreach(config('order.source') as $key => $value)
                                        <option value="{{ $key }}" @if($key == $sourceId) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-2">
                                <select class="layui-input" name="channel_name" lay-search="">
                                    <option value="0">请选择店铺</option>
                                    @foreach($channel as $key => $value)
                                        <option value="{{ $value }}" @if($value == $channelName) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-1">
                                <input type="text" class="layui-input" name="foreign_goods_id"  placeholder="卡门商品ID" value="{{ $foreignGoodsId }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <input type="text" class="layui-input" name="kamen_order_no"  placeholder="卡门单号" value="{{ $kamenOrderNo }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <input type="text" class="layui-input" name="foreign_order_no"  placeholder="外部订单号" value="{{ $foreignOrderNo }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <input type="text" class="layui-input" name="wang_wang"  placeholder="旺旺" value="{{ $wangWang }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
                                <button type="submit" class="layui-btn layui-btn-normal">导出</button>
                            </div>
                        </div>

                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $orders->total() }}　本页显示：{{$orders->count()}}
                        </div>
                        <div class="col-xs-9">
                        </div>
                    </div>
                    <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>时间</th>
                                <th>来源</th>
                                <th>店铺名</th>
                                <th>卡门单号</th>
                                <th>卡门商品ID</th>
                                <th>外部单号</th>
                                <th>旺旺</th>
                                <th>单价</th>
                                <th>总价</th>
                                <th>平台订单号</th>
                                <th>平台接单商户ID</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->created_at }}</td>
                                    <td>{{ config('order.source')[$order->channel] }}</td>
                                    <td>{{ $order->channel_name }}</td>
                                    <td>{{ $order->kamen_order_no }}</td>
                                    <td>{{ $order->foreign_goods_id }}</td>
                                    <td>{{ $order->foreign_order_no }}</td>
                                    <td>{{ $order->wang_wang }}</td>
                                    <td>{{ $order->single_price }}</td>
                                    <td>{{ $order->total_price }}</td>
                                    <td>{{ $order->order->no }}</td>
                                    <td>{{ $order->order->gainer_primary_user_id }}</td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $orders->total() }}　本页显示：{{$orders->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $orders->appends([
                                    'channel_name' => $channelName,
                                    'source_id' => $sourceId,
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,
                                ])->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    //Demo
    layui.use(['form', 'laytpl', 'element', 'laydate'], function(){
        var form = layui.form, layer = layui.layer, laydate = layui.laydate;

        //日期
        laydate.render({
            elem: '#startDate'
        });
        laydate.render({
            elem: '#endDate'
        });
    });
</script>
@endsection