@extends('backend.layouts.main')

@section('title', ' | 商品模版')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">商品模版</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>模版ID</th>
                                    <th>模版名称</th>
                                    <th>状态</th>
                                    <th>添加时间</th>
                                    <th>添加人员</th>
                                    <th>操作</th>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        //Demo
        layui.use('form', function(){
            var form = layui.form;

            //监听提交
            form.on('submit(formDemo)', function(data){
                layer.msg(JSON.stringify(data.field));
                return false;
            });
        });
    </script>
@endsection