@extends('backend.layouts.main')

@section('title', ' | 修改权限')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">修改权限</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="{{ route('permissions.update', ['id' => $permission->id]) }}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PUT">
                                <div style="width: 40%">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">模块</label>
                                        <div class="layui-input-block">
                                            <select name="module_id">
                                            <option value="">请选择模块</option>
                                                @foreach($modules as $module)
                                                <option value="{{ $module->id }}" {{ $permission->module && $module->id == $permission->module->id ? 'selected' : '' }} >{{ $module->alias }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">权限名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" lay-verify="required"  value="{{ old('name') ?: $permission->name  }}" autocomplete="off" placeholder="请输入英文权限名" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">中文权限名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="alias" lay-verify="required"  value="{{ old('alias') ?: $permission->alias  }}" autocomplete="off" placeholder="请输入中文权限名" class="layui-input">
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

        var error = "{{ $errors->count() > 0 ? '用户名或别名已经存在或字符过长！' : '' }}";
        var updateFail = "{{ session('updateFail') ?: '' }}";
        var missModule = "{{ session('missModule') ?: '' }}";

        if (error) {
            layer.msg(error, {icon: 5, time:1500});        } else if(updateFail) {
            layer.msg(updateFail, {icon: 5, time:1500});        } else if(missModule) {
            layer.msg(missModule, {icon: 5, time:1500});        }
  
      //……
      
      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
    });  

    </script>
@endsection