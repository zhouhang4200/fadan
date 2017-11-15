@extends('backend.layouts.main')

@section('title', ' | 实名认证详情')

@section('css')
    <style>
        .ident > .company > .layui-form-item > .layui-form-label {
            width: 110px;
        }

        .ident > .company > .layui-form-item > .layui-input-block {
            margin-left: 170px;
        }

        .ident > .company > .layui-form-item > .layui-upload > .layui-btn {
            margin-left: 30px;
        }

        .ident > .company > .layui-form-item > .layui-upload > .layui-upload-list {
            width: 500px;
            height: 300px;
            margin-left: 170px;
        }

        .ident > .company > .layui-form-item > .layui-upload > .layui-upload-list > img {
            width: 100%;
            height: 100%;
        }

        /*分割线*/
        .ident > .personal > .layui-form-item > .layui-form-label {
            width: 110px;
        }
}

        .ident > .personal > .layui-form-item > .layui-upload > .layui-btn {
            margin-left: 30px;
        }

        .ident > .personal > .layui-form-item > .layui-upload > .layui-upload-list {
            width: 500px;
            height: 300px;
            margin-left: 170px;
            background-size: cover !important;
            background-position: center !important;
        }

        .ident > .personal > .layui-form-item > .layui-upload > .layui-upload-list > img {
            width: 100%;
            height: 100%;
        }

        .none {
            display: none;
        }
        .layui-anim {
            color: #1E9FFF !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class=""><a href="{{ route('admin-idents.index') }}"><span>实名认证列表</span></a></li>
                <li class="active"><span>实名认证详情</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
    <div class="col-lg-12">
        <div class="main-box">
            <header class="main-box-header clearfix">
                <h2>认证相关图片</h2>
            </header>
            <div class="main-box-body clearfix">
                <input id="userId" type="hidden" name="userId" value="{{ $ident->user_id}}">
                @if ($ident->type == 2)
                    <div class="row">
                        <div class="col-xs-12"  style="margin-bottom: 20px;">
                            <div id="gallery-photos-lightbox">
                                <ul class="clearfix gallery-photos">
                                    <li class="col-md-4">
                                        <a href="{{ $ident->license_picture or '' }}" class="photo-box image-link"
                                           style="background-image: url('{{ $ident->license_picture or '' }}');"></a>
                                        <span class="thumb-meta-time"><i class="fa fa-clock-o"></i> 营业执照正面照</span>
                                    </li>
                                    <li class="col-md-4">
                                        <a href="{{ $ident->bank_open_account_picture or '' }}" class="photo-box image-link"
                                           style="background-image: url('{{ $ident->bank_open_account_picture or '' }}');"></a>
                                        <span class="thumb-meta-time"><i class="fa fa-clock-o"></i> 银行开户许可证照片</span>
                                    </li>
                                    <li class="col-md-4">
                                        <a href="{{ $ident->agency_agreement_picture or '' }}" class="photo-box image-link"
                                           style="background-image: url('{{ $ident->agency_agreement_pictureor  or '' }}');"></a>
                                        <span class="thumb-meta-time"><i class="fa fa-clock-o"></i> 代办协议照片</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class='other'>
                            <form class="layui-form">
                                <div  class="ident">
                                    <div class='company'>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">真实姓名</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="name" lay-verify="required"
                                                       value="{{ old('name') ?: $ident->name }}"
                                                       autocomplete="off" placeholder="请输入个人真实姓名"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">手机号</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="phone_number"
                                                       lay-verify="required|phone|number"
                                                       value="{{ old('phone_number') ?: $ident->phone_number }}"
                                                       placeholder="请输入手机号" autocomplete="off"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">开户银行卡号</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="bank_number" lay-verify="required"
                                                       value="{{ old('bank_number') ?: $ident->bank_number }}"
                                                       autocomplete="off" placeholder="请输入开户银行卡号"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">开户银行名称</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="bank_name" lay-verify="required"
                                                       value="{{ old('bank_name') ?: $ident->bank_name }}"
                                                       autocomplete="off" placeholder="请输入详细银行名称如XX行XX支行"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">营业执照名称</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="license_name" lay-verify="required"
                                                       value="{{ old('license_name') ?: $ident->license_name }}"
                                                       autocomplete="off" placeholder="请输入执照名称"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">营业执照号码</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="license_number" lay-verify="required"
                                                       value="{{ old('license_number') ?: $ident->license_number }}"
                                                       placeholder="请输入营业执照号码" autocomplete="off"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">法人姓名</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="corporation" lay-verify="required"
                                                       value="{{ old('corporation') ?: $ident->corporation }}"
                                                       placeholder="请输入法人姓名" autocomplete="off"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">拒绝原因</label>
                                            <div class="layui-input-block">
                                                <input type="text" id="ident-message" name="message"
                                                       lay-verify="" value="{{ $ident->message }}"
                                                       autocomplete="off" placeholder="如果不通过，请输入拒绝原因"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" id="pass"
                                                    lay-filter="pass">通过
                                            </button>
                                            <button class="layui-btn layui-btn-normal" lay-submit="" id="refuse"
                                                    lay-filter="refuse">拒绝
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif ($ident->type == 1)
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 20px;">
                            <div id="gallery-photos-lightbox">
                            <ul class="clearfix gallery-photos">
                                <li class="col-md-4">
                                    <a href="{{ $ident->front_card_picture or '' }}" class="photo-box image-link"
                                       style="background-image: url('{{ $ident->front_card_picture or '' }}');"></a>
                                    <span class="thumb-meta-time"><i class="fa fa-clock-o"></i> 身份证正面照</span>
                                </li>
                                <li class="col-md-4">
                                    <a href="{{ $ident->back_card_picture or '' }}" class="photo-box image-link"
                                       style="background-image: url('{{ $ident->back_card_picture or '' }}');"></a>
                                    <span class="thumb-meta-time"><i class="fa fa-clock-o"></i> 身份证背面照</span>
                                </li>
                                <li class="col-md-4">
                                    <a href="{{ $ident->hold_card_picture or '' }}" class="photo-box image-link"
                                       style="background-image: url('{{ $ident->hold_card_picture or '' }}');"></a>
                                    <span class="thumb-meta-time"><i class="fa fa-clock-o"></i> 手持身份证的本人正面照</span>
                                </li>
                            </ul>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class='self'>
                            <form class="layui-form">
                                <div  class="ident">
                                    <div class='personal'>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">真实姓名</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="name" lay-verify="required"
                                                       value="{{ old('name') ?: $ident->name }}"
                                                       autocomplete="off" placeholder="请输入个人真实姓名"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">手机号</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="phone_number"
                                                       lay-verify="required|phone|number"
                                                       value="{{ old('phone_number') ?: $ident->phone_number }}"
                                                       placeholder="请输入手机号" autocomplete="off"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">开户银行卡号</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="bank_number" lay-verify="required"
                                                       value="{{ old('bank_number') ?: $ident->bank_number }}"
                                                       autocomplete="off" placeholder="请输入开户银行卡号"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">开户银行名称</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="bank_name" lay-verify="required"
                                                       value="{{ old('bank_name') ?: $ident->bank_name }}"
                                                       autocomplete="off" placeholder="请输入详细银行名称如XX行XX支行"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">身份证号码</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="identity_card"
                                                       lay-verify="required|identity"
                                                       value="{{ old('identity_card') ?: $ident->identity_card }}"
                                                       placeholder="请输入身份证号码" autocomplete="off"
                                                       class="layui-input">
                                            </div>
                                        </div>

                                        <div class="layui-form-item">
                                            <label class="layui-form-label">拒绝原因</label>
                                            <div class="layui-input-block">
                                                <input type="text" id="ident-message" name="message"
                                                       lay-verify="" value="{{ $ident->message }}"
                                                       autocomplete="off" placeholder="如果不通过，请输入拒绝原因"
                                                       class="layui-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn layui-btn-normal" lay-submit="" id="pass"
                                                lay-filter="pass">通过
                                        </button>
                                        <button class="layui-btn layui-btn-normal" lay-submit="" id="refuse"
                                                lay-filter="refuse">拒绝
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="/backend/js/jquery.magnific-popup.min.js"></script>
<script>
    $(function() {
        $(document).ready(function() {
            $('#gallery-photos-lightbox').magnificPopup({
                type: 'image',
                delegate: 'a',
                gallery: {
                    enabled: true
                }
            });
        });
    });
    layui.use('form', function () {
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;
        var userId = $('#userId').val();

        form.on('submit(pass)', function (data) {
            $.ajax({
                url: "{{ route('pass-or-refuse.pass') }}",
                method: "POST",
                data: {'userId': userId},
                success: function (data) {
                    if (data.code == 1) {
                        layer.msg('审核完成，状态：通过！', {icon: 6, time: 1500});
                        window.location.href = "{{ route('admin-idents.index') }}";
                    } else {
                        layer.msg(data.message, {icon: 5, time: 1500});
                    }
                }
            });
            return false;
        });

        form.on('submit(refuse)', function (data) {
            var message = data.field.message;

            $.ajax({
                method: 'POST',
                url: "{{ route('pass-or-refuse.refuse') }}",
                data: {'userId': userId, 'message': message},
                success: function (data) {
                    if (data.code == 1) {
                        layer.msg('审核完成， 状态：不通过！', {icon: 6, time: 1500});
                        window.location.href = "{{ route('admin-idents.index') }}";
                    } else {
                        layer.msg(data.message, {icon: 5, time: 1500});
                    }

                }
            });

            return false;
        });

        form.render();
    });
</script>
@endsection