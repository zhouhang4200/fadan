@extends('backend.layouts.main')

@section('title', ' | 添加管理员')

@section('css')
    <style>
        .layui-form-item .layui-input {
            width: 170px;
        }
        .layui-form-item .layui-form-label {
            width: 125px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">添加管理员</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="{{ route('admin-accounts.store') }}">
                                {!! csrf_field() !!}
                                <div style="width: 40%">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">账号</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" lay-verify="required" value="{{ old('name') }}" autocomplete="off" class="layui-input" placeholder="请输入账号名">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">密码</label>
                                        <div class="layui-input-block">
                                            <input type="password" name="password" value="" lay-verify="" placeholder="6-16位" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">立即提交</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        layui.use('form', function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;

        var error = "{{ $errors->count() > 0 ? '按要求填写账号密码！' : '' }}";
        var storeFail = "{{ session('storeFail') ?: '' }}";

        if (error) {
            layer.msg(error, {icon: 5, time:1500});        
        } else if(storeFail) {
            layer.msg(storeFail, {icon: 5, time:1500});
        }
        var succ = "{{ session('succ') ?: '' }}";

        if(succ) {
            layer.msg(succ, {icon: 6, time:1500});
        }
  
      //……
      
      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
    });  


    </script>
@endsection