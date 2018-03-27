@extends('backend.layouts.main')

@section('title', ' | 服务')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商户承包商品配置</span></li>
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
                                <input type="text" class="form-control" name="km_goods_id"  placeholder="卡门商品ID" value="{{ $kmGoodsId }}">
                            </div>
                            <button type="submit" class="btn btn-success">搜索</button>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <button class="layui-btn layui-btn-samll layui-btn-normal" id="add">添加</button>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>商户ID</th>
                                <th>卡门商品ID</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th width="15%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($goodsContractor as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->user_id }}</td>
                                    <td>{{ $item->km_goods_id }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->updated_at }}</td>
                                    <td>
                                        <button class="layui-btn layui-btn-mini layui-btn-normal" lay-submit="" lay-filter="destroy" data-id="{{ $item->id }}">删除</button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        {{ $goodsContractor->appends(['km_goods_id' => $kmGoodsId])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="add-box" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">商户ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="user_id" lay-verify="required" placeholder="请输入商户ID" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">卡门商品ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="km_goods_id" lay-verify="required" placeholder="请输入卡门商品ID" autocomplete="off" class="layui-input" value="">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="store">确定添加</button>
            </div>
        </form>
    </div>

@endsection

@section('js')
    <script>
        layui.use('form', function(){
            var form = layui.form;
            //添加
            form.on('submit(store)', function(data){
                $.post('{{ route('businessman.goods-contractor.store') }}', {data:data.field}, function (result) {
                    layer.msg(result.message);
                    if (result.code == 1) {
                        reload();
                    }
                }, 'json');
                return false;
            });

            // 添加弹窗
            $('#add').on('click', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加',
                    content: $('.add-box')
                });
            });

            //删除
            form.on('submit(destroy)', function(data){
                layer.confirm('您确认要删除吗?', function(index){
                    $.post('{{ route('businessman.goods-contractor.destroy') }}', {id:data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        if (result.code == 1) {
                            reload();
                        }
                    }, 'json');
                });
                return false;
            });
        });
    </script>
@endsection