@extends('frontend.layouts.app')

@section('title', '账号 - 添加子账号')

@section('css')
    <style>
        .layui-form-label {
            width:65px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="POST" action="{{ route('users.store') }}">
        {!! csrf_field() !!}
        <div style="width: 40%">
            <div class="layui-form-item">
                <label class="layui-form-label">账号:</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="title" value="{{ old('name') }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">邮箱:</label>
                <div class="layui-input-block">
                    <input type="text" name="email" lay-verify="required" value="{{ old('email') }}" placeholder="请输入邮箱" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码:</label>
                <div class="layui-input-block">
                    <input type="password" name="password" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">确认密码:</label>
                <div class="layui-input-block">
                    <input type="password" name="password_confirmation" lay-verify="required" placeholder="请确认密码" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">立即提交</button>
                </div>
            </div>
        </div>
    </form>
@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form', 'table'], function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;

        var error = "{{ $errors->count() > 0 ? '账号名或邮箱已经存在!' : '' }}";
        var addError = "{{ session('addError') ?: '' }}";

        if (error) {
            layer.msg(error, {icon: 5, time:1500},);
        } else if(addError) {
            layer.msg(addError, {icon: 5, time:1500},);
        }

      //……

      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
          form.render();
    });
    </script>
@endsection