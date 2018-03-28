@extends('frontend.layouts.app')

@section('title', '账号 - 员工管理 - 员工编辑')

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        {!! csrf_field() !!}
        <input type="hidden" name="_method" value="PUT">
        <div class="layui-form-item">
            <label class="layui-form-label">员工姓名</label>
            <div class="layui-input-block">
                <input type="text" name="username" lay-verify="required|length" value="{{ $user->username }}" autocomplete="off" placeholder="" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-block">
                <input type="password" name="password" value="" lay-verify="" placeholder="不填写则为原密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">代充</label>
            <div class="layui-input-block">
                <input type="radio" name="type" value="1" title="接单" {{ $user->type == 1 ? 'checked' : '' }}>
                <input type="radio" name="type" value="2" title="发单" {{ $user->type == 2 ? 'checked' : '' }}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">代练</label>
            <div class="layui-input-block">
                <input type="radio" name="leveling_type" value="1" title="接单" {{ $user->leveling_type == 1 ? 'checked' : '' }}>
                <input type="radio" name="leveling_type" value="2" title="发单" {{ $user->leveling_type == 2 ? 'checked' : '' }}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">岗位</label>
            <div class="layui-input-block">
                @forelse($userRoles as $userRole)
                <input type="checkbox" name="roles" value="{{ $userRole->id }}" lay-skin="primary" title="{{ $userRole->alias }}" {{ $user->newRoles && in_array($userRole->id, $user->newRoles->pluck('id')->flatten()->toArray()) ? 'checked' : '' }} >
                @empty
                @endforelse
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">QQ</label>
            <div class="layui-input-block">
                <input type="text" name="qq" value="{{ $user->qq }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">微信</label>
            <div class="layui-input-block">
                <input type="text" name="wechat" value="{{ $user->wechat }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">电话</label>
            <div class="layui-input-block">
                <input type="text" name="phone" value="{{ $user->phone }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea placeholder="" name="remark" class="layui-textarea">{{ $user->remark }}</textarea>
            </div>
        </div>
        <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-id="{{ $user->id }}" lay-filter="update">确认</button>
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
  
            form.verify({
                length: [
                    /^\S{1,30}$/
                    ,'长度超出允许范围'
                  ]
              ,pass: [
                /^[\S]{6,12}$/
                ,'密码必须6到12位，且不能出现空格'
              ] 
            });  
            // 取消按钮
            $('.cancel').click(function () {
                window.location.href="{{ route('staff-management.index') }}";
            });
            // 编辑
            form.on('submit(update)', function (data) {
                var roles=[];
                var id=this.getAttribute('lay-id');
                $("input:checkbox[name='roles']:checked").each(function() { // 遍历name=test的多选框
                    $(this).val();  // 每一个被选中项的值
                    roles.push($(this).val());
                });

                $.post("{{ route('staff-management.update') }}", {id:id,roles:roles,data:data.field}, function (result) {
                    layer.msg(result.message);
                    window.location.href="{{ route('staff-management.index') }}";
                });
                return false;
            });
        });  

    </script>
@endsection