@extends('frontend.layouts.app')

@section('title', '账号 - 我的账号')

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
                <tr class="user-td">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                        <div style="text-align: center">
                        <button class="layui-btn layui-btn-normal edit"><a href="{{ route('home-accounts.edit', ['id' => $user->id]) }}" style="color: #fff">修改密码</a></button>
                        </div>
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
            layer.msg(succ, {icon: 6, time:1500},);
        }
  
          //……
          
          //但是，如果你的HTML是动态生成的，自动渲染就会失效
          //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
            form.render();
        });  

    </script>
@endsection