@extends('backend.layouts.main')

@section('title', ' | 前台模块列表')

@section('css')
    <style>
        .layui-table th, td{
            text-align:center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">前台模块列表</li>
                        </ul>
                        <div class="layui-tab-content">
                        <div style="padding-top:10px; padding-bottom:10px; float:right">
                            <a href="{{ route('modules.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal layui-btn-small">添加前台模块</button></a>
                        </div>
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>名称</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($modules as $module)
                                        <tr>
                                            <td>{{ $module->id }}</td>
                                            <td>{{ $module->alias }}</td>
                                            <td>{{ $module->created_at }}</td>
                                            <td style="text-align: center"><a href="{{ route('modules.edit', ['id' => $module->id])  }}" class="layui-btn layui-btn-normal layui-btn-mini">编缉</a>
                                            <button class="layui-btn layui-btn-normal layui-btn-mini" onclick="del({{ $module->id }})">删除</button></td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        {!! $modules->render() !!}
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
                    url: '/admin/rbac/modules/'+id,
                    success: function (data) {
                        if (data.code == 1) {
                            layer.msg('删除成功!', {icon: 6, time:1500});                            window.location.href = "{{ route('modules.index') }}";                    
                        } else {
                            layer.msg('删除失败!', {icon: 5, time:1500});                        }
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
  
      //……
      
      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
    });  
    </script>
@endsection