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

        .layui-inline .layui-form-label {
            width: 60px;
        }
        .layui-form-label {
            width: 55px;
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
                <div class="layui-inline">
                    <label class="layui-form-label">搜索选择框</label>
                    <div class="layui-input-inline">
                    <select name="name" lay-verify="required" lay-search="">
                        <option value="">输入名字或直接选择</option>
                        @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ $name && $name == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                        @endforeach
                    </select>
                    </div>
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
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
            <button  class="layui-btn layui-btn-normal"><a href="{{ route('users.index') }}" style="color:#fff">返回</a></button></div>
        </div>
    </form>
    <div class="layui-tab-item layui-show" lay-size="sm">
    <div style="padding-top:10px; padding-bottom:10px; float:right">
        <a href="{{ route('users.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal">添加子账号</button></a>
    </div>
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
                        <button class="layui-btn layui-btn-normal edit"><a href="{{ route('users.edit', ['id' => $user->id]) }}" style="color: #fff">编辑</a></button>
                        <button class="layui-btn layui-btn-normal delete" onclick="del({{ $user->id }})">删除</button>
                        <button class="layui-btn layui-btn-normal rbac"><a href="{{ route('user-groups.create', ['id' => $user->id]) }}" style="color: #fff">权限</a></button>
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