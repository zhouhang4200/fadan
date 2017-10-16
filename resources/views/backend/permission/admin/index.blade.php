@extends('backend.layouts.main')

@section('title', ' | 权限列表')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">权限列表</li>
                        </ul>
                        <div class="layui-tab-content">
                        <div style="padding-top:10px; padding-bottom:10px; float:right">
                            <a href="{{ route('admin-permissions.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal">添加后台权限</button></a>
                        </div>
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>权限ID</th>
                                    <th>名称</th>
                                    <th>别名</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($permissions as $permission)
                                        <tr>
                                            <td>{{ $permission->id }}</td>
                                            <td>{{ $permission->name }}</td>
                                            <td>{{ $permission->alias }}</td>
                                            <td>{{ $permission->created_at }}</td>
                                            <td style="text-align: center"><a href="{{ route('admin-permissions.edit', ['id' => $permission->id])  }}"><button class="layui-btn layui-btn layui-btn-normal layui-btn-small">编缉</button></a>
                                            <button class="layui-btn layui-btn layui-btn-normal layui-btn-small" onclick="del({{ $permission->id }})">删除</button></td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        {!! $permissions->render() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
            // 删除
        function del(id)
        {
             layui.use(['form', 'layedit', 'laydate',], function(){
                var form = layui.form
                ,layer = layui.layer;
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'DELETE',
                        url: '/admin/rbac/permissions/'+id,
                        success: function (data) {
                            console.log(data);
                            var obj = eval('(' + data + ')');
                            if (obj.code == 1) {
                                layer.msg('删除成功!', {icon: 6, time:1500},);
                                window.location.href = "{{ route('admin-permissions.index') }}";                    
                            } else {
                                layer.msg('删除失败!', {icon: 5, time:1500},);
                            }
                        }
                    });
                    layer.close(index);
                });        
               
            });
        }; 
        //Demo
        layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            var succ = "{{ session('succ') ?: '' }}";

            if(succ) {
                layer.msg(succ, {icon: 6, time:1500},);
            }

            //监听提交
            form.on('submit(formDemo)', function(data){
                layer.msg(JSON.stringify(data.field));
                return false;
            });
        });

    </script>
@endsection