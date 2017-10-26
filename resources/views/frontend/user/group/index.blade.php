@extends('frontend.layouts.app')

@section('title', '账号 - 子账号分组列表')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-inline" style="float:left">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                    <select name="name" lay-verify="" lay-search="">
                        <option value="">输入名字或直接选择</option>
                        @foreach($childUsers as $childUser)
                        <option value="{{ $childUser->id }}" {{ $name && $name == $childUser->id ? 'selected' : '' }}>{{ $childUser->name }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
        </div>
        <div style="float: left">
            <div class="layui-inline" >
                <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1">查找</button>
                <a href="{{ route('user-groups.index') }}" class="layui-btn layui-btn-normal layui-btn-small">返回</a>
            </div>
        </div>
    </form>
        <table class="layui-table"  lay-size="sm">
            <colgroup>
                <col width="150">
                <col width="200">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>用户ID</th>
                <th>用户名</th>
                <th>用户邮箱</th>
                <th>权限组</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                    @foreach($user->rbacGroups as $rbacGroup)
                        {{ $rbacGroup->alias }}
                    @endforeach
                    </td>
                    <td style="text-align: center">
                        <a href="{{ route('user-groups.edit', ['id' => $user->id]) }}" class="layui-btn layui-btn-normal layui-btn-small">编缉</a>
                        <button class="layui-btn layui-btn-normal layui-btn-small" onclick="del({{ $user->id }})">删除</button>
                    </td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>
    </div>

    {!! $users->appends([
        'name' => $name,
    ])->render() !!}

@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
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

        // 删除
        function del(id)
        {
            layui.use(['form', 'layedit', 'laydate',], function(){
                var form = layui.form
                ,layer = layui.layer;
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'DELETE',
                        url: '/user-groups/'+id,
                        success: function (data) {                    
                            if (data.code == 1) {
                                layer.msg('删除成功!', {icon: 6, time:1500},);
                                window.location.href = "{{ route('user-groups.index') }}";                    
                            } else {
                                layer.msg('删除失败!', {icon: 5, time:1500},);
                            }
                        }
                    });
                    layer.close(index);
                });        
               
            });
        };

    </script>
@endsection