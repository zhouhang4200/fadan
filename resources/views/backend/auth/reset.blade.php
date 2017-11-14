@extends('backend.layouts.auth')

@section('title', '重置密码')

@section('css')
    <style>
        .input-container input {
            height:40px;
        }
    </style>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.password.request') }}"  class="layui-form">
    {!! csrf_field() !!}
        <div class="header">
            <div class="content">
                <div style="font-size: 23px;color:#2196f3;font-weight: 400">千手 · 订单集市</div>
            </div>
        </div>
        <div class="main">
            <div class="container">
                <div class="input-container">
                    <div class="title">重置密码</div>
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="layui-form-item">
                        <input type="email" name="email" required="" lay-verify="required" placeholder="请输入邮箱" value="{{ old('email') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password" required="" lay-verify="required" placeholder="请输入至少6位数密码" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon"> &#x1005;</i>
                    </div>
                    <div class="layui-form-item ">
                        <input type="password" name="password_confirmation" required="" lay-verify="required" placeholder="再次输入密码" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon"> &#x1005;</i>
                    </div>
                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" style="width: 100%">重 置</button>
                    </div>
                    <div class="register-and-forget-password">
                        <a class="register" target="_blank" href="{{ route('/admin/login') }}">登录</a>
                        <div class="layui-clear"></div>
                    </div>
                </div>
                @include('backend.layouts.domain')
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
            ,layer = layui.layer;
          
            var errorPassword = "{{ $errors->count() > 0 && array_key_exists('password', $errors->toArray()) && $errors->toArray()['password'] ? '请按要求填写密码!' : '' }}";
            var errorEmail = "{{ $errors->count() > 0 && array_key_exists('email', $errors->toArray()) && $errors->toArray()['email'] ? '邮箱填写错误!' : '' }}";

            if(errorEmail) {
                layer.msg(errorEmail, {icon: 5, time:1500},);
            } else if (errorPassword) {
                layer.msg(errorPassword, {icon: 5, time:1500},);
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