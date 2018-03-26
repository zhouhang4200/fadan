@extends('frontend.layouts.app')

@section('title', '账号 - 员工管理 - 员工编辑')

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="POST" action="{{ route('staff-management.update', ['id' => $user->id]) }}">
        {!! csrf_field() !!}
        <input type="hidden" name="_method" value="PUT">
        <div class="layui-form-item">
            <label class="layui-form-label">员工姓名</label>
            <div class="layui-input-block">
                <input type="text" name="username" lay-verify="required|length" value="{{ old('username') ?: $user->username }}" autocomplete="off" placeholder="" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">账号</label>
            <div class="layui-input-block">
                <input type="text" name="name" value="{{ old('name') ?: $user->name }}" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
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
                <input type="checkbox" name="role[]" value="{{ $userRole->id }}" lay-skin="primary" title="{{ $userRole->alias }}" {{ $user->newRoles && in_array($userRole->id, $user->newRoles->pluck('id')->flatten()->toArray()) ? 'checked' : '' }} >
                @empty
                @endforelse
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">QQ</label>
            <div class="layui-input-block">
                <input type="text" name="qq" value="{{ old('qq') ?: $user->qq }}" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">微信</label>
            <div class="layui-input-block">
                <input type="text" name="wechat" value="{{ old('wechat') ?: $user->wechat }}" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">电话</label>
            <div class="layui-input-block">
                <input type="text" name="phone" value="{{ old('phone') ?: $user->phone }}" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea placeholder="" name="remark" class="layui-textarea">{{ old('remark') ?: $user->remark }}</textarea>
            </div>
        </div>
        <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">确认</button>
            <a class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1" href="{{ route('staff-management.index') }}">取消</a>
        </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
         layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;
            var error = "{{ $errors->count() > 0 ? '账号名或邮箱已经存在!' : '' }}";
            var fail = "{{ session('fail') ?: '' }}";

            if(fail) {
                layer.msg(fail, {icon: 5, time:1500});            } else if (error) {
                layer.msg(error, {icon: 5, time:1500});            }
  
            form.verify({
                length: [
                    /^\S{1,30}$/
                    ,'长度超出允许范围'
                  ]
              
              //我们既支持上述函数式的方式，也支持下述数组的形式
              //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
              ,pass: [
                /^[\S]{6,12}$/
                ,'密码必须6到12位，且不能出现空格'
              ] 
            });  
          
          //但是，如果你的HTML是动态生成的，自动渲染就会失效
          //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
          form.render();

        });  

    </script>
@endsection