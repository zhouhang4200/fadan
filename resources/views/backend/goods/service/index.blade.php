@extends('backend.layouts.main')

@section('title', ' | 服务')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>服务</span></li>
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
                                <input type="text" class="form-control" name="name"  placeholder="输入服务名称" value="{{ $name }}">
                            </div>
                            <button type="submit" class="btn btn-success">搜索</button>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <button class="layui-btn layui-btn-samll layui-btn-normal" id="add-goods-service">添加服务</button>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>名称</th>
                                <th>添加员</th>
                                <th>修改人</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th>状态</th>
                                <th width="15%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($services as $item)
                                <tr class="{{ $item->status == 0 ? 'layui-bg-red' : '' }}">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->createdAdmin->name }}</td>
                                    <td>{{ $item->updatedAdmin->name ?? '无' }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->updated_at }}</td>
                                    <td>{{ $item->status == 0 ? '没启用' : '已启用' }}</td>
                                    <td>
                                        @if($item->status == 0)
                                            <button class="layui-btn layui-btn-mini layui-btn-normal" lay-submit="" lay-filter="change-status" data-id="{{ $item->id }}" data-status="1">启用</button>
                                        @else
                                            <button class="layui-btn layui-btn-mini layui-btn-danger" lay-submit="" lay-filter="change-status" data-id="{{ $item->id }}"  data-status="0">禁用</button>
                                        @endif
                                        <button class="layui-btn layui-btn-mini layui-btn-normal" lay-submit="" lay-filter="show" data-route="{{ route('goods.service.show', ['id' => $item->id]) }}">修改</button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        {{ $services->appends(['name' => $name])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="add-service-box" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">服务名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="required" placeholder="请输入服务名称" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">显示排序</label>
                <div class="layui-input-inline">
                    <input type="text" name="sortord" lay-verify="required" placeholder="请输入显示排序" autocomplete="off" class="layui-input" value="999">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="save-service">确定添加</button>
            </div>
        </form>
    </div>

    <div class="edit-service-box" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <input type="hidden" name="id">
            <div class="layui-form-item">
                <label class="layui-form-label">服务名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="required" placeholder="请输入服务名称" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">显示排序</label>
                <div class="layui-input-inline">
                    <input type="text" name="sortord" lay-verify="required" placeholder="请输入显示排序" autocomplete="off" class="layui-input" value="999">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="edit">保存修改</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
        layui.use('form', function(){
            var form = layui.form;
            //添加服务
            form.on('submit(save-service)', function(data){
                $.post('{{ route('goods.service.store') }}', {data:data.field}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            //修改状态
            form.on('submit(change-status)', function(data){
                $.post('{{ route('goods.service.status') }}', {id:data.elem.getAttribute('data-id'), status:data.elem.getAttribute('data-status')}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            // 查看服务
            form.on('submit(show)', function(data){
                $.get(data.elem.getAttribute('data-route'), {id:data.elem.getAttribute('data-id')}, function (result) {
                    $('.edit-service-box input[name="id"]').val(result.id);
                    $('.edit-service-box input[name="name"]').val(result.name);
                    $('.edit-service-box input[name="sortord"]').val(result.sortord);
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '修改商品服务',
                        content: $('.edit-service-box')
                    });
                }, 'json');
                return false;
            });
            // 修改服务
            form.on('submit(edit)', function(data){
                $.post('{{ route('goods.service.edit') }}', {data:data.field,id:data.field.id}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            // 添加商品弹窗
            $('#add-goods-service').on('click', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加商品服务',
                    content: $('.add-service-box')
                });
            });
        });
    </script>
@endsection