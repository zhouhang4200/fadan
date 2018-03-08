@extends('frontend.layouts.auth')

@section('title', '登录')

@section('content')
    <form method="POST" action="{{ route('login') }}"  class="layui-form">
    {!! csrf_field() !!}
        <div class="header">
            <div class="content">
                <a href="">
                    <h1 class="logo">淘宝发单平台</h1>
                </a>
            </div>
        </div>
        <div class="main">
            <div class="container">
                <div class="input-container">
                    <div class="title">登录</div>
                    <div class="layui-form-item">
                        <input type="text" name="name" required="" lay-verify="required" placeholder="请输入账号" value="{{ old('name') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password" required="" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon"> &#x1005;</i>
                    </div>
                    <div class="layui-form-item ">
                        {!! Geetest::render() !!}
                    </div>
                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" style="width: 100%">登 录</button>
                    </div>
                    <div class="register-and-forget-password">
                        <a class="register" target="_blank" href="{{ route('register') }}">新用户注册</a>
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
            var error = "{{ $errors->count() > 0 ? '账号被禁用或密码错误！' : '' }}";
            var loginError = "{{ session('loginError') ? '异地登录异常！' : '' }}";
            var forbidden = "{{ session('forbidden') ?: '' }}";

            if(GeetestError) {
                layer.msg(GeetestError, {icon: 5, time:1500});
            } else if(forbidden) {
                layer.msg(loginError, {icon: 5, time:1500});
            } else if (error) {
                layer.msg(error, {icon: 5, time:1500});
            } else if(loginError) {
                layer.msg(loginError, {icon: 5, time:1500});
            }
        });

    </script>
@endsection

