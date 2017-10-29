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
                        <a class="btn btn-primary" href="javascript:window.history.go(-1);" style="float: right;">返回</a>
                        <div>
                            @foreach ($order->detail as $item)
                                <p>{{ $item->field_display_name }}：{{ $item->field_name == 'password' ? '******' : $item->field_value }}</p>
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
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->history as $history)
                                        <tr>
                                            <td>{{ $history->user_id }}</td>
                                            <td>{{ $history->admin_user_id ?: '---' }}</td>
                                            <td>{{ config('order.operation_type')[$history->type] }}</td>
                                            <td>{{ $history->name }}</td>
                                            <td>{{ $history->description }}</td>
                                            <td>{{ $history->created_at }}</td>
                                            <td>
                                                <button type="button" class="layui-btn layui-btn-mini layui-btn-normal compare" data-route="{{ $history->id }}">数据对比</button>
                                                <div class="compare-box" style="display: none;">
                                                    <table>
                                                        <tr>
                                                            <td>操作前</td>
                                                            <td>操作后</td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                @foreach (unserialize($history->before) as $key => $value)
                                                                    <p>{{ $key }}: {{ $value }}</p>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                @foreach (unserialize($history->after) as $key => $value)
                                                                    <p>{{ $key }}: {{ $value }}</p>
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
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
layui.use(['layer'], function() {
    $('.compare').click(function () {
        var $this = $(this);

        layer.open({
            type: 1,
            title: false,
            content: $(this).siblings('.compare-box')
        });
    });
});
</script>
@endsection