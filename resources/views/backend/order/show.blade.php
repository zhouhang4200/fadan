@extends('backend.layouts.main')

@section('title', ' | 订单详情')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="main-box">
            <div class="main-box-body clearfix">
                <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="add">订单详情</li>
                    </ul>

                    <div class="layui-tab-content">
                        <div>
                            @foreach ($order->detail as $item)
                                <p>{{ $item->field_display_name }}：{{ $item->field_value }}</p>
                            @endforeach
                        </div>

                        <div class="layui-tab-item layui-show">
                            操作记录：
                            <table class="layui-table" lay-size="sm">
                                <thead>
                                    <tr>
                                        <th>用户</th>
                                        <th>管理员</th>
                                        <th>类型</th>
                                        <th>名称</th>
                                        <th>说明</th>
                                        <th>时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @forelse($order->history as $history)
                                        <tr>
                                            <td>{{ $history->user_id }}</td>
                                            <td>{{ $history->admin_user_id}}</td>
                                            <td>{{ config('history.operation_type')[$history->type] }}</td>
                                            <td>{{ $history->name }}</td>
                                            <td>{{ $history->description }}</td>
                                            <td>{{ $history->created_at }}</td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
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