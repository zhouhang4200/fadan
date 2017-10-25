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

        <button id="withdraw" class="layui-btn" type="button" style="float: right;">提现</button>
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
            <th>更新时间</th>
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
                <td>{{ $data->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $dataList->appends([
    'status'     => $status,
    'time_start' => $timeStart,
    'time_end'   => $timeEnd,
    ])->links() }}


<div id="withdraw-box" style="display: none;padding: 20px 60px 20px 0;">
    <div class="layui-form-item">
        <label class="layui-form-label">提现金额</label>
        <div class="layui-input-block">
            <input type="text" name="fee" class="layui-input" placeholder="可提现金额 {{ Auth::user()->userAsset->balance }}">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">备注说明</label>
        <div class="layui-input-block">
            <input type="text" name="remark" class="layui-input" placeholder="可留空">
        </div>
    </div>
    <div id="template"></div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button id="withdraw-submit" class="layui-btn layui-bg-blue">提交</button>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
layui.use(['laydate', 'form'], function () {
    var laydate = layui.laydate;

    laydate.render({elem: '#time-start'});
    laydate.render({elem: '#time-end'});
});

$('#withdraw').click(function () {
    layer.open({
        type: 1,
        title: '提现单',
        area: ['350px', '240px'],
        content: $('#withdraw-box')
    });
});

$('#withdraw-submit').click(function () {
    var loading = layer.load(2, {shade: [0.1, '#000']});

    $.ajax({
        url: "{{ route('frontend.finance.widthdraw-order.store') }}",
        type: 'POST',
        dataType: 'json',
        data: {
            fee: $('[name="fee"]').val(),
            remark: $('[name="remark"]').val()
        },
        error: function (data) {
            layer.close(loading);
            var responseJSON = data.responseJSON.errors;
            for (var key in responseJSON) {
                layer.msg(responseJSON[key][0]);
                break;
            }
        },
        success: function (data) {
            layer.close(loading);
            if (data.status === 1) {
                layer.alert('操作成功', function () {
                    location.reload();
                });
            } else {
                layer.alert(data.message);
            }
        }
    });
});
</script>
@endsection