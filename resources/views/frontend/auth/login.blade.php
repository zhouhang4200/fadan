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
                    {{--<div class="layui-form-item ">--}}
                        {{--{!! Geetest::render() !!}--}}
                    {{--</div>--}}
                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="doLogin" style="width: 100%">登 录</button>
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
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});

        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
            ,layer = layui.layer;

            form.on('submit(doLogin)', function (data) {
                $.post('{{ route('login') }}', {
                    name:data.field.name,
                    password:encrypt(data.field.password),
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

