@extends('frontend.layouts.auth')

@section('title', '注册')

@section('css')
    <style>
        .input-container input {
            height:40px;
        }
    </style>
@endsection

@section('content')

    <form method="" action=""  class="layui-form">
    {!! csrf_field() !!}
        <div class="header">
            <div class="content">
                <a href="">
                    <span class="logo">淘宝发单平台</span>
                </a>
            </div>
        </div>
        <div class="main">
            <div class="container">
                <div class="input-container">
                    <div class="title">注册</div>
                    <div class="layui-form-item">
                        <input type="text" name="name" required="" lay-verify="required" placeholder="请输入账号名 (可写中文)" value="{{ old('name') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>
                    <div class="layui-form-item">
                        <input type="text" name="username" required="" lay-verify="required" placeholder="请输入昵称" value="{{ old('username') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>
                    <div class="layui-form-item">
                        <input type="email" name="email" required="" lay-verify="required|email" placeholder="请输入邮箱 (用于找回密码)" value="{{ old('email') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe64c;</i>
                    </div>
                    <div class="layui-form-item">
                        <input type="text" name="qq" required="" lay-verify="required|number" placeholder="请输入QQ" value="{{ old('qq') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe63a;</i>
                    </div>
                    <div class="layui-form-item">
                        <input type="text" name="phone" required="" lay-verify="required|phone" placeholder="请输入手机号" value="{{ old('phone') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe63b;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password" required="" lay-verify="required" placeholder="请输入最少6位数密码" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon"> &#x1005;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password_confirmation" required="" lay-verify="required" placeholder="再次输入密码" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon"> &#x1005;</i>
                    </div>
                    <div class="layui-form-item ">
                        {!! Geetest::render() !!}
                    </div>
                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="register" style="width: 100%">注 册</button>
                    </div>
                    <div class="register-and-forget-password">
                        <a class="register" target="_blank" href="{{ route('login') }}">登录</a>
                        <a class="forget-password" href="{{ route('password.request') }}">忘记密码？</a>
                        <div class="layui-clear"></div>
                    </div>
                </div>
                @include('frontend.auth.footer')
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});

        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
            ,layer = layui.layer;

            form.on('submit(register)', function (data) {
                $.post('{{ route('register') }}', {
                    name:data.field.name,
                    password:encrypt(data.field.password),
                    password_confirmation:encrypt(data.field.password_confirmation),
                    email:data.field.email,
                    username:data.field.username,
                    qq:data.field.qq,
                    phone:data.field.phone,
                    geetest_challenge:data.field.geetest_challenge,
                    geetest_seccode:data.field.geetest_seccode,
                    geetest_validate:data.field.geetest_validate
                }, function (result) {
                    if (result.status == 1) {
                        location.reload();
                    } else {
                        layer.msg(result.message);
                    }
                    return false;
                });
                return false;
            });
        });
    </script>
@endsection