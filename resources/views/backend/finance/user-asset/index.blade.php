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
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td class="user-id">{{ $data->user_id }}</td>
                                    <td class="balance">{{ $data->balance + 0 }}</td>
                                    <td class="frozen">{{ $data->frozen + 0 }}</td>
                                    <td class="total-recharge">{{ $data->total_recharge + 0 }}</td>
                                    <td class="total-withdraw">{{ $data->total_withdraw + 0 }}</td>
                                    <td class="total-consume">{{ $data->total_consume + 0 }}</td>
                                    <td class="total-refund">{{ $data->total_refund + 0}}</td>
                                    <td class="total-expend">{{ $data->total_expend + 0}}</td>
                                    <td class="total-income">{{ $data->total_income + 0}}</td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>{{ $data->updated_at }}</td>
                                    <td><button class="btn btn-primary btn-sm account-checking" type="button">对账</button></td>
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

layui.use(['layer'], function(){
    var layer = layui.layer;

    $('.account-checking').click(function () {
        var $td           = $(this).parent();
        var userId        = $td.siblings('.user-id').text();
        var balance       = parseFloat($td.siblings('.balance').text());
        var frozen        = parseFloat($td.siblings('.frozen').text());
        var totalRecharge = parseFloat($td.siblings('.total-recharge').text());
        var totalWithdraw = parseFloat($td.siblings('.total-withdraw').text());
        var totalConsume  = parseFloat($td.siblings('.total-consume').text());
        var totalRefund   = parseFloat($td.siblings('.total-refund').text());
        var totalExpend   = parseFloat($td.siblings('.total-expend').text());
        var totalIncome   = parseFloat($td.siblings('.total-income').text());

        var content = '<p>左右相等就是对的</p><p>';
        content += '计算（左）：' + balance + ' + ' + frozen + ' = ' + (balance * 10000 + frozen * 10000) / 10000 + '</p>';
        content += '<p>计算（右）：'
                + totalRecharge
                + ' + '
                + totalRefund
                + ' + '
                + totalIncome
                + ' - '
                + totalWithdraw
                + ' - '
                + totalConsume
                + ' - '
                + totalExpend
                + ' = '
                + (totalRecharge * 10000 + totalRefund * 10000 + totalIncome * 10000 - totalWithdraw * 10000 - totalConsume * 10000 - totalExpend * 10000) / 10000;
                + '</p>';
        content += '<p style="color:#aaa">公式：剩余金额 + 冻结金额 = 累计平台加款 + 累计平台退款 + 累计交易收入 - 累计平台提现 - 累计平台消费 - 累计交易支出</p>';

        var accountCheckingAlert = layer.alert(content, {title:'用户ID' + userId});
        layer.style(accountCheckingAlert, {width:'730px'});
    });
});
</script>
@endsection