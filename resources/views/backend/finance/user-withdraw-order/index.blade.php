@extends('backend.layouts.main')

@section('title', ' | 用户提现管理')

@section('content')
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">用户提现管理</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form id="search-flow" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="time-start" name="time_start" value="{{ $timeStart }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="time-end" name="time_end" value="{{ $timeEnd }}">
                                </div>
                            </div>
                            <div class="form-group col-md-1">
                                <select class="form-control" name="status">
                                    <option value="">所有状态</option>
                                    @foreach (config('withdraw.status') as $key => $value)
                                        <option value="{{ $key }}" {{ $key == $status ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="相关单号" name="no" value="{{ $no }}">
                            </div>
                            <div class="col-md-1">
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
                            <th>提现单号</th>
                            <th>主账号ID</th>
                            <th>原千手ID</th>
                            <th>姓名</th>
                            <th>开户行</th>
                            <th>卡号</th>
                            <th>提现金额</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td>{{ $data->no }}</td>
                                    <td>{{ $data->creator_primary_user_id }}</td>
                                    <td>{{ $data->user->nickname }}</td>
                                    <td>{{ $data->user->realNameIdent->name }}</td>
                                    <td>{{ $data->user->realNameIdent->bank_name }}</td>
                                    <td>{{ $data->user->realNameIdent->bank_number }}</td>
                                    <td>{{ $data->fee + 0 }}</td>
                                    <td>{{ config('withdraw.status')[$data->status] }}</td>
                                    <td>{{ $data->created_at}}</td>
                                    <td>{{ $data->updated_at}}</td>
                                    <td>
                                        @if ($data->status == 1)
                                        <button type="button" class="layui-btn layui-btn-normal layui-btn-mini complete" data-id="{{ $data->id }}">完成</button>
                                        <button type="button" class="layui-btn layui-btn-mini layui-btn-danger refuse" data-id="{{ $data->id }}">拒绝</button>
                                        @else
                                        ---
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->appends([
                        'user_id'       => $userId,
                        'status'        => $status,
                        'no'            => $no,
                        'time_start'    => $timeStart,
                        'time_end'      => $timeEnd,
                        ])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$('#time-start').datepicker();
$('#time-end').datepicker();

layui.use(['layer'], function () {

    // 完成
    $('.complete').click(function () {
        var id = $(this).data('id');
        layer.confirm('提现已完成？' , function (layerConfirm) {
            $.post("{{ route('finance.user-widthdraw-order.complete', '') }}/" + id, function (data) {
                layer.close(layerConfirm);
                if (data.status === 1) {
                    layer.alert('操作成功', function () {
                        location.reload();
                    });
                } else {
                    layer.alert(data.message);
                }
            }, 'json');
        });
    });

    // 拒绝
    $('.refuse').click(function () {
        var id = $(this).data('id');
        layer.confirm('拒绝提现？' , function (layerConfirm) {
            $.post("{{ route('finance.user-widthdraw-order.refuse', '') }}/" + id, function (data) {
                layer.close(layerConfirm);
                if (data.status === 1) {
                    layer.alert('操作成功', function () {
                        location.reload();
                    });
                } else {
                    layer.alert(data.message);
                }
            }, 'json');
        });
    });
});

</script>
@endsection