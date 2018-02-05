@extends('frontend.layouts.app')

@section('title', '设置 - 短信管理')

@section('css')
    <style>
        fieldset {
            border: none;
            padding: 0;
            border-top: 1px solid #eee;
        }
        .layui-form-switch {
            margin-top: 0;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.setting.submenu')
@endsection

@section('main')

    <fieldset>
        <legend><a name="hr">自动发送</a></legend>
    </fieldset>

    <div id="auto-list">
        @include('frontend.setting.sms.auto-list')
    </div>

    <fieldset>
        <legend><a name="hr">手动发送</a></legend>
    </fieldset>

    <button class="layui-btn layui-bg-blue layui-btn-mini" id="template-add" style="margin-top: 15px">添加</button>

    <div id="manual-list">
        @include('frontend.setting.sms.manual-list')
    </div>

    <div id="template-add-popup" style="display: none;padding: 20px">
        <form class="layui-form" action="" id="template-add-form">

            <div class="layui-form-item">
                <input type="text" name="name" required lay-verify="required" placeholder="模板名称"
                       autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-item layui-form-text">
                <textarea name="contents" placeholder="短信内容" class="layui-textarea"></textarea>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="template-add-save">确定添加
                </button>
                <button type="button" class="layui-btn layui-btn-danger cancel">取消添加</button>
            </div>
        </form>
    </div>

@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'element'], function () {
            var form = layui.form, layer = layui.layer, element = layui.element, type;

            // 点击页码翻页
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                loadData($(this).attr('href'));
                return false;
            });
            // 关闭弹层
            $(document).on('click', '.cancel', function () {
                layer.closeAll();
            });

            // 添加
            form.on('submit(template-add-save)', function (data) {
                $.post('{{ route('frontend.sms.add') }}', {
                    name: data.field.name,
                    contents: data.field.contents
                }, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        loadData('{{ route('frontend.sms.index') }}?type=' + type);
                    }
                }, 'json');
                return false;
            });

            // 删除
            form.on('submit(template-delete)', function (data) {
                layer.confirm('您确定要删除吗?', {icon: 3, title: '提示'}, function () {
                    $.post('{{ route('frontend.sms.delete') }}', {id: data.elem.getAttribute('data-id')}, function (result) {
                        layer.msg(result.message);
                        if (result.status == 1) {
                            loadData('{{ route('frontend.sms.index') }}?type=' + type);
                        }
                    }, 'json');
                });
                return false;
            });

            // 保存修改
            form.on('submit(template-edit-save)', function (data) {
                type = data.field.type;
                $.post('{{ route('frontend.sms.edit') }}', {
                    id:data.field.id,
                    name: data.field.name,
                    contents: data.field.contents
                }, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        loadData('{{ route('frontend.sms.index') }}?type=' + type);
                    }
                }, 'json');
                return false;
            });

            // 修改
            $('.content').on('click', '.template-edit', function () {
                var id  = $(this).attr('data-id');
                $.post('{{ route("frontend.sms.show") }}', {id:id}, function (result) {
                    if (result) {
                        layer.open({
                            type: 1,
                            shade: 0.2,
                            title: '编辑模板',
                            area: ['500px'],
                            content: result
                        });
                    }

                }, 'json');
                return false;
            });

            // 按用户ID添加
            $('#template-add').click(function () {
                type = 2;
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加模板',
                    area: ['500px'],
                    content: $('#template-add-popup')
                });
                return false;
            });

            // 加载数据
            function loadData(url) {
                $.get(url, function (result) {
                    if (type == 1) {
                        $('#auto-list').html(result);
                    } else {
                        $('#manual-list').html(result);
                    }
                    layui.form.render();
                }, 'json');
            }
        });
    </script>
@endsection