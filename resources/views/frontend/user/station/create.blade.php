@extends('frontend.layouts.app')

@section('title', '账号 - 岗位添加')

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
            <div class="layui-form-item">
                <label class="layui-form-label">岗位名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required" value="{{ old('name') }}" autocomplete="off" placeholder="请输入" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">拥有权限</label>
                    <div class="layui-input-block">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="col-md-1 text-center" style="width: 1%">模块</th>
                            <th class="col-md-1 text-center">权限</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($modulePermissions as $modulePermission)
                            <tr>
                                <td><input type="checkbox" name="new_module_id" lay-skin="primary" title="{{ $modulePermission->name }}" lay-filter="module" value="{{ $modulePermission->id }}"></td>
                                <td>
                                    @foreach($modulePermission->newPermissions as $permission)
                                    <div class="layui-input-inline" style="width:240px">
                                      <input type="checkbox" name="permissions" lay-skin="primary" title="{{ $permission->alias }}" value="{{ $permission->id }}">
                                    </div>
                                    @endforeach
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
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="confirm">立即提交</button>
                </div>
            </div>
        </div>
    </form>
@endsection
<!--START 底部-->
@section('js')
    <script>
    layui.use(['form', 'table'], function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;
        // 全选
        form.on('checkbox(module)', function (data) {
            var child=$(data.elem).parents('td').next('td').find('input[type="checkbox"]');
            child.each(function(index, item){  
                item.checked = data.elem.checked;  
            });  
            form.render('checkbox');
        })
        form.on('submit(confirm)', function (data) {
            var ids=[];
            $("input:checkbox[name='permissions']:checked").each(function() { // 遍历name=test的多选框
                $(this).val();  // 每一个被选中项的值
                ids.push($(this).val());
            });
            $.post("{{ route('station.store') }}", {ids:ids,data:data.field}, function (result) {
                layer.msg(result.message);
                if (result.status > 0) {
                    window.location.href="{{ route('station.index') }}";
                }
            })
            return false;
        })
    });
    </script>
@endsection