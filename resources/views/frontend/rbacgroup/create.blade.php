@extends('frontend.layouts.app')

@section('title', '权限 - 添加权限组')

@section('css')
<style>
    .layui-form-label {
        width:65px;
    }
</style>
@endsection

@section('submenu')
@include('frontend.rbacgroup.submenu')
@endsection

@section('main')
<form class="layui-form" method="POST" action="{{ route('rbacgroups.store') }}">
    {!! csrf_field() !!}
    <div style="width: 40%">
        <div class="layui-form-item">
            <label class="layui-form-label">权限组名:</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="title" value="{{ old('name') }}" autocomplete="off" placeholder="请输入组名" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注:</label>
            <div class="layui-input-block">
                <input type="text" name="remark" lay-verify="" value="{{ old('remark') }}" placeholder="备注可为空" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">权限清单</label>
            <div class="layui-input-block">
            @foreach($permissions as $permission)
                <input type="checkbox" name="permissionIds[]" value="{{ $permission->id }}" title="{{ $permission->alias }}">
            @endforeach
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
layui.use('form', function(){
    var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
    var layer = layui.layer;

    var error = "{{ $errors->count() > 0 ? '组名不可为空！' : '' }}";
    var missError = "{{ session('missError') ?: '' }}";

    if (error) {
        layer.msg(error, {icon: 5, time:1500},);
    } else if(missError) {
        layer.msg(missError, {icon: 5, time:1500},);
    }

  //……

  //但是，如果你的HTML是动态生成的，自动渲染就会失效
  //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
});
</script>
@endsection