@extends('backend.layouts.main')

@section('title', ' | 用户资产列表')

@section('content')
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">用户资产列表</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form id="search-flow" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="用户ID" name="user_id" value="{{ $userId }}">
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">搜索</button>
                            </div>
                        </div>
                    </form>

                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>用户ID</th>
                            <th>剩余金额</th>
                            <th>冻结金额</th>
                            <th>累计平台加款</th>
                            <th>累计平台提现</th>
                            <th>累计平台消费</th>
                            <th>累计平台退款</th>
                            <th>累计交易支出</th>
                            <th>累计交易收入</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td>{{ $data->user_id }}</td>
                                    <td>{{ $data->balance + 0 }}</td>
                                    <td>{{ $data->frozen + 0 }}</td>
                                    <td>{{ $data->total_recharge + 0 }}</td>
                                    <td>{{ $data->total_withdraw + 0 }}</td>
                                    <td>{{ $data->total_consume + 0 }}</td>
                                    <td>{{ $data->total_refund + 0}}</td>
                                    <td>{{ $data->total_expend + 0}}</td>
                                    <td>{{ $data->total_income + 0}}</td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>{{ $data->updated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->appends(['user_id' => $userId])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$('#date-start').datepicker();
$('#date-end').datepicker();
</script>
@endsection