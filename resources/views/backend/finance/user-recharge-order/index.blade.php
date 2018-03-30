@extends('backend.layouts.main')

@section('title', ' | 用户加款单')

@section('content')
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">用户加款单</li>
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
                                <select class="form-control" name="type">
                                    <option value="">所有状态</option>
                                    @foreach ($config['type'] as $key => $value)
                                        <option value="{{ $key }}" {{ $key == $type ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
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
                                <button class="btn btn-primary" type="button"  id="export">导出</button>
                            </div>
                        </div>
                    </form>

                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>单号</th>
                            <th>外部单号</th>
                            <th>旺旺号</th>
                            <th>加款金额</th>
                            <th>类型</th>
                            <th>加款人</th>
                            <th>加款人主账号</th>
                            <th>备注</th>
                            <th>创建时间</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td>{{ $data->no }}</td>
                                    <td>{{ $data->foreign_order_no }}</td>
                                    <td>{{ $data->wangwang }}</td>
                                    <td>{{ $data->fee + 0 }}</td>
                                    <td>{{ $config['type'][$data->type] }}</td>
                                    <td>{{ $data->creator_user_id }}</td>
                                    <td>{{ $data->creator_primary_user_id}}</td>
                                    <td>{{ $data->remark}}</td>
                                    <td>{{ $data->created_at}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->appends([
                        'user_id'    => $userId,
                        'type'       => $type,
                        'no'         => $no,
                        'time_start' => $timeStart,
                        'time_end'   => $timeEnd,
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

$('#export').click(function () {
    var url = "{{ route('finance.user-widthdraw-order') }}?export=1&" + $('#search-flow').serialize();
    window.location.href = url;
});

layui.use(['layer'], function () {

    // 完成
    $('.complete').click(function () {
        var id = $(this).data('id');
        layer.confirm('提现已完成？' , function (layerConfirm) {

            layer.close(layerConfirm);

            layer.prompt({title: '请输入备注',formType: 2},function(value, promptIndex, elem){
                $.post("{{ route('finance.user-widthdraw-order.complete', '') }}/" + id, {remark:value},function (data) {

                    if (data.status === 1) {
                        layer.alert('操作成功', function () {
                            location.reload();
                        });
                    } else {
                        layer.alert(data.message, function (index) {
                            layer.close(index);
                        });
                    }
                    layer.close(promptIndex);
                }, 'json');
            });
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
