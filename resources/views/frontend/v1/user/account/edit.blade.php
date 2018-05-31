@extends('frontend.v1.layouts.app')

@section('title', '账号 - 修改密码')
@section('css')
    <style>
        .layui-anim .layui-icon {
            width: 28px;height: 25px;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-header">修改密码</div>
    <div class="layui-card-body" style="padding: 15px 25px 15px 15px">
        <form class="layui-form" action="" lay-filter="component-form-group" id="form-order">
            <div class="layui-row layui-col-space10 layui-form-item">
                <div class="layui-col-lg6">
                    <label class="layui-form-label"><span class="font-color-orange"></span> 账号</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" value="{{ old('name') ?: $user->name }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" display-name="账号">
                    </div>
                </div>
            </div>
            <div class="layui-row layui-col-space10 layui-form-item">
                <div class="layui-col-lg6">
                    <label class="layui-form-label"><span class="font-color-orange">*</span> 密码</label>
                    <div class="layui-input-block">
                        <input type="text" name="password" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" display-name="密码">
                    </div>
                </div>
            </div>
            @if(Auth::user()->could('frontend.workbench.recharge.index'))
            <div class="layui-row layui-col-space10 layui-form-item">
                <label class="layui-form-label"><span class="font-color-orange">*</span> 代充</label>
                <div class="layui-input-block">
                    <input type="radio" name="type" value="1" title="接单" @if($user->type == 1) checked="" @endif>
                    <input type="radio" name="type" value="2" title="发单" @if($user->type == 2) checked="" @endif>
                    <div style="float: right;" class="layui-form-mid layui-word-aux">设置为接单：则工作台显您接的单，发单：则工作显示您发出的单</div>
                </div>
            </div>
            @endif
            <div class="layui-row layui-col-space10 layui-form-item">
                <label class="layui-form-label" style="width: 84px;height: 31px;padding-top: 15px;"><span class="font-color-orange">*</span> 代练</label>
                <div class="layui-input-block">
                    <input type="radio" name="leveling_type" value="1" title="接单" @if($user->leveling_type == 1) checked="" @endif>
                    <input type="radio" name="leveling_type" value="2" title="发单" @if($user->leveling_type == 2) checked="" @endif>
                    <div style="position: relative;float:right;width: 1009px;height: 26px;padding-bottom: 7px;margin-top: 4px" class="layui-form-mid layui-word-aux">设置为接单：则工作台显您接的单，发单：则工作显示您发出的单</div>
                    </div>
            </div>
            <div class="layui-form-item layui-layout-admin">
                <div class="layui-input-block">
                        <button class="qs-btn" style="width: 92px;" lay-submit="" lay-filter="update">确定</button>
                </div>
            </div>
        </form>
    </div>
</div>



       <!--  <form class="layui-tab layui-tab-brief layui-form" method="" action="">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">
                <div class="layui-form-item">
                    <label class="layui-form-label">账号(不可更改)</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" lay-verify="required" value="{{ old('name') ?: $user->name }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="password" value="" lay-verify="" placeholder="最少6位,不填写则为原密码" autocomplete="off" class="layui-input">
                    </div>
                </div>
                @if(Auth::user()->could('frontend.workbench.recharge.index'))
                    <div class="layui-form-item">
                        <label class="layui-form-label">代充</label>
                        <div class="layui-input-inline">
                            <input type="radio" name="type" value="1" title="接单" @if($user->type == 1) checked="" @endif>
                            <input type="radio" name="type" value="2" title="发单" @if($user->type == 2) checked="" @endif>
                        </div>
                        <div class="layui-form-mid layui-word-aux">设置为接单：则工作台显您接的单，发单：则工作显示您发出的单</div>
                    </div>
                @endif
                <div class="layui-form-item">
                    <label class="layui-form-label">代练</label>
                    <div class="layui-input-inline">
                        <input type="radio" name="leveling_type" value="1" title="接单" @if($user->leveling_type == 1) checked="" @endif>
                        <input type="radio" name="leveling_type" value="2" title="发单" @if($user->leveling_type == 2) checked="" @endif>
                    </div>
                    <div class="layui-form-mid layui-word-aux">设置为接单：则工作台显您接的单，发单：则工作显示您发出的单</div>
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="qs-btn layui-btn-normal" lay-submit="" lay-filter="update">提交</button>
                </div>
            </div>
        </form>
    </div>
</div> -->
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
            
            form.on('submit(update)', function (data) {
                $.post("{{ route('home-accounts.update') }}", {password:encrypt(data.field.password), data:data.field}, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        window.location.href="{{ route('home-accounts.index') }}";
                    }
                });
                return false;
            })
        });  
    </script>
@endsection