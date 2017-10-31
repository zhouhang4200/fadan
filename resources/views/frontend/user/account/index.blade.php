@extends('frontend.layouts.app')

@section('title', '账号 - 我的账号')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
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
                <th width="5%">操作</th>
            </tr>
            </thead>
            <tbody>
                <tr class="user-td">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                        <a href="{{ route('home-accounts.edit', ['id' => $user->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini edit">修改密码</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            var succ = "{{ session('succ') ?: '' }}";

            if(succ) {
                layer.msg(succ, {icon: 6, time:1500});
            }
            form.render();
        });  

    </script>
@endsection