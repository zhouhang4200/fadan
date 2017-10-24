@extends('frontend.layouts.app')

@section('title', '财务 - 资产日报')

@section('submenu')
@include('frontend.finance.submenu')
@endsection

@section('main')
<form class="layui-form" id="search-form">
    <div class="layui-form-item">
        <div class="layui-input-inline" style="width: 100px;">
            <input type="text" class="layui-input" id="date-start" name="date_start" value="{{ $dateStart }}" placeholder="开始时间">
        </div>
        <div class="layui-form-mid">-</div>
        <div class="layui-input-inline" style="width: 100px;">
            <input type="text" class="layui-input" id="date-end" name="date_end" value="{{ $dateEnd }}" placeholder="结束时间">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
        </div>
    </div>
</form>

<table class="layui-table">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>日期</th>
            <th>余额</th>
            <th>加款</th>
            <th>提现</th>
            <th>消费</th>
            <th>退款</th>
            <th>支出</th>
            <th>收入</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataList as $data)
            <tr>
                <td>{{ $data->date }}</td>
                <td>
                    <p>余额：{{ $data->balance + 0 }}</p>
                    <p>冻结：{{ $data->frozen + 0 }}</p>
                </td>
                <td>
                    <p>当日：{{ $data->recharge + 0 }}</p>
                    <p>累计：{{ $data->total_recharge + 0 }}</p>
                </td>
                <td>
                    <p>当日：{{ $data->withdraw + 0 }}</p>
                    <p>累计：{{ $data->total_withdraw + 0 }}</p>
                </td>
                <td>
                    <p>当日：{{ $data->consume + 0 }}</p>
                    <p>累计：{{ $data->total_consume + 0 }}</p>
                </td>
                <td>
                    <p>当日：{{ $data->refund + 0 }}</p>
                    <p>累计：{{ $data->total_refund + 0 }}</p>
                </td>
                <td>
                    <p>当日：{{ $data->expend + 0 }}</p>
                    <p>累计：{{ $data->total_expend + 0 }}</p>
                </td>
                <td>
                    <p>当日：{{ $data->income + 0 }}</p>
                    <p>累计：{{ $data->total_income + 0 }}</p>
                </td>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $dataList->appends(['date_start' => $dateStart, 'date_end' => $dateEnd])->links() }}
@endsection

@section('js')
<script>
layui.use(['laydate', 'form'], function () {
    var laydate = layui.laydate;

    laydate.render({elem: '#date-start'});
    laydate.render({elem: '#date-end'});
});

$('#export').click(function () {
    var url = "{{ route('frontend.finance.amount-flow.export') }}?" + $('#search-form').serialize();
    window.location.href = url;
});
</script>
@endsection