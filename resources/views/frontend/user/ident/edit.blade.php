@extends('frontend.layouts.app')

@section('title', '账号 - 实名认证')

@section('css')
    <style>
        .ident >.company>.layui-form-item >.layui-form-label{
            width: 110px;
        }
        .ident >.company>.layui-form-item >.layui-input-block{
            margin-left: 170px;
         }
        .ident >.company>.layui-form-item >.layui-upload >.layui-btn{
            margin-left: 30px;
        }
        .ident >.company>.layui-form-item >.layui-upload >.layui-upload-list{
            width: 500px;
            height: 300px;
            margin-left: 170px;
        }
        .ident >.company>.layui-form-item >.layui-upload >.layui-upload-list >img{
            width: 100%;
            height: 100%;
        }
        /*分割线*/
          .ident >.personal>.layui-form-item >.layui-form-label{
            width: 110px;
        }
        .ident >.personal>.layui-form-item >.layui-input-block{
           margin-left: 170px;
        }

        .ident >.personal>.layui-form-item >.layui-upload >.layui-btn{
            margin-left: 30px;
        }
        .ident >.personal>.layui-form-item >.layui-upload >.layui-upload-list{
            width: 500px;
            height: 300px;
            margin-left: 170px;
        }
        .ident >.personal>.layui-form-item >.layui-upload >.layui-upload-list >img{
            width: 100%;
            height: 100%;
        }
        .ident >.layui-form-item >.layui-input-block{
            margin-left: 170px;
        }
        .none{
            display: none;
        }
        .type>.layui-form-item>.layui-input-block{
            margin-left: 170px;
        }
        .layui-anim{
            color: #1E9FFF !important;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    @if($ident->type == 2)
    <div class = 'other'>
        <form class="layui-form" method="POST" action="{{ route('idents.update', ['id' => $ident->id]) }}" enctype="multipart/form-data">
            {!! csrf_field() !!}
                <div style="width: 80%" class="ident">
                <input type="hidden" name="type" value='2' >
                <input type="hidden" name="_method" value="PUT">
                <div class='company'>
                    <div class="layui-form-item">
                        <label class="layui-form-label">真实姓名</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" lay-verify="required" value="{{ old('name') ?: $ident->name }}" autocomplete="off" placeholder="请输入个人真实姓名" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">手机号</label>
                            <div class="layui-input-block">
                                <input type="text" name="phone_number" lay-verify="required|phone|number" value="{{ old('phone_number') ?: $ident->phone_number }}" placeholder="请输入手机号" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">开户银行卡号</label>
                        <div class="layui-input-block">
                            <input type="text" name="bank_number" lay-verify="required" value="{{ old('bank_number') ?: $ident->bank_number }}" autocomplete="off" placeholder="请输入开户银行卡号" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">开户银行名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="bank_name" lay-verify="required" value="{{ old('bank_name') ?: $ident->bank_name }}" autocomplete="off" placeholder="请输入详细银行名称如XX行XX支行" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">营业执照名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="license_name" lay-verify="required" value="{{ old('license_name') ?: $ident->license_name }}" autocomplete="off" placeholder="请输入执照名称" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">营业执照号码</label>
                        <div class="layui-input-block">
                            <input type="text" name="license_number" lay-verify="required" value="{{ old('license_number') ?: $ident->license_number }}" placeholder="请输入营业执照号码" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">法人姓名</label>
                        <div class="layui-input-block">
                            <input type="text" name="corporation" lay-verify="required" value="{{ old('corporation') ?: $ident->corporation }}" placeholder="请输入法人姓名" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">营业执照正面照</label>
                        <div class="layui-upload">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-small" id="license_picture">上传图片</button>
                            <input class="layui-upload-file" type="file" name="license_picture">
                            <div class="layui-upload-list">
                                <img class="layui-upload-img" id="demo1" src="{{ $ident->license_picture }}">
                                <input type="hidden" name="license_picture" value="{{ $ident->license_picture }}">
                                <p id="demoText"></p>
                            </div>
                        </div> 
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">银行开户许可证照片</label>
                        <div class="layui-upload">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-small" id="bank_open_account_picture">上传图片</button>
                            <input class="layui-upload-file" type="file" name="bank_open_account_picture">
                            <div class="layui-upload-list">
                                <img class="layui-upload-img" id="demo5" src="{{  $ident->bank_open_account_picture }}">
                                <input type="hidden" name="bank_open_account_picture" value="{{ $ident->bank_open_account_picture }}">
                                <p id="demoText"></p>
                            </div>
                        </div> 
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">代办协议照片</label>
                        <div class="layui-upload">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-small" id="agency_agreement_picture">上传图片</button>
                            <input class="layui-upload-file" type="file" name="agency_agreement_picture">
                            <div class="layui-upload-list">
                                <img class="layui-upload-img" id="demo6" src="{{ $ident->agency_agreement_picture }}">
                                <input type="hidden" name="agency_agreement_picture" value="{{ $ident->agency_agreement_picture }}">
                                <p id="demoText"></p>
                            </div>
                        </div> 
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">提交</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @elseif($ident->type == 1)
    <div class='self'>
            <form class="layui-form" method="POST" action="{{ route('idents.update', ['id' => $ident->id]) }}" enctype="multipart/form-data">
                {!! csrf_field() !!}
                    <div style="width: 80%" class="ident">
                    <input type="hidden" name="type" value='1' >
                    <input type="hidden" name="_method" value="PUT">
                    <div class='personal'>
                        <div class="layui-form-item">
                            <label class="layui-form-label">真实姓名</label>
                            <div class="layui-input-block">
                                <input type="text" name="name" lay-verify="required" value="{{ old('name') ?: $ident->name }}" autocomplete="off" placeholder="请输入个人真实姓名" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">手机号</label>
                                <div class="layui-input-block">
                                    <input type="text" name="phone_number" lay-verify="required|phone|number" value="{{ old('phone_number') ?: $ident->phone_number }}" placeholder="请输入手机号" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">开户银行卡号</label>
                            <div class="layui-input-block">
                                <input type="text" name="bank_number" lay-verify="required" value="{{ old('bank_number') ?: $ident->bank_number }}" autocomplete="off" placeholder="请输入开户银行卡号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">开户银行名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="bank_name" lay-verify="required" value="{{ old('bank_name') ?: $ident->bank_name }}" autocomplete="off" placeholder="请输入详细银行名称如XX行XX支行" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证号码</label>
                            <div class="layui-input-block">
                                <input type="text" name="identity_card" lay-verify="required|identity" value="{{ old('identity_card') ?: $ident->identity_card }}" placeholder="请输入身份证号码" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证正面照</label>
                            <div class="layui-upload">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-small" id="front">上传图片</button>
                                <div class="layui-upload-list">
                                <img class="layui-upload-img" id="demo2" src="{{ $ident->front_card_picture }}">
                                <input type="hidden" name="front_card_picture" value="{{ $ident->front_card_picture }}">
                                <p id="demoText"></p>
                                </div>
                            </div> 
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证背面照</label>
                            <div class="layui-upload">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-small" id="back">上传图片</button>
                                <div class="layui-upload-list">
                                    <img class="layui-upload-img" id="demo3" src="{{ $ident->back_card_picture }}">
                                    <input type="hidden" name="back_card_picture" value="{{ $ident->back_card_picture }}">
                                    <p id="demoText"></p>
                                </div>
                            </div> 
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">手持身份证的本人正面照</label>
                            <div class="layui-upload">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-small" id="hold">上传图片</button>
                                <div class="layui-upload-list">
                                <img class="layui-upload-img" id="demo4" src="{{ $ident->hold_card_picture }}">
                                <input type="hidden" name="hold_card_picture" value="{{ $ident->hold_card_picture }}">
                                <p id="demoText"></p>
                                </div>
                            </div> 
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">提交</button>
                        </div>
                    </div>
                </div>
            </form>
    </div>
    @endif
@endsection
<!--START 底部-->
@section('js')
    <script>
        //普通图片上传
       layui.use(['form', 'upload', 'layedit', 'laydate'], function () {
            var $ = layui.jquery,
            upload = layui.upload,
            form = layui.form,
            layer = layui.layer,
            layedit = layui.layedit;

            var error = "{{ $errors->count() > 0 ? '请上传相关照片!' : '' }}";

            if(error) {
                layer.msg(error, {icon: 5, time:1500});
            }

            //普通图片上传
            var uploadInst = upload.render({
                elem: '#license_picture' //上传按钮的ID
                ,
                url: "{{ route('ident.upload-images') }}" //接口
                    ,
                size: 3000 //限制文件大小，单位 KB
                    ,
                accept: 'file' //普通文件
                    ,
                exts: 'jpg|jpeg|png|gif' //只允许上传图片的类型
                    ,
                before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        $('#demo1').attr('src', result); //图片链接（base64）
                    });
                },
                done: function (res) {
                    //如果上传失败
                    if (res.code == 2) {
                        return layer.msg('上传失败');
                    }
                    //上传成功
                    $("input[name='license_picture']").val(res.path); //填充图片路径
                   
                },
                error: function () {
                }
            });


            //普通图片上传
            var uploadInst = upload.render({
                elem: '#front' //上传按钮的ID
                ,
                url: "{{ route('ident.upload-images') }}" //接口
                    ,
                size: 3000 //限制文件大小，单位 KB
                    ,
                accept: 'file' //普通文件
                    ,
                exts: 'jpg|jpeg|png|gif' //只允许上传图片的类型
                    ,
                before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        $('#demo2').attr('src', result); //图片链接（base64）
                    });
                },
                done: function (res) {
                    //如果上传失败
                    if (res.code == 2) {
                        return layer.msg('上传失败');
                    }
                    //上传成功
                    $("input[name='front_card_picture']").val(res.path); //填充图片路径
                    
                },
                error: function () {
                }
            });

            //普通图片上传
            var uploadInst = upload.render({
                elem: '#back' //上传按钮的ID
                ,
                url: "{{ route('ident.upload-images') }}" //接口
                    ,
                size: 3000 //限制文件大小，单位 KB
                    ,
                accept: 'file' //普通文件
                    ,
                exts: 'jpg|jpeg|png|gif' //只允许上传图片的类型
                    ,
                before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        $('#demo3').attr('src', result); //图片链接（base64）
                    });
                },
                done: function (res) {
                    //如果上传失败
                    if (res.code == 2) {
                        return layer.msg('上传失败');
                    }
                    //上传成功
                    $("input[name='back_card_picture']").val(res.path); //填充图片路径
                    
                },
                error: function () {
                }
            });

            //普通图片上传
            var uploadInst = upload.render({
                elem: '#hold' //上传按钮的ID
                ,
                url: "{{ route('ident.upload-images') }}" //接口
                    ,
                size: 3000 //限制文件大小，单位 KB
                    ,
                accept: 'file' //普通文件
                    ,
                exts: 'jpg|jpeg|png|gif' //只允许上传图片的类型
                    ,
                before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        $('#demo4').attr('src', result); //图片链接（base64）
                    });
                },
                done: function (res) {
                    //如果上传失败
                    if (res.code == 2) {
                        return layer.msg('上传失败');
                    }
                    //上传成功
                    $("input[name='hold_card_picture']").val(res.path); //填充图片路径
   
                },
                error: function () {
                }
            });

             //普通图片上传
            var uploadInst = upload.render({
                elem: '#bank_open_account_picture' //上传按钮的ID
                ,
                url: "{{ route('ident.upload-images') }}" //接口
                    ,
                size: 3000 //限制文件大小，单位 KB
                    ,
                accept: 'file' //普通文件
                    ,
                exts: 'jpg|jpeg|png|gif' //只允许上传图片的类型
                    ,
                before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        $('#demo5').attr('src', result); //图片链接（base64）
                    });
                },
                done: function (res) {
                    //如果上传失败
                    if (res.code == 2) {
                        return layer.msg('上传失败');
                    }
                    //上传成功
                    $("input[name='bank_open_account_picture']").val(res.path); //填充图片路径
   
                },
                error: function () {
                }
            });

             //普通图片上传
            var uploadInst = upload.render({
                elem: '#agency_agreement_picture' //上传按钮的ID
                ,
                url: "{{ route('ident.upload-images') }}" //接口
                    ,
                size: 3000 //限制文件大小，单位 KB
                    ,
                accept: 'file' //普通文件
                    ,
                exts: 'jpg|jpeg|png|gif' //只允许上传图片的类型
                    ,
                before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        $('#demo6').attr('src', result); //图片链接（base64）
                    });
                },
                done: function (res) {
                    //如果上传失败
                    if (res.code == 2) {
                        return layer.msg('上传失败');
                    }
                    //上传成功
                    $("input[name='agency_agreement_picture']").val(res.path); //填充图片路径
   
                },
                error: function () {
                }
            });



        });
    </script>
@endsection