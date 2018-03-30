@extends('backend.layouts.main')

@section('title', ' | 用户角色列表')

@section('css')
    <style>
       .layui-form-item .layui-input-inline {
            float: left;
            width: 210px;
            margin-right: 10px;
            text-align: left;
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
                            <li class="layui-this" lay-id="add">用户角色列表</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form">
                            {!! csrf_field() !!}
                                <input type="hidden" name="id" value="{{ $role->id }}">
                                <div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">英文角色名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" lay-verify="required" value="{{ $role->name }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">中文角色名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="alias" lay-verify="required" value="{{ $role->alias }}" autocomplete="off" placeholder="请输入" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">配置权限</label>
                                            <div class="layui-input-block">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="col-md-2 text-center">模块名</th>
                                                    <th class="col-md-10 text-center">权限名</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($modules as $module)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="new_module_id" lay-skin="primary" title="{{ $module->name }}" lay-filter="module" value="{{ $module->id }}">
                                                        </td>
                                                        <td>
                                                            <div class="layui-form-item" pane="">
                                                            @forelse($module->newPermissions as $permission)
                                                            <div class="layui-input-inline">
                                                                <input type="checkbox" {{ in_array($permission->id, $role->newPermissions->pluck('id')->toArray()) ? 'checked' : '' }} name="new_permission_ids" lay-skin="primary" title="{{ $permission->alias }}" value="{{ $permission->id }}">
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
                                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="update">确认</button>
                                            <button  type="button" class="layui-btn layui-btn-normal cancel">取消</button>
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
            var form = layui.form,layer=layui.layer,laydate=layui.laydate,table=layui.table;
             // 取消按钮
            $('.cancel').click(function () {
                window.location.href="{{ route('home.role.index') }}";
            });
            // 全选
            form.on('checkbox(module)', function (data) {
                var child=$(data.elem).parents('td').next('td').find('input[type="checkbox"]');
                child.each(function(index, item){  
                    item.checked = data.elem.checked;  
                });  
                form.render('checkbox');
            })
            // 发送保存数据
            form.on('submit(update)', function (data) {
                var ids=[];
                $("input:checkbox[name='new_permission_ids']:checked").each(function() { // 遍历name=test的多选框
                    $(this).val();  // 每一个被选中项的值
                    ids.push($(this).val());
                });
                $.post("{{ Route('home.role.update') }}", {ids:ids,data:data.field}, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        window.location.href="{{ route('home.role.index') }}";
                    }
                })
                return false;
            })
            // 取消
            // 取消按钮
            $('.cancel').click(function () {
                $.get("{{ route('home.role.index') }}", function (result) {
                    $('#role-list').html(result);
                    form.render();
                }, 'json');
            });
        });
    </script>
@endsection