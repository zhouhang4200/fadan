@extends('frontend.layouts.app')

@section('title', '账号 - 修改子账号分组')

@section('css')
    <link rel="stylesheet" type="text/css" href="/backend/css/bootstrap/bootstrap.min.css"/>
    <style>
        .layui-form input {
            width:800px;
        }
        .table {
            width:800px;
        }
        .layui-form-item .layui-input-inline {
            float: left;
            width: 135px;
            margin-right: 10px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="POST" action="{{ route('user-groups.update', ['id' => $user->id]) }}">
        {!! csrf_field() !!}
        <input type="hidden" name="_method" value="PUT">
        <div style="width: 40%">
            <div class="layui-form-item">
                <label class="layui-form-label">名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required" value="{{ $user->name }}" autocomplete="off" placeholder="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">邮箱</label>
                <div class="layui-input-block">
                    <input type="text" name="email" lay-verify="required" value="{{ $user->email }}" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">权限组</label>
                    <div class="layui-input-block">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="col-md-1 text-center">权限组名</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="layui-form-item" pane="">
                                    @foreach($groups as $group)
                                    <div class="layui-input-inline">
                                      <input type="checkbox" name="groups[]" lay-skin="primary" title="{{ $group->alias }}" value="{{ $group->id }}" {{ $user->rbacGroups && in_array($group->id, $user->rbacGroups->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    </div>
                                    @endforeach
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">立即提交</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        layui.use(['form'], function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;

            var error = "{{ $errors->count() > 0 ? '组名以及别名不可为空！' : '' }}";
            var missError = "{{ session('missError') ?: '' }}";
            var createError = "{{ session('createError') ?: '' }}";
            var masterError = "{{ session('masterError') ?: '' }}";
            var updateError = "{{ session('updateError') ?: '' }}";

            if (error) {
                layer.msg(error, {icon: 5, time:1500},);
            } else if(missError) {
                layer.msg(missError, {icon: 5, time:1500},);
            } else if(createError) {
                layer.msg(createError, {icon: 5, time:1500},);
            } else if(masterError) {
                layer.msg(masterError, {icon: 5, time:1500},);
            } else if(updateError) {
                layer.msg(updateError, {icon: 5, time:1500},);
            }

            //……
            //但是，如果你的HTML是动态生成的，自动渲染就会失效
            //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
            form.render();
        });
    </script>
@endsection