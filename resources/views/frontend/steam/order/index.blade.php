@extends('frontend.layouts.app')


@section('title', "订单 - 订单列表")

@section('css')
    <style>
        .user-td td div{
            text-align: center;width: 320px;
        }
        .layui-table tr th {
            text-align: center;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.steam.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search-form">
        <div class="layui-form-item">

            <div class="layui-input-inline">
                <input type="text" class="layui-input" value="{{Request::input('orderNo')}}" name="orderNo"  placeholder="订单号">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
                {{--<a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small" >导出</a>--}}
            </div>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th width="12%">订单号</th>
            <th width="11%">下单时间</th>
            <th width="11%">成功下单时间</th>
            <th>游戏名</th>
            <th>版本名</th>
            <th>商户号</th>
            <th>CDK</th>
            <th>充值机器人</th>
            <th>兑换账号</th>
            <th>消耗金额</th>
            <th>面值</th>
            <th>失败信息</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->no }}</td>
                <td>{{ $order->created_at }}</td>
                <td>{{ $order->success_time }}</td>
                <td>{{ $order->goodses->game_name }}</td>
                <td>{{ $order->goodses->name }}</td>
                <td>{{ $order->user_id }}</td>
                <td>{{ $order->cdk }}</td>
                <td>{{ $order->filled_account }}</td>
                <td>{{ $order->recharge_account}}</td>
                <td>{{ $order->consume_money }}</td>
                <td>{{ $order->price }}</td>
                <td> {{ $order->message ?? ''}}</td>
                <td>{{ config('backend.status')[$order->status] ?? '' }}</td>
                <td>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="13">暂时没有订单数据</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $orders->appends(Request::all())->links() }}
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
                    ,layer = layui.layer
                    ,layedit = layui.layedit
                    ,laydate = layui.laydate;
            laydate.render({
                elem: '#startDate'
            });
            laydate.render({
                elem: '#endDate'
            });
            // 查看订单列表
           $('.layui-table').on('click', '.detail', function () {
               layer.open({
                   type: 2,
                   title: '订单详情',
                   shadeClose: true,
                   maxmin: true, //开启最大化最小化按钮
                   area: ['600px', '630px'],
                   scrollbar: false,
                   content: "{{ url('/workbench/order-operation/detail') }}?no=" + $(this).attr('data-no')
               });
           })
        });
    </script>
@endsection