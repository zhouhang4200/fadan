@extends('backend.layouts.main')

@section('title', ' | 托管资金明细')

@section('content')
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">托管资金明细</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form id="search-flow" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="相关单号" name="order_no" value="{{ Request::input('order_no') }}">
                            </div>
                            <div class="col-md-1">
                                <input type="text" class="form-control" placeholder="用户ID" name="user_id" value="{{ Request::input('user_id') }}">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">搜索</button>
                            </div>
                        </div>
                    </form>

                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>订单号</th>
                            <th>金额</th>
                            <th>商户ID</th>
                            <th>模型</th>
                            <th>模型ID</th>
                            <th>创建于</th>
                            <th>更新于</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td>{{ $data->order_no }}</td>
                                    <td>{{ $data->amount + 0}}</td>
                                    <td>{{ $data->user_id }}</td>
                                    <td>{{ $data->orderable_type }}</td>
                                    <td>{{ $data->orderable_id }}</td>
                                    <td>{{ $data->created_at}}</td>
                                    <td>{{ $data->udpated_at}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->appends(Request::all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
