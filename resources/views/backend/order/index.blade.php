@extends('backend.layouts.main')

@section('title', ' | 订单列表')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .user-td td div{
            text-align: center;width: 320px;
        }
        .layui-table tr th {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">订单列表</li>
                        </ul>

                        <div class="layui-tab-content">
                        <form class="layui-form" method="" action="">

                                <div class="layui-form-item">

                                    <div class="layui-input-inline" style="width:280px">
                                    <label class="layui-form-label">渠道</label>
                                        <div class="layui-input-inline">
                                            <select name="source" lay-verify="" lay-search="">
                                                <option value="">输入名字或直接选择</option>
                                                @foreach(config('order.source') as $id => $source)
                                                <option value="{{ $id }}">{{ $source }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-input-inline" style="width:280px">
                                        <label class="layui-form-label">订单状态</label>
                                            <div class="layui-input-inline">
                                                <select name="status" lay-verify="" lay-search="">
                                                    <option value="">输入名字或直接选择</option>
                                                    @foreach(config('order.status') as $id => $status)
                                                    <option value="{{ $id }}">{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="layui-input-inline" style="width:280px">
                                        <label class="layui-form-label">开始时间</label>
                                        <div class="layui-input-inline">
                                        <input type="text" class="layui-input" value="{{ old('startDate') ?: $startDate }}" name="startDate" id="test1" placeholder="年-月-日">
                                        </div>
                                    </div>
                                    <div class="layui-input-inline" style="width:280px">
                                        <label class="layui-form-label">结束时间</label>
                                        <div class="layui-input-inline">
                                        <input type="text" class="layui-input" value="{{ old('endDate') ?: $endDate }}"  name="endDate" id="test2" placeholder="年-月-日">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                                        <button  class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('orders.index') }}" style="color:#fff">返回</a></button>
                                    </div>
                                </div>
                            </form>
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                    <tr>
                                        <th>单号</th>
                                        <th>外部单号</th>
                                        <th>来源</th>
                                        <th>状态</th>
                                        <th>商品</th>
                                        <th>服务</th>
                                        <th>游戏</th>
                                        <th>原售价</th>
                                        <th>售价</th>
                                        <th>数量</th>
                                        <th>原总额</th>
                                        <th>总额</th>
                                        <th>创建者(主账号)</th>
                                        <th>接单者(主账号)</th>
                                        <th>创建时间</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @forelse($orders as $order)
                                        <tr>
                                            <td>{{ $order->no }}</td>
                                            <td>{{ $order->foreign_order_no }}</td>
                                            <td>{{ config('order.source')[$order->source] }}</td>
                                            <td>{{ config('order.status')[$order->status] }}</td>
                                            <td>{{ $order->goods_name }}</td>
                                            <td>{{ $order->service_name }}</td>
                                            <td>{{ $order->game_name }}</td>
                                            <td>{{ $order->original_price }}</td>
                                            <td>{{ $order->price }}</td>
                                            <td>{{ $order->quantity }}</td>
                                            <td>{{ $order->original_amount }}</td>
                                            <td>{{ $order->amount }}</td>
                                            <td>
                                                {{ $order->creator_user_id }}({{ $order->creator_primary_user_id }})
                                            </td>
                                            <td>{{ $order->gainer_user_id }}({{ $order->gainer_primary_user_id }})</td>
                                            <td>{{ $order->created_at }}</td>
                                            <td>
                                                <a type="button" class="layui-btn layui-btn-mini layui-btn-normal" href="{{ route('orders.show', ['order' => $order->id]) }}">详情</a>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        {!! $orders->appends([
                            'status' => $status,
                            'source' => $source,
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                        ])->render() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
layui.use(['form', 'layedit', 'laydate'], function(){
var laydate = layui.laydate;
//常规用法
laydate.render({
elem: '#test1'
});

//常规用法
laydate.render({
    elem: '#test2'
    });
});
</script>
@endsection