@extends('backend.layouts.main')

@section('title', ' | 用户资料')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class=""><a href="{{ route('frontend.user.index') }}"><span>用户列表</span></a></li>
                <li class="active"><span>用户资料</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <div class="layui-tab layui-tab-brief" lay-filter="detail">
                            <ul class="layui-tab-title">
                                <li  class="layui-this"  lay-id="detail"><a href="{{ route('frontend.user.show', ['id' => Route::input('userId')])  }}">用户资料</a></li>
                                <li lay-id="authentication"><a href="{{ route('frontend.user.authentication', ['id' => Route::input('userId')])  }}">实名认证</a></li>
                                <li lay-id="authentication"><a href="{{ route('frontend.user.transfer-account-info', ['id' => Route::input('userId')])  }}">转账信息</a></li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show detail">
                                    <form class="layui-form" action="">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">千手ID</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="id" required disabled readonly  lay-verify="" autocomplete="off" class="layui-input" value="{{ $user->id }}">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">名字</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="name" disabled readonly  lay-verify="" autocomplete="off" class="layui-input" value="{{ $user->name }}">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">Email</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="email" disabled readonly  lay-verify="" autocomplete="off" class="layui-input" value="{{ $user->name }}">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">QQ</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="qq" disabled readonly  autocomplete="off" class="layui-input" value="{{ $user->qq }}">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">手机</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="phone" disabled readonly   lay-verify="" autocomplete="off" class="layui-input" value="{{ $user->phone }}">
                                            </div>
                                        </div>

                                        <div class="layui-form-item">
                                            <label class="layui-form-label">类型</label>
                                            <div class="layui-input-block">
                                                <input type="radio" name="type" value="1" title="发单" @if($user->type == 1) checked @endif>
                                                <input type="radio" name="type" value="2" title="接单" @if($user->type == 2) checked @endif>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">别名</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="nickname"   placeholder="可写原千手ID用户识别"  autocomplete="off" class="layui-input" value="{{ $user->nickname }}">
                                            </div>
                                        </div>
                                        <div class="layui-form-item layui-form-text">
                                            <label class="layui-form-label">备注</label>
                                            <div class="layui-input-block">
                                                <textarea name="remark" placeholder="请输入内容" class="layui-textarea">{{ $user->remark }}</textarea>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <div class="layui-input-block">
                                                <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit lay-filter="save">立即提交</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                                <div class="layui-tab-item authentication"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    //Demo
    layui.use('form', function(){
        var form = layui.form;

        //监听提交
        form.on('submit(save)', function(data){
            $.post("{{ route('frontend.user.edit') }}", {
                id:data.field.id,
                type:data.field.type,
                nickname:data.field.nickname,
                remark:data.field.remark}, function (result) {
                 layer.msg(result.message);
            }, 'json');
            return false;
        });
    });
</script>
@endsection