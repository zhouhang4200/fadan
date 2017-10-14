@extends('frontend.layouts.auth')

@section('title', '登录')

@section('content')
    <form method="POST" action="{{ route('login') }}"  class="layui-form">
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
                @include('frontend.layouts.domain')
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
            ,layer = layui.layer;
          
            var error = "{{ $errors->count() > 0 ? '账号或密码错误！' : '' }}";
            var loginError = "{{ session('loginError') ? '异地登录异常！' : '' }}";

            if (error) {
                layer.msg(error, {icon: 5, time:1500},);
            } else if(loginError) {
                layer.msg(loginError, {icon: 5, time:1500},);
            }

            //监听提交
            // form.on('submit(formDemo)', function(data){
                // var token=$('meta[name="_token"]').attr('content');
                // $.ajax({
                //     url: "{{ route('login') }}",
                //     data: {'_token':token} ,
                //     type: "post",
                //     dataType: "json",
                //     success: function (data) {
                //         console.log(1);
                //     },
                // });
            // }); 
        });

    </script>
@endsection

