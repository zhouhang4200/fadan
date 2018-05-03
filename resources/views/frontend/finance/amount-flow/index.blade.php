@extends('frontend.layouts.app')

@section('title', '财务 - 资金流水')

@section('css')
    <style>
        .layui-form-item .layui-inline {
            margin-bottom: 5px;
            margin-right: 5px;
        }
        .layui-form-mid {
            margin-right: 4px;
        }
    </style>
@endsection

@section('submenu')
@include('frontend.finance.submenu')
@endsection

@section('main')
<form class="layui-form" id="search">
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-mid">说明：</label>
            <div class="layui-input-inline">
                <select name="trade_sub_type" lay-search="">
                    <option value="">请选择</option>
                    @foreach (config('tradetype.user_sub') as $key => $value)
                        <option value="{{ $key }}" {{ $key == $tradeSubType ? 'selected' : '' }}> {{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-mid">类型：</label>
            <div class="layui-input-inline">
                <select name="trade_type" lay-search="">
                    <option value="">所有类型</option>
                    @foreach (config('tradetype.user_display') as $key => $value)
                        <option value="{{ $key }}" {{ $key == $tradeType ? 'selected' : '' }}> {{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-mid">相关单号：</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" name="trade_no" placeholder="相关单号" value="{{ $tradeNo }}">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-mid">时间：</label>
            <div class="layui-input-inline" style="">
                <input type="text" class="layui-input" id="time-start" name="time_start" value="{{ $timeStart }}" placeholder="开始时间">
            </div>
            <div class="layui-input-inline" style="">
                <input type="text" class="layui-input" id="time-end" name="time_end" value="{{ $timeEnd }}" placeholder="结束时间">
            </div>
            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
            <button class="layui-btn layui-btn-normal" type="button" id="export">导出</button>
        </div>
    </div>

</form>

<table class="layui-table" lay-size="sm">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>流水号</th>
            <th>说明</th>
            <th>类型</th>
            <th>变动金额</th>
            <th>账户余额</th>
            <th>相关单号</th>
            <th>时间</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dataList as $data)
            <tr>
                <td>{{ $data->id }}</td>
                <td>{{ config('tradetype.user_sub')[$data->trade_subtype] }}</td>
                <td>{{ config('tradetype.user')[$data->trade_type] }}</td>
                <td>{{ $data->fee + 0 }}</td>
                <td>{{ $data->balance + 0 }}</td>
                <td>{{ $data->trade_no }}</td>
                <td>{{ $data->created_at }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="999">暂时没有数据</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $dataList->appends([
    'trade_no' => $tradeNo,
    'trade_type' => $tradeType,
    'trade_sub_type' => $tradeSubType,
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
    var url = "{{ route('frontend.finance.amount-flow.export') }}?" + $('#search').serialize();
    window.location.href = url;
});
</script>
@endsection