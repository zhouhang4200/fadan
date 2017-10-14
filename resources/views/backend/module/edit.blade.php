@extends('backend.layouts.main')

@section('title', ' | 修改模块')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">修改模块</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="{{ route('roles.update', ['id' => $role->id]) }}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PUT">
                                <div style="width: 40%">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">模块名:</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" lay-verify="required" value="{{ old('name') ?: $role->name }}" autocomplete="off" placeholder="请输入模块名" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">别名:</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="alias" lay-verify="required" value="{{ old('alias') ?: $role->alias  }}" autocomplete="off" placeholder="请输入别名" class="layui-input">
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
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

        var error = "{{ $errors->count() > 0 ? '用户名或别名已经存在！' : '' }}";
        var updateFail = "{{ session('updateFail') ?: '' }}";

        if (error) {
            layer.msg(error, {icon: 5, time:1500},);
        } else if(updateFail) {
            layer.msg(updateFail, {icon: 5, time:1500},);
        }
  
      //……
      
      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
    });  


    </script>
@endsection