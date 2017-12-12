@extends('frontend.layouts.app')


@section('title', "订单 - 接单列表")

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
    @include('frontend.order.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search-form">
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 100px;">
                <select name="status" lay-search>
                    <option value="">所有状态</option>
                    @foreach (config('order.status') as $key => $value)
                        <option value="{{ $key }}" {{ $key == $status ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-input-inline" style="width: 100px;">
                <select name="service_id" lay-search>
                    <option value="">所有类型</option>
                    @foreach ($services as $key => $value)
                        <option value="{{ $key }}" {{ $key == $serviceId ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-input-inline" style="width: 100px;">
                <select name="game_id" lay-search>
                    <option value="">所有游戏</option>
                    @foreach ($games as $key => $value)
                        <option value="{{ $key }}" {{ $key == $gameId ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="start_date" id="startDate" placeholder="开始日期">
            </div>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="end_date" id="endDate" placeholder="结束日期">
            </div>
            <div class="layui-input-inline" style="width: 200px;">
                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
                <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small" >导出</a>
            </div>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>发单ID</th>
            <th>单号</th>
            <th>类型</th>
            <th>游戏</th>
            <th>商品名</th>
            <th>订单总额</th>
            <th>状态</th>
            <th>时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $item)
            <tr>
                <td>{{ $item->creator_primary_user_id }}</td>
                <td>千手：{{ $item->no }}<br>外部：{{ $item->foreign_order_no }}</td>
                <td>{{ $item->service_name }}</td>
                <td>{{ $item->game_name }}</td>
                <td>{{ $item->goods_name }}</td>
                <td>{{ $item->amount }}</td>
                <td>{{ config('order.status')[$item->status] }}</td>
                <td>{{ $item->created_at }}</td>
                <td><button class="layui-btn layui-btn-normal layui-btn-custom-mini detail" data-no="{{ $item->no }}">详情</button></td>
            </tr>
        @empty
            <tr>
                <td colspan="10">暂时没有订单数据</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $orders->appends([
        'service_id' => $serviceId,
        'game_id' => $gameId,
        'status' => $status,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ])->links() }}
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