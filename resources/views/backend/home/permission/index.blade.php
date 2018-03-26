@extends('backend.layouts.main')

@section('title', ' | 用户权限列表')

@section('css')
    <style>
        .layui-table th, td{
            text-align:center;
        }
        .layui-form-item .layui-input-block{
            width:250px;
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
                            <li class="layui-this" lay-id="add">用户权限列表</li>
                        </ul>
                        <div class="layui-tab-content">
                            <form class="layui-form" action="">
                                <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="add">新增</button>
                            </form>
                            <div class="layui-tab-item layui-show" id="permission-list">
                                @include('backend.home.permission.list', ['modules' => $modules])
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-tab-content" style="display: none; padding:  0 20px" id="permission_create">
        <form class="layui-form" action="">
        {!! csrf_field() !!}
            <div class="layui-form-item">
                <div class="layui-block">
                    <label class="layui-form-label">选择模块</label>
                    <div class="layui-input-block">
                        <select name="new_module_id" lay-verify="required" lay-search="">
                            <option value="">请选择</option>
                            @forelse($modules as $module)
                            <option value="{{ $module->id }}">{{ $module->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">英文名</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">中文名</label>
                <div class="layui-input-block">
                    <input type="text" name="alias" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="confirm">确认</button>
                    <button  type="button" class="layui-btn layui-btn-normal cancel">取消</button>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-tab-content" style="display: none; padding:  0 20px" id="permission_edit">
        <form class="layui-form" action="">
        {!! csrf_field() !!}
            <div class="layui-form-item">
                <div class="layui-block">
                    <label class="layui-form-label">选择模块</label>
                    <div class="layui-input-block">
                        <select name="new_module_id" lay-verify="required" lay-search="">
                            <option value="">请选择</option>
                            @forelse($modules as $module)
                            <option value="{{ $module->id }}">{{ $module->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">英文名</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">中文名</label>
                <div class="layui-input-block">
                    <input type="text" name="alias" lay-verify="required" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="confirm">确认</button>
                    <button  type="button" class="layui-btn layui-btn-normal cancel">取消</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        layui.use('form', function(){
            var form = layui.form,layer=layui.layer,laydate=layui.laydate,table=layui.table;

            // 监听新增
            form.on('submit(add)', function () {
                layer.open({
                    type: 1,
                    shade: 0.6,
                    title: '新增',
                    area: ['450px', '300px'],
                    content: $('#permission_create')
                });
                form.on('submit(confirm)', function(data){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('home.permission.add') }}",
                        data:{data:data.field},
                        success: function (data) {
                            layer.msg(data.message);
                            $.get("{{ route('home.permission.index') }}", function (result) {
                                $('#permission-list').html(result);
                                form.render();
                            }, 'json');
                        }
                    });
                    layer.closeAll();
                    return false;
                });                
                return false;  
            })
             // 取消按钮
            $('.cancel').click(function () {
                layer.closeAll();
            });
            // 编辑
            form.on('submit(edit)', function () {
                var id=this.getAttribute('lay-id');
                var name=this.getAttribute('lay-name');
                var alias=this.getAttribute('lay-alias');
                var new_module_id=this.getAttribute('lay-module_id');

                $('#permission_edit input[name="name"]').val(name);
                $('#permission_edit input[name="alias"]').val(alias);
                $('#permission_edit select[name="new_module_id"]').val(new_module_id);
                form.render();
                layer.open({
                    type: 1,
                    shade: 0.6,
                    title: '编辑',
                    area: ['450px', '300px'],
                    content: $('#permission_edit')
                });

                form.on('submit(confirm)', function(data){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('home.permission.update') }}",
                        data:{id:id, data:data.field},
                        success: function (data) {
                            layer.msg(data.message);

                            $.get("{{ route('home.permission.index') }}", function (result) {
                                $('#permission-list').html(result);
                                form.render();
                            }, 'json');
                        }
                    });
                    layer.closeAll();
                    return false;
                });                
                return false;  
            })

            // 删除
            form.on('submit(destroy)', function (data) {
                var id=this.getAttribute('lay-id');
                $.post("{{ route('home.permission.destroy') }}", {id:id}, function (result) {
                    layer.msg(result.message);

                    $.get("{{ route('home.permission.index') }}", function (result) {
                        $('#permission-list').html(result);
                        form.render();
                    }, 'json');
                })
                return false;
            })
        });
    </script>
@endsection