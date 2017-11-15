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

    <form method="POST" action="{{ route('register') }}"  class="layui-form">
    {!! csrf_field() !!}
        <div class="header">
            <div class="content">
                <a href="">
                    <span class="logo"></span>
                </a>
            </div>
        </div>
        <div class="main">
            <div class="container">
                <div class="input-container">
                    <div class="title">注册</div>
                    <div class="layui-form-item">
                        <input type="text" name="name" required="" lay-verify="required" placeholder="请输入账号" value="{{ old('name') }}" autocomplete="off" class="layui-input layui-form-danger">
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
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" style="width: 100%">注 册</button>
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
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
            ,layer = layui.layer;
          
            var GeetestError = "{{ $errors->count() > 0  && array_key_exists('geetest_challenge', $errors->toArray()) ? '请正确完成验证码操作!' : '' }}";
            var errorName = "{{ $errors->count() > 0 && array_key_exists('name', $errors->toArray()) && $errors->toArray()['name'] ? '用户名已经存在!' : '' }}";
            var errorPassword = "{{ $errors->count() > 0 && array_key_exists('password', $errors->toArray()) && $errors->toArray()['password'] ? '请按要求填写密码!' : '' }}";
            var loginError = "{{ session('loginError') ? '异地登录异常！' : '' }}";
            var errorEmail = "{{ $errors->count() > 0 && array_key_exists('email', $errors->toArray()) && $errors->toArray()['email'] ? '邮箱已经存在!' : '' }}";

            if (GeetestError) {
                layer.msg(GeetestError, {icon: 5, time: 1500});
            } else if (errorName) {
                layer.msg(errorName, {icon: 5, time: 1500});
            } else if (errorPassword) {
                layer.msg(errorPassword, {icon: 5, time: 1500});
            } else if (errorEmail) {
                layer.msg(errorEmail, {icon: 5, time: 1500});
            } else if (loginError) {
                layer.msg(loginError, {icon: 5, time: 1500});
            }
        });
    </script>
@endsection