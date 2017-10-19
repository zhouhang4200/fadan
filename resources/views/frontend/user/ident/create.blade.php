@extends('frontend.layouts.app')

@section('title', '账号 - 实名认证')

@section('css')
    <style>
        .layui-form-label {
            width:65px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="POST" action="{{ route('users.store') }}">
        {!! csrf_field() !!}
        <div style="width: 100%">
            <div class="layui-form-item">
                <label class="layui-form-label">执照名称</label>
                <div class="layui-input-block">
                  <input type="text" name="license_name" value="{{ old('license_name') }}" lay-verify="title" autocomplete="off" placeholder="请输入执照名称" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">执照注册号</label>
                <div class="layui-input-block">
                  <input type="text" name="license_number" value="{{ old('license_number') }}" lay-verify="title" autocomplete="off" placeholder="请输入执照号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block overflow">
                    <fieldset class="layui-elem-field layui-field-title" >
                      <legend>上传营业执照</legend>
                    </fieldset>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block overflow">
                    <label class="left h5-upload-file cm-bac relative"  >
                        <input type="file" name="license_image" class="layui-upload-file" id="license_image">
                    </label>
                    <input type="text" class='none' name="license_imagepath">
                    <div class="left h5-exp">示例：</div>
                    <div class="left h5-upload-file cm-bac" style="background-image:url(/resources/img/license.jpg)"></div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">法人姓名</label>
                <div class="layui-input-block">
                  <input type="text" name="corporation" value="{{ old('corporation') }}" lay-verify="required" autocomplete="off" placeholder="请输入法人姓名" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">法人身份证</label>
                <div class="layui-input-block overflow">
                    <input type="text" name="identity" value="{{ old('identity') }}" lay-verify="required|identity" autocomplete="off" placeholder="请输入身份证" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block overflow">
                    <fieldset class="layui-elem-field layui-field-title" >
                      <legend>上传身份证正面照</legend>
                    </fieldset>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block overflow">
                    <label class="left h5-upload-file cm-bac relative">
                        <input type="file" name="front_image" class="layui-upload-file" id="front_image">
                    </label>
                    <input type="text" class='none' name="front_imagepath">
                    <div class="left h5-exp">示例：</div>
                    <div class="left h5-upload-file cm-bac" style="background-image:url(/statics/client-v1/img/zm.png)"></div>
                </div>
            </div>      

            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block overflow">
                    <fieldset class="layui-elem-field layui-field-title" >
                      <legend>上传身份证背面照</legend>
                    </fieldset>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block overflow">
                    <label class="left h5-upload-file cm-bac relative">
                        <input type="file" name="back_image" class="layui-upload-file" id="back_image">
                    </label>
                    <input type="text" class='none' name="back_imagepath">
                    <div class="left h5-exp">示例：</div>
                    <div class="left h5-upload-file cm-bac" style="background-image:url(/statics/client-v1/img/fm.png)"></div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block overflow">
                    <fieldset class="layui-elem-field layui-field-title" >
                      <legend>上传手持身份证照</legend>
                    </fieldset>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block overflow">
                    <label class="left h5-upload-file cm-bac relative">
                        <input type="file" name="hold_image" class="layui-upload-file" id="hold_image">
                    </label>
                    <input type="text" class='none' name="hold_imagepath">
                    <div class="left h5-exp">示例：</div>
                    <div class="left h5-upload-file cm-bac" style="background-image:url(/statics/client-v1/img/sc.jpg)"></div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">联系手机</label>
                <div class="layui-input-block">
                  <input type="text" name="tel" value="{{ old('tel') }}" lay-verify="required|phone|number" autocomplete="off" placeholder="请输入手机号码" class="layui-input phone">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">验证码</label>
                <div class="layui-input-inline">
                  <input type="text" name="code" lay-verify="required|number" autocomplete="off" placeholder="请输入验证码" class="layui-input">
                </div>
                <div class="layui-btn layui-btn-normal get-code" >获取验证码</div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">立即提交</button>
                </div>
            </div>
        </div>
    </form>
@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form','upload'], function(){
  var form = layui.form()
  ,layer = layui.layer;
 
  //自定义验证规则
  form.verify({
    code: function(value){
      if(value.length < 1){
        return '请填写验证码';
      }
    }
    ,pass: [/(.+){6,12}$/, '密码必须6到12位']
    ,content: function(value){
      layedit.sync(editIndex);
    }
  });
  
    //上传照片
    var loading;
    $.each($('.layui-upload-file'), function(index, val) {
        layui.upload({
            elem:{"dom":val.id,"token":"{{ csrf_token() }}","name":"qq"},
            url: '/v1/business/image',
            method: 'post',
            before:function(){
                loading = layer.load(1, {
                  shade: [0.1,'#000'] //0.1透明度的白色背景
                });
            },
            success: function(res){
                // console.log(res);
                layer.closeAll();
                // console.log(res); //上传成功返回值，必须为json格式
                $('#'+val.id).parents("label").css('background-image','url('+res+')'); //预览图
                $("input[name='"+val.id+"path']").val(res); //填充图片路径
            }
        });
    });
  
  // 获取验证码
    var lock=true;
    $('.get-code').on('click',function(e){
        e.stopPropagation();

        if(!lock||!$('.phone').val()) return;
        $.post('', {'id':,'tel':$('.phone').val() }, function(data) {
            console.log(data);
            lock=false;
            var count=60;
            $('.get-code').html(60+'s');
            var timer=setInterval(function(){
                count--;
                if(count==0){
                    $('.get-code').html('获取');
                    clearInterval(timer);
                    lock=true;
                }else{
                    $('.get-code').html(count+'s');
                }
            },1000)
        });
    });
  
  // 监听提交
  form.on('submit(sub)', function(data){
    // layer.alert(JSON.stringify(data.field), {
    //   title: '最终的提交信息'
    // })
    // return false;
  });
  
  // 查看协议
  $('#xy').on('click',function(){
     layer.open({
        type: 1
        ,title:''
        ,area: ['800px', '80%']
        ,content: $('ul.xy-list')
        // ,shade: 0 //不显示遮罩
        ,yes: function(){
          layer.closeAll();
        }
      });
  })

  form.on('checkbox(xy)', function(data){
      if(data.elem.checked){
        $("button#submit").removeClass('layui-btn-disabled').prop('disabled','');
      }else{
        $("button#submit").addClass('layui-btn-disabled').prop('disabled', 'disabled');
      }; 
  });        
      
  
});
    </script>
@endsection