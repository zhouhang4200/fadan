@extends('frontend.layouts.app')

@section('title', '账号 - 子账号列表')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-form-item" >
                <div class="layui-inline">
                    <div class="layui-input-inline">
                    <select name="name" lay-verify="" lay-search="">
                        <option value="">输入名字或直接选择</option>
                        @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ $name && $name == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="年-月-日">
                </div>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="年-月-日">
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                    <a href="{{ route('users.index') }}"   class="layui-btn layui-btn-normal layui-btn-small">返回</a>

                </div>
            <a href="{{ route('users.create') }}" class="layui-btn layui-btn-normal layui-inline layui-btn-small fr">添加子账号 </a>

        </div>

    </form>
    <div class="layui-tab-item layui-show" lay-size="sm">
    <div style="padding-top:10px; padding-bottom:10px; float:right">

    </div>
        <table class="layui-table" lay-size="sm">
            <thead>
            <tr>
                <th style="width:7%">账号id</th>
                <th>账号名</th>
                <th>类型</th>
                <th>权限组</th>
                <th width="15%">注册时间</th>
                <th width="27%">操作</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr class="user-td" style="text-align: center">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ config('user.type')[$user->type ]}}</td>
                    <td>
                    @forelse($user->rbacGroups as $name)
                        {{ $name->name }}&nbsp;&nbsp;
                    @empty
                    --
                    @endforelse
                    </td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                            <a href="{{ route('users.edit', ['id' => $user->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini edit">编辑账号</a>
                            <button class="layui-btn layui-btn-normal layui-btn-mini delete" onclick="del({{ $user->id }})">删除账号</button>
                        @if($user->rbacGroups->count() == 0)
                            <a href="{{ route('user-groups.create', ['id' => $user->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini rbac">添加权限</a>
                        @else
                            <a href="{{ route('user-groups.edit', ['id' => $user->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini rbac">编辑权限</a>
                        @endif
                        @if($user->rbacGroups->count() > 0)
                            <button class="layui-btn layui-btn-normal layui-btn-mini delete" onclick="delPermission({{ $user->id }})">删除权限</button>
                        @endif
                    </td>
                </tr>
            @empty
            @endforelse
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
                            if (data.code == 1) {
                                layer.msg(data.message, {icon: 6, time:1000},);
                                window.location.href = "{{ route('users.index') }}";
                            } else {
                                layer.msg('删除失败', {icon: 5, time:1500},);
                            }
                        }
                    });
                    layer.close(index);
                });

            });
        };

        // 删除
        function delPermission(id)
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
                                window.location.href = "{{ route('users.index') }}";                    
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