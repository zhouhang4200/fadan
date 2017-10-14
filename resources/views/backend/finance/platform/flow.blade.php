@extends('backend.layouts.main')

@section('title', ' | 资金流水')

@section('content')
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">资金流水</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>流水号</th>
                            <th>用户</th>
                            <th>管理员</th>
                            <th>类型</th>
                            <th>子类型</th>
                            <th>相关单号</th>
                            <th>金额</th>
                            <th>备注</th>
                            <th>平台资金</th>
                            <th>平台托管</th>
                            <th>用户余额</th>
                            <th>用户冻结</th>
                            <th>累计用户加款</th>
                            <th>累计用户提现</th>
                            <th>累计用户消费</th>
                            <th>累计退款给用户</th>
                            <th>累计用户成交次数</th>
                            <th>累计用户成交金额</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td>{{ $data->id }}</td>
                                    <td>{{ $data->user_id }}</td>
                                    <td>{{ $data->admin_user_id }}</td>
                                    <td>{{ $data->trade_type }}</td>
                                    <td>{{ $data->trade_subtype }}</td>
                                    <td>{{ $data->trade_no }}</td>
                                    <td>{{ $data->fee + 0}}</td>
                                    <td>{{ $data->remark + 0}}</td>
                                    <td>{{ $data->amount + 0}}</td>
                                    <td>{{ $data->managed + 0}}</td>
                                    <td>{{ $data->balance + 0}}</td>
                                    <td>{{ $data->frozen + 0}}</td>
                                    <td>{{ $data->total_recharge + 0}}</td>
                                    <td>{{ $data->total_withdraw + 0}}</td>
                                    <td>{{ $data->total_consume + 0}}</td>
                                    <td>{{ $data->total_refund + 0}}</td>
                                    <td>{{ $data->total_trade_quantity }}</td>
                                    <td>{{ $data->total_trade_amount + 0}}</td>
                                    <td>{{ $data->created_at}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
