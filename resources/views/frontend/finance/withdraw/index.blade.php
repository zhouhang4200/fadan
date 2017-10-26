@extends('frontend.layouts.app')

@section('title', '财务 - 我的提现')

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
            <select name="status">
                <option value="">全部状态</option>
                @foreach (config('withdraw.status') as $key => $value)
                    <option value="{{ $key }}" {{ $key == $status ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
        </div>

        @inject('withdraw', 'App\Services\Views\WithdrawService')
        {{ $withdraw->button('提现', 'layui-btn layui-btn-normal', 'float:right;') }}
    </div>
</form>

<table class="layui-table" lay-size="sm">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>提现单号</th>
            <th>提现金额</th>
            <th>状态</th>
            <th>备注</th>
            <th>创建时间</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataList as $data)
            <tr>
                <td>{{ $data->no }}</td>
                <td>{{ $data->fee + 0 }}</td>
                <td>{{ config('withdraw.status')[$data->status] }}</td>
                <td>{{ $data->remark }}</td>
                <td>{{ $data->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $dataList->appends([
    'status'     => $status,
    'time_start' => $timeStart,
    'time_end'   => $timeEnd,
    ])->links() }}

@endsection

@section('js')
<script>
layui.use(['laydate', 'form'], function () {
    var laydate = layui.laydate;

    laydate.render({elem: '#time-start'});
    laydate.render({elem: '#time-end'});
});
</script>
@endsection