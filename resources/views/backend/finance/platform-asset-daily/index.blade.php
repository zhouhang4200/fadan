@extends('backend.layouts.main')

@section('title', ' | 平台资产日报')

@section('content')
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">平台资产日报</li>
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
                                <button class="btn btn-primary" type="submit">搜索</button>
                                <button class="btn btn-primary" type="button" id="export-flow">导出</button>
                            </div>
                        </div>
                    </form>

                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>日期</th>
                            <th>平台资金</th>
                            <th>平台托管</th>
                            <th>用户余额</th>
                            <th>用户冻结</th>
                            <th>当日用户加款</th>
                            <th>累计用户加款</th>
                            <th>当日用户提现</th>
                            <th>累计用户提现</th>
                            <th>当日用户消费</th>
                            <th>累计用户消费</th>
                            <th>当日退款给用户</th>
                            <th>累计退款给用户</th>
                            <th>当日用户成交次数</th>
                            <th>累计用户成交次数</th>
                            <th>当日用户成交</th>
                            <th>累计用户成交</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td class="date">{{ $data->date }}</td>
                                    <td class="amount">{{ $data->amount + 0 }}</td>
                                    <td class="managed">{{ $data->managed + 0 }}</td>
                                    <td class="balance">{{ $data->balance + 0 }}</td>
                                    <td class="frozen">{{ $data->frozen + 0 }}</td>
                                    <td>{{ $data->recharge + 0 }}</td>
                                    <td class="total-recharge">{{ $data->total_recharge + 0 }}</td>
                                    <td>{{ $data->withdraw + 0 }}</td>
                                    <td class="total-withdraw">{{ $data->total_withdraw + 0 }}</td>
                                    <td>{{ $data->consume + 0 }}</td>
                                    <td>{{ $data->total_consume + 0 }}</td>
                                    <td>{{ $data->refund + 0 }}</td>
                                    <td>{{ $data->total_refund + 0 }}</td>
                                    <td>{{ $data->trade_quantity }}</td>
                                    <td>{{ $data->total_trade_quantity }}</td>
                                    <td>{{ $data->trade_amount + 0 }}</td>
                                    <td>{{ $data->total_trade_amount + 0 }}</td>
                                    <td><button class="layui-btn layui-btn-mini layui-btn-normal account-checking" type="button">对账</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->appends(['date_start' => $dateStart, 'date_end' => $dateEnd])->links() }}
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

layui.use(['layer'], function(){
    var layer = layui.layer;

    $('.account-checking').click(function () {
        var $td           = $(this).parent();
        var date          = $td.siblings('.date').text();
        var amount        = parseFloat($td.siblings('.amount').text());
        var managed       = parseFloat($td.siblings('.managed').text());
        var balance       = parseFloat($td.siblings('.balance').text());
        var frozen        = parseFloat($td.siblings('.frozen').text());
        var totalRecharge = parseFloat($td.siblings('.total-recharge').text());
        var totalWithdraw = parseFloat($td.siblings('.total-withdraw').text());

        var content = '<p>左右相等就是对的</p><p>';
        content += '计算（左）：' + totalRecharge + ' - ' + totalWithdraw + ' = ' + (totalRecharge * 10000 - totalWithdraw * 10000) / 10000 + '</p>';
        content += '<p>计算（右）：'
                + amount
                + ' + '
                + managed
                + ' + '
                + balance
                + ' + '
                + frozen
                + ' = '
                + (amount * 10000 + managed * 10000 + balance * 10000 + frozen * 10000) / 10000;
                + '</p>';
        content += '<p style="color:#aaa">公式: 累计用户加款 - 累计用户提现 = 平台资金 + 平台托管 + 用户余额 + 用户冻结</p>';

        var accountCheckingAlert = layer.alert(content, {title:'日期: ' + date});
        layer.style(accountCheckingAlert, {width:'730px'});
    });
});
</script>
@endsection
