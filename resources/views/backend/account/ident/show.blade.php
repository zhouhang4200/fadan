@extends('backend.layouts.main')

@section('title', ' | 实名认证')

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
            visibility: 
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

@section('content')
   <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">实名认证详情</li>
                        </ul>
                        <input id="userId" type="hidden" name="userId" value="{{ $ident->user_id}}">
                        <div class="layui-tab-content">
                        @if ($ident->type == 2)
                            <div class = 'other'>
                            <form class="layui-form">
                                        <div style="width: 80%" class="ident">
                                        <div class='company'>
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
                                                    
                                                    <input class="layui-upload-file" type="file" name="agency_agreement_picture">
                                                    <div class="layui-upload-list">
                                                        <img class="layui-upload-img" id="demo6" src="{{ $ident->agency_agreement_picture }}">
                                                        <input type="hidden" name="agency_agreement_picture" value="{{ $ident->agency_agreement_picture }}">
                                                        <p id="demoText"></p>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="layui-form-item">
                                            <label class="layui-form-label">拒绝原因</label>
                                                <div class="layui-input-block">
                                                <input type="text" id="ident-message" name="message" lay-verify="" value="{{ $ident->message }}" autocomplete="off" placeholder="如果不通过，请输入拒绝原因" class="layui-input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <div class="layui-input-block">
                                                <button class="layui-btn layui-btn-normal" lay-submit="" id="pass" lay-filter="pass">通过</button>
                                                <button class="layui-btn layui-btn-normal" lay-submit="" id="refuse" lay-filter="refuse">拒绝</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @elseif ($ident->type == 1)
                            <div class='self'>
                                <form class="layui-form">
                                        <div style="width: 80%" class="ident">
                                        <div class='personal'>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">身份证号码</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="identity_card" lay-verify="required|identity" value="{{ old('identity_card') ?: $ident->identity_card }}" placeholder="请输入身份证号码" autocomplete="off" class="layui-input">
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">手机号</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="phone_number" lay-verify="required|phone|number" value="{{ old('phone_number') ?: $ident->phone_number }}" placeholder="请输入手机号" autocomplete="off" class="layui-input">
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">身份证正面照</label>
                                                <div class="layui-upload">
                                                
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
                                                    
                                                    <div class="layui-upload-list">
                                                    <img class="layui-upload-img" id="demo4" src="{{ $ident->hold_card_picture }}">
                                                    <input type="hidden" name="hold_card_picture" value="{{ $ident->hold_card_picture }}">
                                                    <p id="demoText"></p>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="layui-form-item">
                                            <label class="layui-form-label">拒绝原因</label>
                                                <div class="layui-input-block">
                                                <input type="text" id="ident-message" name="message" lay-verify="" value="{{ $ident->message }}" autocomplete="off" placeholder="如果不通过，请输入拒绝原因" class="layui-input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" id="pass" lay-filter="pass">通过</button>
                                            <button class="layui-btn layui-btn-normal" lay-submit="" id="refuse" lay-filter="refuse">拒绝</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            var layer = layui.layer;
            var userId = $('#userId').val();

            form.on('submit(pass)', function (data) {
                $.ajax({
                    url:"{{ route('pass-or-refuse.pass') }}",
                    method:"POST",
                    data:{'userId': userId},
                    success:function (data) {
                        if (data.code == 1) {
                            layer.msg('审核完成，状态：通过！', {icon: 6, time:1500});
                            window.location.href = "{{ route('admin-idents.index') }}";
                        } else {
                            layer.msg(data.message, {icon: 5, time:1500});
                        }
                    }
                });
                return false;
            });

            form.on('submit(refuse)', function (data) {
                var message = data.field.message;

                $.ajax({
                    method:'POST',
                    url:"{{ route('pass-or-refuse.refuse') }}",
                    data:{'userId': userId, 'message': message},
                    success:function (data) {
                        if (data.code == 1) {
                            layer.msg('审核完成， 状态：不通过！', {icon: 6, time:1500});
                             window.location.href = "{{ route('admin-idents.index') }}";
                        } else {
                            layer.msg(data.message, {icon: 5, time:1500});
                        }
                        
                    }
                });

                return false;
            });
            
            form.render();
        });  

    </script>
@endsection