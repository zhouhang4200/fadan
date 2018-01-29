@extends('backend.layouts.main')

@section('title', ' | 旺旺黑名单')

@section('css')
    <style>
        .layui-form-pane .layui-form-label {
            width: 120px;
            padding: 8px 15px;
            height: 36px;
            line-height: 20px;
            border-radius: 2px 0 0 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            box-sizing: border-box;
        }
        blockquote:before{
            content: ""
        }
        .theme-whbl blockquote, .theme-whbl blockquote.pull-right{
            border-color: #e6e6e6;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>旺旺黑名单</span></li>
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
                                <input type="text" class="form-control" name="wang_wang"  placeholder="旺旺号" value="{{ $wangWang }}">
                            </div>
                            <button type="submit" class="btn btn-success">搜索</button>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="create">添加</button>
                    </div>

                </header>
                <div class="main-box-body clearfix">

                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $wangWangBlacklist->total() }}　本页显示：{{$wangWangBlacklist->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $wangWangBlacklist->appends([
                                      'wang_wang' => $wangWang,
                                  ])->render() !!}
                            </div>
                        </div>
                    </div>
                    <table class="layui-table layui-form" lay-size="sm">
                        <thead>
                        <tr>
                            <th>旺旺号</th>
                            <th>添加人</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($wangWangBlacklist as $item)
                            <tr>
                                <td>{{ $item->wang_wang }}</td>
                                <td>{{ optional($item->adminUser)->name }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>
                                    <button class="layui-btn layui-btn-normal" data-id="{{ $item->id }}"  lay-submit="" lay-filter="delete">删除</button>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $wangWangBlacklist->total() }}　本页显示：{{$wangWangBlacklist->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $wangWangBlacklist->appends([
                                      'wang_wang' => $wangWang,
                                  ])->render() !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="add-blacklist-box" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">旺旺名</label>
                <div class="layui-input-inline">
                    <input type="text" name="wang_wang" lay-verify="required" placeholder="请输入旺旺名" autocomplete="off"
                           class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="create-save">保存</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        //Demo
        layui.use(['form', 'layedit', 'laytpl', 'element', 'laydate', 'table', 'upload'], function(){
            var form = layui.form, layer = layui.layer, element = layui.element, table=layui.table;

            // 添加弹窗
            form.on('submit(create)', function (data) {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加黑名单',
                    content: $('.add-blacklist-box')
                });
            });
            // 保存
            form.on('submit(create-save)', function (data) {
                $.post('{{ route('customer.wang-wang-blacklist.store') }}', {wang_wang:data.field.wang_wang}, function (result) {
                    layer.msg(result.message);
                    reload();
                }, 'json');
                return false;
            });
            // 删除
            form.on('submit(delete)', function (data) {
                layer.confirm('确定要删除吗？', function () {
                    $.post('{{ route('customer.wang-wang-blacklist.delete') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        reload();
                    }, 'json');
                });
                return false;
            });
        });

    </script>
@endsection