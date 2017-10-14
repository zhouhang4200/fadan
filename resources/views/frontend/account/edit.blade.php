@extends('frontend.layouts.app')

@section('title', '账号 - 编辑子账号')

@section('submenu')
@include('frontend.account.submenu')
@endsection

@section('main')
<form class="layui-form" method="POST" action="{{ route('accounts.update', ['id' => $user->id]) }}">
{!! csrf_field() !!}
    <input type="hidden" name="_method" value="PUT">
        <div class="layui-form-item">
            <label class="layui-form-label">账号</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="title" value="{{ old('name') ?: $user->name }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">邮箱</label>
            <div class="layui-input-block">
                <input type="text" name="email" lay-verify="required" value="{{ old('email') ?: $user->email }}" placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-block">
                <input type="password" name="password" value="" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-block">
                <input type="password" name="password_confirmation" value="" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="" lay-filter="demo1">编辑</button>
        </div>
    </div>
</form>
@endsection

@section('js')
<script>

</script>
@endsection