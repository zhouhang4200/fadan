@extends('frontend.v1.layouts.app')

@section('title', '账号 - 员工管理 - 员工添加')

@section('css')
    <style>
        .layui-form-item {
            width: 400px;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-header">员工添加</div>
    <div class="layui-card-body">
        <form class="layui-form" method="" action="">
            {!! csrf_field() !!}
            <div class="layui-form-item">
                <label class="layui-form-label">*员工姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="username" lay-verify="required|length" value="" autocomplete="off" placeholder="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">*账号</label>
                <div class="layui-input-block">
                    <input type="text" name="name" value="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">*密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" value="" lay-verify="required|password" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">代练</label>
                <div class="layui-input-inline">
                    <input type="radio" name="leveling_type" value="1" title="接单" >
                    <input type="radio" name="leveling_type" value="2" title="发单" checked >
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">岗位</label>
                <div class="layui-input-block">
                    @forelse($userRoles as $userRole)
                    <input type="checkbox" name="roles" value="{{ $userRole->id }}" lay-skin="primary" title="{{ $userRole->alias }}" >
                    @empty
                    @endforelse
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">QQ</label>
                <div class="layui-input-block">
                    <input type="text" name="qq" value="" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">微信</label>
                <div class="layui-input-block">
                    <input type="text" name="wechat" value="" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">电话</label>
                <div class="layui-input-block">
                    <input type="text" name="phone" value="" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <textarea placeholder="" name="remark" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="qs-btn layui-btn-normal" lay-submit="" lay-filter="store">确认</button>
                <button type="button" class="qs-btn layui-btn-normal cancel" >取消</button>
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
                length: [
                    /^\S{1,30}$/
                    ,'长度超出允许范围'
                  ]
              ,password: [
                /^[\S]{6,12}$/
                ,'密码必须6到12位，且不能出现空格'
              ] 
            });  

            // 取消按钮
            $('.cancel').click(function () {
                window.location.href="{{ route('staff-management.index') }}";
            });
            // 新增
            form.on('submit(store)', function (data) {
                var roles=[];
                $("input:checkbox[name='roles']:checked").each(function() { // 遍历name=test的多选框
                    $(this).val();  // 每一个被选中项的值
                    roles.push($(this).val());
                });

                $.post("{{ route('staff-management.store') }}", {
                    roles:roles,
                    data:data.field,
                    password:encrypt(data.field.password)
                }, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        window.location.href="{{ route('staff-management.index') }}";
                    }
                });
                return false;
            });
        });  
    </script>
@endsection