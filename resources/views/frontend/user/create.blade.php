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
                    <input type="text" name="name" lay-verify="title" value="{{ old('name') }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">邮箱:</label>
                <div class="layui-input-block">
                    <input type="text" name="email" lay-verify="required" value="{{ old('email') }}" placeholder="邮箱可为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码:</label>
                <div class="layui-input-block">
                    <input type="password" name="password" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
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
                    <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                </div>
            </div>
        </div>
    </form>
@endsection
<!--START 底部-->
@section('js')
    <script>

    </script>
@endsection