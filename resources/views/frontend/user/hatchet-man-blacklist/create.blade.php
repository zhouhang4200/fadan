@extends('frontend.layouts.app')

@section('title', '账号 - 打手黑名单添加')

@section('css')
    <style>
        .layui-form-item {
            width: 400px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
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
                <input type="text" name="hatchet_man_phone" value="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">*打手QQ</label>
            <div class="layui-input-block">
                <input type="text" name="hatchet_man_qq" value="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">*打手平台</label>
            <div class="layui-input-block">
                <select name="third" lay-verify="" lay-search="">
                    <option value="">输入或选择</option>
                    @forelse($thirds as $third => $name)
                        <option value="{{ $third }}" >{{ $name }}</option>
                    @empty
                    @endforelse
                </select>
                
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
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="store">确认</button>
            <button type="button" class="layui-btn layui-btn-normal cancel" >取消</button>
        </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
         layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

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