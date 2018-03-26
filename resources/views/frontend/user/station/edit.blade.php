@extends('frontend.layouts.app')

@section('title', '账号 - 岗位编辑')

@section('css')
    <link rel="stylesheet" type="text/css" href="/backend/css/bootstrap/bootstrap.min.css"/>
    <style>
        .layui-form input {
            width:800px;
        }
        .table {
            width:800px;
        }
        .layui-form-label {
            width:100px;
        }
        .layui-form-item .layui-input-inline {
            float: left;
            width: 150px;
            margin-right: 10px;
        }
        .layui-form-checked[lay-skin="primary"] span {     
            background-color: #fff;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
<form class="layui-form" method="" action="">
    {!! csrf_field() !!}
    <div style="width: 100%">
        <input type="hidden" name="id" value="{{ $userRole->id }}">
        <div class="layui-form-item">
            <label class="layui-form-label">岗位名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="title" value="{{ old('alias') ?: $userRole->alias }}" autocomplete="off" placeholder="请输入" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">拥有权限</label>
                <div class="layui-input-block">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="col-md-1 text-center">模块</th>
                        <th class="col-md-1 text-center" style="width: 668px;">权限</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse($modulePermissions as $modulePermission)
                        <tr>
                            <td>{{ $modulePermission->name }}</td>
                            <td>
                                <div class="layui-form-item" pane="">
                                @forelse($modulePermission->newPermissions as $permission)
                                <div class="layui-input-inline" style="width:240px">
                                  <input type="checkbox" name="permissions" lay-skin="primary" title="{{ $permission->alias }}" value="{{ $permission->id }}" {{ in_array($permission->id, $userRole->newPermissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                </div>
                                @empty
                                @endforelse
                                </div>
                            </td>
                        </tr>
                            @empty
                            @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="update">立即提交</button>
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

    form.on('submit(update)', function (data) {
        var permissionIds=[];
        $("input:checkbox[name='permissions']:checked").each(function() { // 遍历name=test的多选框
            $(this).val();  // 每一个被选中项的值
            permissionIds.push($(this).val());
        });
        $.post("{{ route('station.update') }}", {permissionIds:permissionIds, data:data.field}, function (result) {
            layer.msg(result.message);
            if (result.status > 0) {
                window.location.href="{{ route('station.index') }}";
            }
        })
        return false;
    });

});
</script>
@endsection