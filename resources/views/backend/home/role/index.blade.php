@extends('backend.layouts.main')

@section('title', ' | 用户角色列表')

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
                            <li class="layui-this" lay-id="add">用户角色列表</li>
                        </ul>
                        <div class="layui-tab-content">
                                <a class="layui-btn layui-btn-normal layui-btn-small"  href="{{ route('home.role.create') }}">新增</a>
                            <div class="layui-tab-item layui-show" id="role-list">
                                @include('backend.home.role.list', ['roles' => $roles])
                            </div>
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
                layer.closeAll();
            });
            // 删除
            form.on('submit(destroy)', function (data) {
                var id=this.getAttribute('lay-id');
                $.post("{{ route('home.role.destroy') }}", {id:id}, function (result) {
                    layer.msg(result.message);

                    $.get("{{ route('home.role.index') }}", function (result) {
                        $('#role-list').html(result);
                        form.render();
                    }, 'json');
                })
                return false;
            })
        });
    </script>
@endsection