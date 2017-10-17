@extends('frontend.layouts.app')

@section('title', '账号 - 子账号列表')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-form-item .layui-input-inline {
            float: left;
            width: 150px;
            margin-right: 10px;
        }
        .layui-form-label {
            width:50px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-inline" >
            <div class="layui-form-item" style="float: left">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-inline">
                <input type="text" name="name" value="{{ $name ?: '' }}" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                </div>

                  <label class="layui-form-label">开始时间</label>
                  <div class="layui-input-inline">
                        <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="年-月-日">
                  </div>

                  <label class="layui-form-label">结束时间</label>
                  <div class="layui-input-inline">
                        <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="年-月-日">
                  </div>
            </div>
            <div style="float: left">
            <button class="layui-btn" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
            <button  class="layui-btn"><a href="{{ route('users.index') }}" style="color:#fff">返回</a></button></div>
        </div>
    </form>
    <div class="layui-tab-item layui-show" lay-size="sm">
        <table class="layui-table" lay-size="sm">
            <colgroup>
                <col width="150">
                <col width="200">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>用户ID</th>
                <th>用户名</th>
                <th>邮箱</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr class="user-td">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                        <div style="text-align: center">
                        <button class="layui-btn edit"><a href="{{ route('users.edit', ['id' => $user->id]) }}" style="color: #fff">编辑</a></button>
                        <button class="layui-btn delete" onclick="del({{ $user->id }})">删除</button>
                        <button class="layui-btn rbac"><a href="{{ route('rbacgroups.create') }}" style="color: #fff">权限</a></button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

{!! $users->appends([
    'name' => $name,
    'startDate' => $startDate,
    'endDate' => $endDate,
])->render() !!}

@endsection
<!--START 底部-->
@section('js')
    <script>
        // 时间插件
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            //常规用法
            laydate.render({
            elem: '#test1'
            });

            //常规用法
            laydate.render({
            elem: '#test2'
            });
        });

        // 删除
        function del(id)
        {
             layui.use(['form', 'layedit', 'laydate',], function(){
                var form = layui.form
                ,layer = layui.layer;
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'DELETE',
                        url: '/users/'+id,
                        success: function (data) {
                            console.log(data);
                            var obj = eval('(' + data + ')');
                            if (obj.code == 1) {
                                window.location.href = '/users';
                            } else {
                                layer.msg('删除失败', {icon: 5, time:1500},);
                            }
                        }
                    });
                    layer.close(index);
                });

            });
        };

        // 权限

    </script>
@endsection