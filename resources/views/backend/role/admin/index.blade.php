@extends('backend.layouts.main')

@section('title', ' | 角色列表')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">角色列表</li>
                        </ul>
                        <div class="layui-tab-content">
                        <div style="padding-top:10px; padding-bottom:10px; float:right">
                            <a href="{{ route('admin-roles.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal">添加后台角色</button></a>
                        </div>
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>角色ID</th>
                                    <th>名称</th>
                                    <th>别名</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $role)
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>{{ $role->alias }}</td>
                                            <td>{{ $role->created_at }}</td>
                                            <td style="text-align: center"><a href="{{ route('admin-roles.edit', ['id' => $role->id])  }}"><button class="layui-btn layui-btn layui-btn-normal layui-btn-small">编缉</button></a>
                                            <button class="layui-btn layui-btn layui-btn-normal layui-btn-small" onclick="del({{ $role->id }})">删除</button></td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        {!! $roles->render() !!}
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

        // 删除
    function del(id)
    {
         layui.use(['form', 'layedit', 'laydate',], function(){
            var form = layui.form
            ,layer = layui.layer;
            layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                $.ajax({
                    type: 'DELETE',
                    url: '/admin/rbac/admin-roles/'+id,
                    success: function (data) {
                        console.log(data);
                       
                        if (data.code == 1) {
                            layer.msg('删除成功!', {icon: 6, time:1500},);
                            window.location.href = "{{ route('admin-roles.index') }}";                    
                        } else {
                            layer.msg('删除失败!', {icon: 5, time:1500},);
                        }
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
            layer.msg(succ, {icon: 6, time:1500},);
        }
  
      //……
      
      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
    });  
    </script>
@endsection