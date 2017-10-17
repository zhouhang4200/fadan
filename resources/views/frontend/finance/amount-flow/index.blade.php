@extends('frontend.layouts.app')

@section('title', '财务 - 资金流水')

@section('submenu')
@include('frontend.finance.submenu')
@endsection

@section('main')
<form class="layui-form" id="search-form">
    <div class="layui-form-item">
        <div class="layui-input-inline" style="width: 100px;">
            <input type="text" class="layui-input" id="time-start" name="time_start" value="{{ $timeStart }}" placeholder="开始时间">
        </div>
        <div class="layui-form-mid">-</div>
        <div class="layui-input-inline" style="width: 100px;">
            <input type="text" class="layui-input" id="time-end" name="time_end" value="{{ $timeEnd }}" placeholder="结束时间">
        </div>
        <div class="layui-input-inline" style="width: 100px;">
            <select name="trade_type">
                <option value="">所有类型</option>
                @foreach (config('tradetype.user') as $key => $value)
                    <option value="{{ $key }}" {{ $key == $tradeType ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <input type="text" class="layui-input" name="trade_no" placeholder="相关单号">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
            <button class="layui-btn layui-btn-primary" type="button" id="export">导出</button>
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
            <th>流水号</th>
            <th>相关单号</th>
            <th>类型</th>
            <th>金额</th>
            <th>说明</th>
            <th>时间</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataList as $data)
            <tr>
                <td>{{ $data->id }}</td>
                <td>{{ $data->trade_no }}</td>
                <td>{{ config('tradetype.user')[$data->trade_type] }}</td>
                <td>{{ $data->fee + 0 }}</td>
                <td>{{ $data->remark }}</td>
                <td>{{ $data->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $dataList->appends([
    'trade_no' => $tradeNo,
    'trade_type' => $tradeType,
    'time_start' => $timeStart,
    'time_end' => $timeEnd,
    ])->links() }}
@endsection

@section('js')
<script>
layui.use(['laydate', 'form'], function () {
    var laydate = layui.laydate;

    laydate.render({elem: '#time-start'});
    laydate.render({elem: '#time-end'});
});

$('#export').click(function () {
    var url = "{{ route('frontend.finance.amount-flow.export') }}?" + $('#search-form').serialize();
    window.location.href = url;
});
</script>
@endsection