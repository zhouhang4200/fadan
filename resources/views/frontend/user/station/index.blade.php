@extends('frontend.layouts.app')

@section('title', '账号 - 岗位列表')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-form-label {
            width:100px;
        }
        .layui-table th, .layui-table td {
            text-align:center;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <div style="padding-top:5px; padding-bottom:10px; float:right">
        <a href="{{ route('station.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal layui-btn-small">添加岗位</button></a>
    </div>
    <form class="layui-form" method="" action="" id="role">
        @include('frontend.user.station.list', ['userRoles' => $userRoles])
    </form>
    {!! $userRoles->render() !!}
@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;
            // 删除
            form.on('submit(delete)', function (data) {
                var roleId=this.getAttribute('lay-id');
                $.post("{{ route('station.destroy') }}", {roleId:roleId}, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        $.get("{{ route('station.index') }}", function (result) {
                            $('#role').html(result);
                            form.render();
                        }, 'json');
                    }
                    form.render;
                });
                return false;
            })
        });
    </script>
@endsection