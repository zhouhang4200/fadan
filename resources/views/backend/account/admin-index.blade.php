@extends('backend.layouts.main')

@section('title', ' | 后端账号')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">后端账号</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>账号ID</th>
                                    <th>名称</th>
                                    <th>邮箱</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($adminUsers as $adminUser)
                                        <tr>
                                            <td>{{ $adminUser->id }}</td>
                                            <td>{{ $adminUser->name }}</td>
                                            <td>{{ $adminUser->email }}</td>
                                            <td>{{ $adminUser->created_at }}</td>
                                            <td style="text-align: center;"><a href="{{ route('admin-groups.create', ['id' => $adminUser->id])  }}"><button class="layui-btn layui-btn layui-btn-normal layui-btn-small">添加权限</button></a></td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        {!! $adminUsers->render() !!}
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