@extends('frontend.layouts.app')

@section('title', '权限 - 修改权限组')

@section('css')
    <style>
        .layui-form input {
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
    </style>
@endsection

@section('submenu')
    @include('frontend.rbacgroup.submenu')
@endsection

@section('main')
<form class="layui-form" method="POST" action="{{ route('rbacgroups.update', ['id' => $rbacGroup->id]) }}">
{!! csrf_field() !!}
<input type="hidden" name="_method" value="PUT">
    <div style="width: 40%">
        <div class="layui-form-item">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="title" value="{{ old('name') ?: $rbacGroup->name }}" autocomplete="off" placeholder="请输入组名" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">别名</label>
            <div class="layui-input-block">
                <input type="text" name="alias" lay-verify="" value="{{ old('alias') ?: $rbacGroup->alias }}" placeholder="请输入别名" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">权限清单</label>
                <div class="layui-input-block">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="col-md-1 text-center">权限清单名</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="layui-form-item" pane="">
                                @foreach($permissions as $permission)
                                <div class="layui-input-inline">
                                  <input type="checkbox" name="permissions[]" lay-skin="primary" title="{{ $permission->alias }}" value="{{ $permission->id }}" {{ in_array($permission->id, $rbacGroup->permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                </div>
                                @endforeach
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
layui.use('form', function(){
    var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
    var layer = layui.layer;

    var error = "{{ $errors->count() > 0 ? '组名以及别名不可为空！' : '' }}";
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