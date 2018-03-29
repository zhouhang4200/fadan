@extends('backend.layouts.main')

@section('title', ' | 订单列表')

@section('css')
    <style>
        .layui-form-pane .layui-form-label {
            width: 120px;
            padding: 8px 15px;
            height: 36px;
            line-height: 20px;
            border-radius: 2px 0 0 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            box-sizing: border-box;
        }

        blockquote:before {
            content: ""
        }

        .theme-whbl blockquote, .theme-whbl blockquote.pull-right {
            border-color: #e6e6e6;
        }

        .layui-form-item .layui-input-inline {
            width: 180px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>订单列表</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form" id="search-form">
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" value="{{Request::input('orderNo')}}" name="orderNo" placeholder="订单号">
                            </div>

                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" value="{{Request::input('user_id')}}" name="user_id" placeholder="商户号">
                            </div>

                            <div class="form-group col-xs-1">
                                <select name="status" lay-search="">
                                    <option value="-1">订单状态</option>
                                    @foreach(config('backend.status') as $key => $value)
                                        <option value="{{ $key }}"
                                                @if($key == Request::input('status')) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="layui-input-inline" style="width: 200px;">
                                <button class="layui-btn layui-btn-normal" type="submit">查询</button>
                                {{--<a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small" >导出</a>--}}
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
                            <div class=" pull-right">
                                {!! $orders->appends(Request::all())->render() !!}
                            </div>
                        </div>
                    </div>
                    <table class="layui-table layui-form" lay-size="sm">
                        <thead>
                        <tr>
                            <th width="12%">订单号</th>
                            <th width="9%">下单时间</th>
                            <th width="9%">完成时间</th>
                            <th>游戏名</th>
                            <th>版本名</th>
                            <th>商户号</th>
                            <th width="9%">CDK</th>
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
                        @endforelse
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $orders->total() }}　本页显示：{{$orders->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $orders->appends(Request::all())->render() !!}
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
        layui.use(['form', 'layedit', 'laytpl', 'element', 'laydate', 'table', 'upload'], function () {
            var form = layui.form, layer = layui.layer, laydate = layui.laydate, layTpl = layui.laytpl,
                element = layui.element, table = layui.table, upload = layui.upload;

            //日期
            laydate.render({
                elem: '#startDate'
            });
            laydate.render({
                elem: '#endDate'
            });
            //日期
            laydate.render({
                elem: '#start_time'
            });
            laydate.render({
                elem: '#end_time'
            });
            //日期
            laydate.render({
                elem: '#start_time1'
            });
            laydate.render({
                elem: '#end_time1'
            });

            // 订单操作
            form.on('select(operation)', function (data) {
                eval(data.value + "('" + data.elem.getAttribute('data-no') + "'," + data.elem.getAttribute('data-id') + ")");
            });

            //订单详情
            function detail(no, id) {
                window.open("/admin/order/platform/content/" + id);
            }

        });

    </script>
@endsection