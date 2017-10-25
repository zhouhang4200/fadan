@extends('backend.layouts.main')

@section('title', ' | 用户资产日报')

@section('content')
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">用户资产日报</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form id="search-flow" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="date-start" name="date_start" value="{{ $dateStart }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="date-end" name="date_end" value="{{ $dateEnd }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="用户ID" name="user_id" value="{{ $userId }}">
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">搜索</button>
                                <button class="btn btn-primary" type="button" id="export-flow">导出</button>
                            </div>
                        </div>
                    </form>

                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>日期</th>
                            <th>用户ID</th>
                            <th>余额</th>
                            <th>冻结</th>
                            <th>当日加款</th>
                            <th>累计加款</th>
                            <th>当日提现</th>
                            <th>累计提现</th>
                            <th>当日消费</th>
                            <th>累计消费</th>
                            <th>当日从平台退款</th>
                            <th>累计从平台退款</th>
                            <th>当日成交次数</th>
                            <th>累计成交次数</th>
                            <th>当日用户成交</th>
                            <th>累计用户成交</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td>{{ $data->date }}</td>
                                    <td>{{ $data->user_id }}</td>
                                    <td>{{ $data->balance + 0 }}</td>
                                    <td>{{ $data->frozen + 0 }}</td>
                                    <td>{{ $data->recharge + 0 }}</td>
                                    <td>{{ $data->total_recharge + 0 }}</td>
                                    <td>{{ $data->withdraw + 0 }}</td>
                                    <td>{{ $data->total_withdraw + 0 }}</td>
                                    <td>{{ $data->consume + 0 }}</td>
                                    <td>{{ $data->total_consume + 0 }}</td>
                                    <td>{{ $data->refund + 0 }}</td>
                                    <td>{{ $data->total_refund + 0 }}</td>
                                    <td>{{ $data->expend + 0 }}</td>
                                    <td>{{ $data->total_expend + 0 }}</td>
                                    <td>{{ $data->income + 0 }}</td>
                                    <td>{{ $data->total_income + 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->appends(['user_id' => $userId, 'date_start' => $dateStart, 'date_end' => $dateEnd])->links() }}
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

$('#export-flow').click(function () {
    var url = "{{ route('finance.platform-asset-daily.export') }}?" + $('#search-flow').serialize();
    window.location.href = url;
});
</script>
@endsection