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

            <div class="layui-upload">
              <button type="button" class="layui-btn" id="test1">上传图片</button>
              <div class="layui-upload-list">
                <img class="layui-upload-img" id="demo1">
                <p id="demoText"></p>
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
    //     layui.use(['form','upload'], function(){
    //       var form = layui.form()
    //       ,layer = layui.layer;
 

  
    // //上传照片
    // var loading;
    // $.each($('.layui-upload-file'), function(index, val) {
    //     layui.upload({
    //         elem:{"dom":val.id,"token":"{{ csrf_token() }}","name":"qq"},
    //         url: '/v1/business/image',
    //         method: 'post',
    //         before:function(){
    //             loading = layer.load(1, {
    //               shade: [0.1,'#000'] //0.1透明度的白色背景
    //             });
    //         },
    //         success: function(res){
    //             // console.log(res);
    //             layer.closeAll();
    //             // console.log(res); //上传成功返回值，必须为json格式
    //             $('#'+val.id).parents("label").css('background-image','url('+res+')'); //预览图
    //             $("input[name='"+val.id+"path']").val(res); //填充图片路径
    //         }
    //     });
    // });
    // });

    layui.use('upload', function(){
  var $ = layui.jquery
  ,upload = layui.upload;
  
  //普通图片上传
  var uploadInst = upload.render({
    elem: '#test1'
    ,url: '/upload/'
    ,before: function(obj){
      //预读本地文件示例，不支持ie8
      obj.preview(function(index, file, result){
        $('#demo1').attr('src', result); //图片链接（base64）
      });
    }
    ,done: function(res){
      //如果上传失败
      if(res.code > 0){
        return layer.msg('上传失败');
      }
      //上传成功
    }
    ,error: function(){
      //演示失败状态，并实现重传
      var demoText = $('#demoText');
      demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
      demoText.find('.demo-reload').on('click', function(){
        uploadInst.upload();
      });
    }
  });
  
  //指定允许上传的文件类型
  upload.render({
    elem: '#test3'
    ,url: '/upload/'
    ,accept: 'file' //普通文件
    ,done: function(res){
      console.log(res)
    }
  });
  upload.render({ //允许上传的文件后缀
    elem: '#test4'
    ,url: '/upload/'
    ,accept: 'file' //普通文件
    ,exts: 'zip|rar|7z' //只允许上传压缩文件
    ,done: function(res){
      console.log(res)
    }
  });
  upload.render({
    elem: '#test5'
    ,url: '/upload/'
    ,accept: 'video' //视频
    ,done: function(res){
      console.log(res)
    }
  });
  upload.render({
    elem: '#test6'
    ,url: '/upload/'
    ,accept: 'audio' //音频
    ,done: function(res){
      console.log(res)
    }
  });
  
  //设定文件大小限制
  upload.render({
    elem: '#test7'
    ,url: '/upload/'
    ,size: 60 //限制文件大小，单位 KB
    ,done: function(res){
      console.log(res)
    }
  });
  
  //同时绑定多个元素，并将属性设定在元素上
  upload.render({
    elem: '.demoMore'
    ,before: function(){
      layer.tips('接口地址：'+ this.url, this.item, {tips: 1});
    }
    ,done: function(res, index, upload){
      var item = this.item;
      console.log(item); //获取当前触发上传的元素，layui 2.1.0 新增
    }
  })
  
  //选完文件后不自动上传
  upload.render({
    elem: '#test8'
    ,url: '/upload/'
    ,auto: false
    //,multiple: true
    ,bindAction: '#test9'
    ,done: function(res){
      console.log(res)
    }
  });
  
  //拖拽上传
  upload.render({
    elem: '#test10'
    ,url: '/upload/'
    ,done: function(res){
      console.log(res)
    }
  });
  
  //多文件列表示例
  var demoListView = $('#demoList')
  ,uploadListIns = upload.render({
    elem: '#testList'
    ,url: '/upload/'
    ,accept: 'file'
    ,multiple: true
    ,auto: false
    ,bindAction: '#testListAction'
    ,choose: function(obj){   
      var files = obj.pushFile(); //将每次选择的文件追加到文件队列
      //读取本地文件
      obj.preview(function(index, file, result){
        var tr = $(['<tr id="upload-'+ index +'">'
          ,'<td>'+ file.name +'</td>'
          ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
          ,'<td>等待上传</td>'
          ,'<td>'
            ,'<button class="layui-btn layui-btn-mini demo-reload layui-hide">重传</button>'
            ,'<button class="layui-btn layui-btn-mini layui-btn-danger demo-delete">删除</button>'
          ,'</td>'
        ,'</tr>'].join(''));
        
        //单个重传
        tr.find('.demo-reload').on('click', function(){
          obj.upload(index, file);
        });
        
        //删除
        tr.find('.demo-delete').on('click', function(){
          delete files[index]; //删除对应的文件
          tr.remove();
        });
        
        demoListView.append(tr);
      });
    }
    ,done: function(res, index, upload){
      if(res.code == 0){ //上传成功
        var tr = demoListView.find('tr#upload-'+ index)
        ,tds = tr.children();
        tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
        tds.eq(3).html(''); //清空操作
        delete files[index]; //删除文件队列已经上传成功的文件
        return;
      }
      this.error(index, upload);
    }
    ,error: function(index, upload){
      var tr = demoListView.find('tr#upload-'+ index)
      ,tds = tr.children();
      tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
      tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
    }
  });
  
  //绑定原始文件域
  upload.render({
    elem: '#test20'
    ,url: '/upload/'
    ,done: function(res){
      console.log(res)
    }
  });
  

});
    </script>
@endsection