@extends('backend.layouts.main')

@section('title', ' | 转账信息')

@section('css')
    <style>
        .layui-form-label {
            padding: 7px 9px !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class=""><a href="{{ route('frontend.user.index') }}"><span>用户列表</span></a></li>
                <li class="active"><span>转账信息</span></li>
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
                                <li lay-id="detail"><a href="{{ route('frontend.user.show', ['id' => Route::input('userId')])  }}">用户资料</a></li>
                                <li lay-id="authentication"><a href="{{ route('frontend.user.authentication', ['id' => Route::input('userId')])  }}">实名认证</a></li>
                                <li  class="layui-this"   lay-id="authentication"><a href="{{ route('frontend.user.authentication', ['id' => Route::input('userId')])  }}">转账信息</a></li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show detail">
                                    <form class="layui-form" action="">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">千手ID</label>
                                            <div class="layui-input-block layui-disabled">
                                                <input type="text" name="id" required disabled readonly  lay-verify="" autocomplete="off" class="layui-input" value="{{ Route::input('userId') }}">
                                            </div>
                                        </div>

                                        <div class="layui-form-item">
                                            <label class="layui-form-label">转账账号</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="bank_card"    autocomplete="off" class="layui-input" value="{{ $transferInfo->bank_card or '' }}">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">转账户名</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="name"    lay-verify="" autocomplete="off" class="layui-input" value="{{ $transferInfo->name or '' }}">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">转账开户行</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="bank_name"     lay-verify="" autocomplete="off" class="layui-input" value="{{ $transferInfo->bank_name or ''}}">
                                            </div>
                                        </div>

                                        <div class="layui-form-item">
                                            <div class="layui-input-block">
                                                <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit lay-filter="save">保存</button>
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
            $.post("{{ route('frontend.user.transfer-account-info-update') }}", {
                id:data.field.id,
                name:data.field.name,
                bank_name:data.field.bank_name,
                bank_card:data.field.bank_card
            }, function (result) {
                 layer.msg(result.message);
            }, 'json');
            return false;
        });
    });
</script>
@endsection