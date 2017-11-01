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
                    <input type="text" name="name" lay-verify="required|length" value="{{ old('name') }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码:</label>
                <div class="layui-input-block">
                    <input type="password" name="password" lay-verify="required|pass" placeholder="请输入密码" autocomplete="off" class="layui-input">
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

            form.verify({
                length: [
                    /^\S{1,30}$/
                    ,'长度超出允许范围'
                ]
                ,pass: [
                    /^[\S]{6,12}$/
                    ,'密码必须6到12位，且不能出现空格'
                ] 
            });     
            form.render();
        });
    </script>
@endsection