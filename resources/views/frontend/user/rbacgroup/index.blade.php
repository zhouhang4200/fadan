@extends('frontend.layouts.app')

@section('title', '账号 - 岗位列表')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-form-label {
            width:100px;
        }
        .layui-table th, .layui-table td {
            text-align:center;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <div style="padding-top:5px; padding-bottom:10px; float:right">
        <a href="{{ route('rbacgroups.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal layui-btn-small">添加岗位</button></a>
    </div>
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>序号</th>
            <th>岗位名称</th>
            <th>岗位员工</th>
            <th>拥有权限</th>
            <th style="width:12%">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rbacGroups as $rbacGroup)
            <tr class="rbacGroup-td">
                <td>{{ $rbacGroup->id }}</td>
                <td>{{ $rbacGroup->name }}</td>
                <td>{{ employees($rbacGroup->id) }}</td>
                <td>
                @foreach($rbacGroup->permissions as $permission)
                {{ $permission->alias }} &nbsp;&nbsp;
                @endforeach
                </td>
                <td>
                    <div style="text-align: center">
                    <a href="{{ route('rbacgroups.edit', ['id' => $rbacGroup->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini">编辑</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" onclick="del({{ $rbacGroup->id }})">删除</button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! $rbacGroups->render() !!}
@endsection
<!--START 底部-->
@section('js')
    <script>
    // 删除
    function del(id)
    {
         layui.use(['form', 'layedit', 'laydate',], function(){
            var form = layui.form
            ,layer = layui.layer;
            layer.confirm('确定删除岗位吗?', {icon: 3, title:'提示'}, function(index){
                $.ajax({
                    type: 'DELETE',
                    url: '/rbacgroups/'+id,
                    success: function (data) {
                        if (data.code == 1) {
                            layer.msg('删除成功', {icon: 6, time:1500}); 
                            window.location.href = '/rbacgroups';
                        } else {
                            layer.msg('删除失败', {icon: 5, time:1500});                        }
                    }
                });
                layer.close(index);
            });
        });
    };

    layui.use('form', function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;
        var succ = "{{ session('succ') ?: '' }}";

        if(succ) {
            layer.msg(succ, {icon: 6, time:1500});        }
        form.render();
    });
    </script>
@endsection