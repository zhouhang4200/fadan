@extends('frontend.layouts.auth')

@section('title', '密码找回')

@section('css')
    <style>
        .input-container input {
            height:40px;
        }
    </style>
@endsection

@section('content')

    <form method="POST" action="{{ route('password.email') }}"  class="layui-form">
    {!! csrf_field() !!}
        <div class="header">
            <div class="content">
                <div style="font-size: 23px;color:#2196f3;font-weight: 400">千手 · 订单集市</div>
            </div>
        </div>
        <div class="main">
            <div class="container">
                <div class="input-container">
                    <div class="title">注册邮件地址</div>

                    <div class="layui-form-item">
                        <input type="email" name="email" required="" lay-verify="required" placeholder="请输入注册邮箱" value="{{ old('email') }}" autocomplete="off" class="layui-input layui-form-danger">
                        <i class="layui-icon icon">&#xe612;</i>
                    </div>

                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" style="width: 100%">发 送</button>
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
          
            var error = "{{ $errors->count() > 0 ? '请填写注册时的邮箱!' : '' }}";
            var succ = "{{ session('status') ? '发送成功!' : '' }}";

            if (error) {
                layer.msg(error, {icon: 5, time:2000},);
            } else if (succ) {
                layer.msg(succ, {icon: 6, time:2000},);
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