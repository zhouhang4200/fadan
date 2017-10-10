@extends('layouts.auth')

@section('title', '注册')

@section('css')

@endsection

@section('content')
    <form method="POST" action="{{ route('admin.register') }}"  class="layui-form">
    {!! csrf_field() !!}
        <div class="header">
            <div class="content">
                <div style="font-size: 23px;color:#2196f3;font-weight: 400">千手 · 订单集市</div>
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
                        <input type="email" name="email" required="" lay-verify="required" placeholder="请输入邮箱" value="{{ old('email') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password" required="" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input layui-form-danger">
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
                <p style="text-align: center">
                    © 2017-2018 All Rights Reserved. <a href="">武汉福禄网络科技有限公司</a>
                </p>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        //Demo
        layui.use('form', function(){
            var form = layui.form;
            //监听提交
            form.on('submit(formDemo)', function(data){
                // layer.msg(JSON.stringify(data.field));
                return true;
            });
        });
    </script>
@endsection