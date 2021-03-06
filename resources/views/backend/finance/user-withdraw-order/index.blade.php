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
                            <div class="col-md-1">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="time-start" name="time_start" value="{{ Request::input('time_start') }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="time-end" name="time_end" value="{{ Request::input('time_end') }}">
                                </div>
                            </div>
                            <div class="form-group col-md-1">
                                <select class="form-control" name="type">
                                    <option value="">所有类型</option>
                                    @foreach ($config['type'] as $key => $value)
                                        <option value="{{ $key }}" {{ $key == Request::input('type') ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <select class="form-control" name="status">
                                    <option value="">所有状态</option>
                                    @foreach ($config['status'] as $key => $value)
                                        <option value="{{ $key }}" {{ $key == Request::input('status') ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="相关单号" name="no" value="{{ Request::input('no') }}">
                            </div>
                            <div class="col-md-1">
                                <input type="text" class="form-control" placeholder="用户ID" name="user_id" value="{{ Request::input('user_id') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="管理备注" name="admin_remark" value="{{ Request::input('admin_remark') }}">
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
                            <th>提现单号</th>
                            <th>主账号ID</th>
                            <th>原千手ID</th>
                            <th>当前余额</th>
                            <th>当前冻结</th>
                            <th>提现金额</th>
                            <th>类型</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>详情</th>
                            <th style="width: 152px;">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td>{{ $data->no }}</td>
                                    <td>{{ $data->creator_primary_user_id }}</td>
                                    <td>{{ $data->user->nickname ?? '' }}</td>
                                    <td>{{ $data->user->asset->balance ?? '' }}</td>
                                    <td>{{ $data->user->asset->frozen ?? '' }}</td>
                                    <td>{{ $data->fee + 0 }}</td>
                                    <td>{{ config('withdraw.type')[$data->type] }}</td>
                                    <td>{{ config('withdraw.status')[$data->status] }}</td>
                                    <td>{{ $data->created_at}}</td>
                                    <td>{{ $data->updated_at}}</td>
                                    <td><a href="javascript:void(0)" class="show" data-url="{{ route('finance.user-widthdraw-order.show', ['id' => $data->id]) }}">查看</a></td>
                                    <td>
                                        @if ($data->status == 1)
                                            <button type="button" class="layui-btn layui-btn-warm layui-btn-mini send-email" data-id="{{ $data->id }}">邮件</button>
                                            <button type="button" class="layui-btn layui-btn-normal layui-btn-mini complete" data-id="{{ $data->id }}">同意</button>
                                            <button type="button" class="layui-btn layui-btn-mini layui-btn-danger refuse" data-id="{{ $data->id }}">拒绝</button>
                                        @elseif ($data->status == 4)
                                            <button type="button" class="layui-btn layui-btn-normal layui-btn-mini upload" lay-data="{data:{id:{{ $data->id }}}}">上传附件</button>
                                            <button type="button" class="layui-btn layui-btn-danger layui-btn-mini upload-confirm" data-id="{{ $data->id }}">上传确认</button>
                                        @elseif ($data->status == 5)
                                            <button type="button" class="layui-btn layui-btn-normal layui-btn-mini agree" data-id="{{ $data->id }}">办款</button>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->appends(Request::all())->links() }}
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

layui.use(['layer', 'upload'], function () {
    var upload = layui.upload;

    // 完成
    $('.complete').click(function () {
        var id = $(this).data('id');
        layer.confirm('原来的提现完成，需要自己填单子让财务打款的那种。' , function (layerConfirm) {

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

    // 发邮件标记
    $('.send-email').click(function () {
        var id = $(this).data('id');
        layer.confirm('已发送审核邮件，打标记。' , function (layerConfirm) {

            layer.close(layerConfirm);

            $.post("{{ route('finance.user-widthdraw-order.set-send-email') }}", {
                id: id
            },function (data) {

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

    // 上传
    upload.render({
        elem: '.upload',
        url: "{{ route('finance.user-widthdraw-order.upload') }}",
        accept: 'images',
        field: 'image',
        size: 500,
        before: function (obj) {
            load = layer.load(4, {shade:0.3});
        },
        done: function (res, index, upload) {
            layer.close(load);
            if (res.status === 1) {
                layer.alert('操作成功', function () {
                    location.reload();
                });
            } else {
                layer.alert(res.message, function (index) {
                    layer.close(index);
                });
            }
        }
    });

    // 上传完成
    $('.upload-confirm').click(function () {
        var id = $(this).data('id');
        layer.confirm('确认后附件不可修改。' , function (layerConfirm) {
            layer.close(layerConfirm);

            $.post("{{ route('finance.user-widthdraw-order.upload-confirm') }}", {id: id}, function (data) {
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

    // 同意
    $('.agree').click(function () {
        var id = $(this).data('id');
        layer.confirm('新的同意，走接口自动提现。' , function (layerConfirm) {

            layer.close(layerConfirm);

            layer.prompt({title: '请输入备注',formType: 2},function (value, promptIndex, elem) {
                $.post("{{ route('finance.user-widthdraw-order.auto') }}", {
                    id: id,
                    remark:value
                },function (data) {

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

    // 详情
    $('.show').click(function () {
        $(this).parents('tr').css('background-color', '#89cdff');
        $(this).parents('tr').siblings().removeAttr('style');

        layer.open({
            type: 2,
            title: '详情',
            area: ['80%', '80%'],
            content: $(this).data('url')
        });
    });
});

</script>
@endsection
