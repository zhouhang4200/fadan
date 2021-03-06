@extends('frontend.v1.layouts.app')

@section('title', '账号 - 打手黑名单添加')

@section('css')
    <style>
        .layui-form-item {
            width: 400px;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-header">打手黑名单添加</div>
    <div class="layui-card-body">
        <form class="layui-form" method="" action="">
            {!! csrf_field() !!}
            <div class="layui-form-item">
                <label class="layui-form-label">*打手昵称</label>
                <div class="layui-input-block">
                    <input type="text" name="hatchet_man_name" lay-verify="required" value="" autocomplete="off" placeholder="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">*打手电话</label>
                <div class="layui-input-block">
                    <input type="text" name="hatchet_man_phone" value="" lay-verify="required|number" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">*打手QQ</label>
                <div class="layui-input-block">
                    <input type="text" name="hatchet_man_qq" value="" lay-verify="required|number" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <textarea placeholder="" name="content" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="qs-btn qs-btn-normal" lay-submit="" lay-filter="store">确认</button>
                <a type="button" class="qs-btn qs-btn-primary cancel" >取消</a>
            </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
    <script>
         layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            form.verify({
                number: [
                    /^[0-9]+$/
                    ,'填写格式不正确，必须为数字'
                  ]
            });  
            // 取消按钮
            $('.cancel').click(function () {
                window.location.href="{{ route('hatchet-man-blacklist.index') }}";
            });
            // 新增
            form.on('submit(store)', function (data) {
                $.post("{{ route('hatchet-man-blacklist.store') }}", {
                    data:data.field
                }, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        window.location.href="{{ route('hatchet-man-blacklist.index') }}";
                    }
                });
                return false;
            });
        });  
    </script>
@endsection