@extends('backend.layouts.main')

@section('title', ' | 商品模版')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商品模版</span></li>
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
                                <input type="text" class="form-control" name="name"  placeholder="输入模版名称" value="{{ $name }}">
                            </div>
                            <button type="submit" class="btn btn-success">搜索</button>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <button  class="btn btn-primary pull-right" id="add-goods-template">
                            <i class="glyphicon glyphicon-plus"></i> 添加模版
                        </button>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>模版ID</th>
                                <th>模版名称</th>
                                <th>状态</th>
                                <th>添加时间</th>
                                <th>添加人员</th>
                                <th width="5%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($goodsTemplates as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td><a href="{{ route('goods.template.show', ['id' => $item->id])  }}"><button class="layui-btn layui-btn layui-btn-normal layui-btn-small">编缉</button></a></td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        {{ $goodsTemplates->appends(['name' => $name])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="add-template" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <input type="hidden" name="admin_user_id" value="{{ Auth::user()->id }}">
            <div class="layui-form-item">
                <label class="layui-form-label">模版名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="required" placeholder="请输入模版名称" autocomplete="off" class="layui-input">
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
        layui.use('form', function(){
            var form = layui.form;
            //监听提交
            form.on('submit(save-template)', function(data){
                $.post('{{ route('goods.template.store') }}', {data:data.field}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            // 添加商品模版
            $('#add-goods-template').on('click', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加商品模版',
                    content: $('.add-template')
                });
            });
        });
    </script>
@endsection