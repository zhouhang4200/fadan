@extends('frontend.layouts.app')

@section('title', '统计 - 短信发送详情')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('submenu')
    @include('frontend.statistic.submenu')
@endsection

@section('main')

    <form class="layui-form" id="search">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">千手单号：</label>
                <div class="layui-input-inline">
                    <input type="text" name="order_no" autocomplete="off" class="layui-input" value="{{ $orderNo }}">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">外部单号：</label>
                <div class="layui-input-inline">
                    <input type="text" name="foreign_order_no" autocomplete="off" class="layui-input" value="{{ $foreignOrderNo }}">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">发送手机：</label>
                <div class="layui-input-inline">
                    <input type="text" name="client_phone" autocomplete="off" class="layui-input" value="{{ $clientPhone }}">
                </div>
            </div>
            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
            <button class="layui-btn layui-btn-normal" type="button" function="query" lay-submit="" lay-filter="export">导出</button>

        </div>
    </form>

    <div class="layui-tab-item layui-show" lay-size="sm">
        <form class="layui-form" action="">
        <table class="layui-table" lay-size="sm" style="">
            <thead>
            <tr>
                <th>订单号</th>
                <th>发送手机</th>
                <th>发送内容</th>
                <th>发送时间</th>
            </tr>
            </thead>
            <tbody>
                @forelse($recordDetail as $item)
                    <tr>
                        <td width="20%">千手单号：{{ $item->order_no }} <br/> 外部单号：{{ $item->foreign_order_no }}</td>
                        <td width="13%">{{ $item->client_phone }}</td>
                        <td>{{ $item->contents }}</td>
                        <td width="10%">{{ $item->date }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">暂时没有数据</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
        </form>
    </div>
    {!! $recordDetail->appends([
        'client_phone' => $clientPhone,
        'order_no' => $orderNo,
        'foreign_order_no' => $foreignOrderNo,
    ])->render() !!}

@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            var form = layui.form;

            //常规用法
            laydate.render({
                elem: '#start-date'
            });

            //常规用法
            laydate.render({
                elem: '#end-date'
            });
        });
    </script>
@endsection