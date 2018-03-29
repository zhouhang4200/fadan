@extends('backend.layouts.main')

@section('title', ' | 管理员列表')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">管理员列表</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div style="padding-top:10px; padding-bottom:10px; float:right">
                                <a href="{{ route('admin-accounts.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal layui-btn-small">添加管理员</button></a>
                            </div>
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th style="width:8%">序号</th>
                                    <th>账号名称</th>
                                    <th>账号邮箱</th>
                                    <th>添加时间</th>
                                    <th style="text-align: center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->created_at }}</td>
                                            <td style="text-align: center;">
                                                <a href="{{ route('admin-accounts.edit', ['id' => $user->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini">修改密码</a>
                                            @if (! $user->roles->count() > 0)
                                                <a href="{{ route('admin-groups.create', ['id' => $user->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini">添加角色</a>
                                            @else
                                                <a href="{{ route('admin-groups.show', ['id' => $user->id])  }}" class="layui-btn layui-btn-normal layui-btn-mini" >查看角色</a>
                                            @endif
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        {!! $users->render() !!}
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
            var succ = "{{ session('succ') ?: '' }}";

            if(succ) {
                layer.msg(succ, {icon: 6, time:1500});
            }

            //监听提交
            form.on('submit(formDemo)', function(data){
                layer.msg(JSON.stringify(data.field));
                return false;
            });
        });
    </script>
@endsection