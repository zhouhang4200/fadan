@extends('frontend.layouts.app')

@section('title', '账号 - 员工管理 - 员工添加')

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
    <form class="layui-form" method="POST" action="{{ route('staff-management.store') }}">
        {!! csrf_field() !!}
        <div class="layui-form-item">
            <label class="layui-form-label">*员工姓名</label>
            <div class="layui-input-block">
                <input type="text" name="username" lay-verify="required|length" value="{{ old('username') ?: '' }}" autocomplete="off" placeholder="" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">*账号</label>
            <div class="layui-input-block">
                <input type="text" name="name" value="{{ old('name') ?: '' }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">*密码</label>
            <div class="layui-input-block">
                <input type="password" name="password" value="" lay-verify="required|password" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">代充</label>
            <div class="layui-input-block">
                <input type="radio" name="type" value="1" title="接单" checked>
                <input type="radio" name="type" value="2" title="发单">
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
                @forelse($roles as $role)
                <input type="checkbox" name="role[]" value="{{ $role->id }}" lay-skin="primary" title="{{ $role->name }}" >
                @empty
                @endforelse
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">QQ</label>
            <div class="layui-input-block">
                <input type="text" name="qq" value="{{ old('qq') ?: '' }}" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">微信</label>
            <div class="layui-input-block">
                <input type="text" name="wechat" value="{{ old('wechat') ?: '' }}" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">电话</label>
            <div class="layui-input-block">
                <input type="text" name="phone" value="{{ old('phone') ?: '' }}" lay-verify="" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea placeholder="" name="remark" class="layui-textarea">{{ old('remark') ?:'' }}</textarea>
            </div>
        </div>
        <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="add">确认</button>
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
            var error = "{{ $errors->count() > 0 ? '账号已经存在!' : '' }}";
            var fail = "{{ session('fail') ?: '' }}";

            if (error) {
                layer.msg(error, {icon: 5, time:1500});
            } else if (fail) {
                layer.msg(fail, {icon: 5, time:1500});
            }
  
            form.verify({
                length: [
                    /^\S{1,30}$/
                    ,'长度超出允许范围'
                  ]
              
              //我们既支持上述函数式的方式，也支持下述数组的形式
              //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
              ,password: [
                /^[\S]{6,12}$/
                ,'密码必须6到12位，且不能出现空格'
              ] 
            });  
          
          //但是，如果你的HTML是动态生成的，自动渲染就会失效
          //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
          
          // form.render();
        });  

    </script>
@endsection