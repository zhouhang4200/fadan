@extends('backend.layouts.main')

@section('title', ' | 版本管理')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class=""><span>首页</span></li>
            <li class="active"><span>版本管理</span></li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="main-box">
            <header class="main-box-header clearfix">
                <div class="filter-block">
                    <form class="layui-form">
                        <div class="row">
                            <div class=" col-xs-2">
                                <select class="layui-input" name="name">
                                    <option value="">全部</option>
                                    @foreach ($appName as $v)
                                        <option {{ $name == $v ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-xs-10">
                                <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
                                <button type="button" class="layui-btn layui-btn-success" id="add-new">新增</button>
                            </div>
                        </div>

                    </form>
                </div>
            </header>
            <div class="main-box-body clearfix">
                <table class="layui-table layui-form" lay-size="sm">
                    <thead>
                        <tr>
                            <th>客户端</th>
                            <th>版本号</th>
                            <th>最新版本号</th>
                            <th>强制更新</th>
                            <th>备注</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataList as $data)
                            <tr>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->number }}</td>
                                <td>{{ $data->current_number }}</td>
                                <td>{{ $data->forced_update ? '是' : '否' }}</td>
                                <td>{{ $data->remark }}</td>
                                <td>{{ $data->created_at }}</td>
                                <td>{{ $data->updated_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="99">暂无数据</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-xs-3">
                        总数：{{ $dataList->total() }}　本页显示：{{$dataList->count()}}
                    </div>
                    <div class="col-xs-9">
                        <div class=" pull-right">
                            {!! $dataList->appends(['name' => $name])->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="recharge" style="display: none;padding: 20px">
    <form class="layui-form layui-form-pane">
        <div class="layui-form-item">
            <label class="layui-form-label">客户端名称</label>
            <div class="layui-input-block">
                <select class="layui-input" name="name">
                    @foreach ($appName as $v)
                        <option {{ $name == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">版本号</label>
            <div class="layui-input-block">
                <input type="text" name="number" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <input type="text" name="remark" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">更新策略</label>
            <div class="layui-input-block">
                <input type="radio" name="forced_update" value="0" title="建议" checked>
                <input type="radio" name="forced_update" value="1" title="强制">
            </div>
        </div>

        <div class="layui-form-item">
            <input type="hidden" id="submit-type" value="create">
            <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="version">确定</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
layui.use(['form', 'laytpl', 'element'], function(){
    var form = layui.form, layer = layui.layer;

    $('#add-new').click(function (data) {
        layer.open({
            type: 1,
            shade: 0.2,
            title: '手动加款',
            content: $('#recharge')
        });
    });

    form.on('submit(version)', function (data) {
        $.post("{{ route('app.version.store') }}", data.field, function (data) {
            if (data.status === 1) {
                layer.alert('操作成功', function () {
                    window.location.reload();
                });
            } else {
                layer.alert(data.message);
            }
        }, 'json');

        return false;
    });
});
</script>
@endsection
