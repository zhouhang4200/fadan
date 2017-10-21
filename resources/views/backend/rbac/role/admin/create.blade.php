@extends('backend.layouts.main')

@section('title', ' | 添加后台角色')

@section('css')
    <style>
        .layui-tab-content input {
            width:800px;
        }
        .table {
            width:800px;
        }
        .layui-form-item .layui-input-inline {
            float: left;
            width: 135px;
            margin-right: 10px;
        }
        .layui-form-checkbox span {
            padding: 0 5px;
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
                            <li class="layui-this" lay-id="add">添加后台角色</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="{{ route('admin-roles.store') }}">
                            {!! csrf_field() !!}
                                <div style="width: 40%">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">角色名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" lay-verify="required" value="{{ old('name') }}" autocomplete="off" placeholder="请输入角色名" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">别名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="alias" lay-verify="required" value="{{ old('alias') }}" autocomplete="off" placeholder="请输入别名" class="layui-input">
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">权限</label>
                                            <div class="layui-input-block">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="col-md-1 text-center">模块</th>
                                                    <th class="col-md-10 text-center">权限</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($modulePermissions as $modulePermission)
                                                    <tr>
                                                        <td>{{ $modulePermission->alias }}</td>
                                                        <td>
                                                                <div class="layui-form-item" pane="">
                                                
                                                                @foreach($modulePermission->permissions as $permission)
                                                                <div class="layui-input-inline">
                                                                  <input type="checkbox" name="permissions[]" lay-skin="primary" title="{{ $permission->alias }}" value="{{ $permission->id }}">
 
                                                                </div>
                                                                @endforeach
                                                              </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
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
        var createFail = "{{ session('createFail') ?: '' }}";

        if (error) {
            layer.msg(error, {icon: 5, time:1500},);
        } else if(createFail) {
            layer.msg(createFail, {icon: 5, time:1500},);
        }
  
      //……
      
      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
      form.render();
    });  


    </script>
@endsection