@extends('frontend.layouts.app')

@section('title', '账号 - 修改密码')

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="POST" action="{{ route('home-accounts.update', ['id' => $user->id]) }}">
        {!! csrf_field() !!}
        <input type="hidden" name="_method" value="PUT">
            <div class="layui-form-item">
                <label class="layui-form-label">账号(不可更改)</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required" value="{{ old('name') ?: $user->name }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" value="" lay-verify="" placeholder="最少6位,不填写则为原密码" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">类型</label>
                <div class="layui-input-inline">
                    <input type="radio" name="type" value="1" title="接单" @if($user->type == 1) checked="" @endif>
                    <input type="radio" name="type" value="2" title="发单" @if($user->type == 2) checked="" @endif>
                </div>
                <div class="layui-form-mid layui-word-aux">设置为接单：则工作台显您接的单，发单：则工作显示您发出的单</div>
            </div>
            <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">提交</button>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;
            var updateFail = "{{ session('updateFail') ?: '' }}";

            if(updateFail) {
                layer.msg(updateFail, {icon: 5, time:1500},);
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