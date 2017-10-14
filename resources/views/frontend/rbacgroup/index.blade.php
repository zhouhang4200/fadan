@extends('frontend.layouts.app')

@section('title', '商家前台')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('content')
<!--START 主体-->
    <div class="main">
        <div class="wrapper">
            @include('frontend.layouts.rbac-left')

            <div class="right">
                <div class="content">

                    <div class="path"><span>权限组列表</span></div>

                    <div class="layui-tab">
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show" lay-size="sm">
                                <table class="layui-table" lay-size="sm">
                                    <colgroup>
                                        <col width="150">
                                        <col width="200">
                                        <col>
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>组名</th>
                                        <th>权限名</th>
                                        <th>注册时间</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($rbacGroups as $rbacGroup)
                                        <tr class="rbacGroup-td">
                                            <td>{{ $rbacGroup->id }}</td>
                                            <td>{{ $rbacGroup->name }}</td>
                                            <td>
                                            @foreach($rbacGroup->permissions as $permission)
                                            {{ $permission->alias }} &nbsp;&nbsp;
                                            @endforeach
                                            </td>
                                            <td>{{ $rbacGroup->created_at }}</td>
                                            <td>
                                                <div style="text-align: center">
                                                <button class="layui-btn edit"><a href="{{ route('rbacgroups.edit', ['id' => $rbacGroup->id]) }}" style="color: #fff">编辑</a></button>
                                                <button class="layui-btn delete" onclick="del({{ $rbacGroup->id }})">删除</button>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>                               
                            </div>
                        </div>
                        {!! $rbacGroups->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--END 主体-->
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
            layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                $.ajax({
                    type: 'DELETE',
                    url: '/rbacgroups/'+id,
                    success: function (data) {
                        console.log(data);
                        var obj = eval('(' + data + ')');
                        if (obj.code == 1) {
                            layer.msg('删除成功', {icon: 6, time:1500},);
                            window.location.href = '/rbacgroups';                    
                        } else {
                            layer.msg('删除失败', {icon: 5, time:1500},);
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