@extends('backend.layouts.auth')

@section('title', ' | 登录')

@section('css')

@endsection

@section('content')
    <div class="login">
        <div class="login-warp">
            <div class="login-container">
                <div class="logo"><img src=""></div>
                <div class="container">
                    <div class="warp">
                        <div class="content">
                            <div class="title">
                                <h3>运营管理中心</h3>
                                <span class="txt"></span>
                            </div>
                            <form method="POST" action="{{ route('admin.post.login') }}"  class="layui-form">
                                {!! csrf_field() !!}
                                <div class="layui-form-item">
                                    <input type="text" name="name" required="" lay-verify="required" placeholder="请输入账号" value="{{ old('name') }}" autocomplete="off" class="layui-input layui-form-danger input-text">
                                    <i class="layui-icon icon">&#xe612;</i>
                                </div>
                                <div class="layui-form-item ">
                                    <input type="password" name="password" required="" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input layui-form-danger input-text">
                                    <i class="layui-icon icon"> &#x1005;</i>
                                </div>
                                <div class="layui-form-item ">
                                    <input type="password" name="code" required="" lay-verify="required" placeholder="请输入动态口令" autocomplete="off" class="layui-input layui-form-danger input-text">
                                    <i class="layui-icon icon"> &#x1005;</i>
                                </div>
                                <div class="layui-form-item">
                                    <button class="submit" lay-submit lay-filter="formDemo" style="width: 100%">登 录</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

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