@extends('backend.layouts.main')

@section('title', ' | 组件类型')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>组件类型</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <div class="filter-block pull-left">
                        <form class="form-inline" role="form">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name"  placeholder="输入组件名称" value="{{ $name }}">
                            </div>
                            <button type="submit" class="btn btn-success">搜索</button>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <button  class="btn btn-primary pull-right" id="add-widget-type">
                            <i class="glyphicon glyphicon-plus"></i> 添加组件类型
                        </button>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>名称(英文)</th>
                                <th>显示名称</th>
                                <th>添加人</th>
                                <th>添加时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($widgetType as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ config('goods.template.field_type')[$item->type] }}</td>
                                    <td>{{ $item->display_name }}</td>
                                    <td>{{ $item->createdAdmin->name ?? '无' }}</td>
                                    <td>{{ $item->created_at }}</td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        {{ $widgetType->appends(['name' => $name])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="add-template" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">组件类型</label>
                <div class="layui-input-block">
                    @foreach(config('goods.template.field_type') as $key => $value)
                        <input type="radio" name="type" value="{{ $key }}" title="{{ $value }}" lay-filter="field-type">
                    @endforeach
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">组件英文</label>
                <div class="layui-input-block">
                    <input type="text" name="name" required  lay-verify="required" placeholder="请输入组件英文" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">显示名称</label>
                <div class="layui-input-block">
                    <input type="text" name="display_name" required  lay-verify="required" placeholder="请输入显示名称" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="save-template">确定添加</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
    layui.use(['form', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
            //监听提交
            form.on('submit(save-template)', function(data){
                $.post('{{ route('goods.template.widget.add') }}', {name:data.field.name, display_name:data.field.display_name, type:data.field.type}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            // 添加组件类型
            $('#add-widget-type').on('click', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加组件类型',
                    content: $('.add-template')
                });
            });
        });
</script>
@endsection