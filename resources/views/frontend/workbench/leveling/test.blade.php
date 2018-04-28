@extends('frontend.layouts.app')

@section('title', '工作台 - 代练')

@section('css')
    <style>
        @charset "utf-8";
        /* CSS Document */

        html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, dd, dl, dt, li, ol, ul,input,select,button,textarea { padding:0; margin:0; border:none;}
        input,button,select,textarea,a,img{outline:none; }/*去掉超链接或按钮点击时出现的虚线框黄色边框*/
        ::-moz-focus-inner{border:0;}/*火狐的私有属性去掉点击时边框*/
        article, aside, dialog, figure, footer, header, hgroup, nav, section, blockquote { display: block; }
        ul, ol { list-style: none; }
        img { border: 0 none; vertical-align: top; }
        blockquote, q { quotes: none; }
        blockquote:before, blockquote:after, q:before, q:after { content: none; }
        table { border-collapse: collapse; border-spacing: 0; }
        strong, em, i { font-style: normal; font-weight: normal; }
        ins { text-decoration: underline; }
        del { text-decoration: line-through; }
        mark { background: none; }
        input::-ms-clear { display: none !important; }
        input, textarea {border: 0;font-family:"Microsoft YaHei";}
        html{ overflow: hidden;}
        body {font-size:12px; width:100%; padding: 0;  font-family:"Microsoft YaHei","Arial", "SimSun";}
        a { text-decoration: none; color: #333; }
        a:hover { text-decoration: none; }
        .clearfix:after {visibility: hidden;display: block;font-size: 0;content: ".";clear: both;height: 0;}
        * html .clearfix {zoom: 1;}
        *:first-child + html .clearfix {zoom: 1;}



        /*上传图片通用样式*/
        .upload-ul{ position: relative;  display: inline-block; *display: inline; *zoom:1; max-width: 520px; }
        .upload-ul li{ position: relative; float: left; display: inline-block; width: 120px; height: 90px; margin: 0 10px 10px 0; padding: 0; border: none; cursor: pointer; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px; overflow: hidden; }
        .upload-pick{ background: url(../images/upload-bj.png) no-repeat 0 0; }
        .upload-pick:hover{ background: url(../images/upload-bj.png) no-repeat 0 -90px; }
        .webuploader-pick{position: relative;display: inline-block;vertical-align: top; width: 100%; height: 100%;}
        .webuploader-container{ position: relative; width: 100%; height: 100%; }
        .webuploader-container label{position: absolute;left: 0;top: 0;width: 100%;}
        .webuploader-element-invisible{opacity: 0;width: 100%;height: 100%;clip: rect(1px 1px 1px 1px);clip: rect(1px,1px,1px,1px);}
        .viewThumb{ position:relative;width: 100%;height: 100%;overflow:hidden;border-radius: .3rem;}
        .viewThumb img{ width: 100%;height: 100%}
        .diyBar{ position: absolute; display:none; top: 0;left: 0;width: 100%;height: 100%;background: url(../images/bgblack.png);z-index: 3;}
        .diyProgress{ position: absolute; left: 0;top: 33px;width: 100%;height: 24px;line-height:24px;font-size: 14px;text-align: center;color: #FFF;background:rgba(10,168,241,.7); z-index: 3;}
        .diyControl{ position: absolute; display:none; left: 0;bottom: 0;width: 100%;height: 24px;line-height:24px;font-size: 14px;background: url(../images/bgblack.png); z-index: 3; }
        .viewThumb:hover .diyControl{ display: block; }
        .diyControl span{ display: inline-block; padding: 6.5px 13px; width: 12px; height: 11px; }
        .diyControl span i{ display: block; width: 12px; height: 11px; opacity: .7; }
        .diyControl span i:hover{ opacity: 1; cursor: pointer; }
        .diyLeft{ margin-left: 3px; }
        .diyLeft{ margin-right: 3px; }
        .diyLeft i{ margin-left: 3px; background: url(../images/upload-icon1.png) no-repeat 0 0; }
        .diyCancel i{ background: url(../images/upload-icon1.png) no-repeat 0 -11px; }
        .diyRight i{ margin-right: 3px; background: url(../images/upload-icon1.png) no-repeat 0 -22px; }

        .upload-btn{ display: inline-block; padding: 10px 50px; font: 16px/20px 'Microsoft YaHei'; background:rgba(10,168,241,.7); color: #fff; border-radius: 5px; }
        .upload-btn:hover{ background:rgba(10,168,241,1) }

    </style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')

@endsection

@section('js')
    <script src="/js/webupload.js"></script>
    <script src="/js/diyupload.js"></script>
    <script>
        $(function(){

            //上传图片
            var $tgaUpload = $('#goodsUpload').diyUpload({
                url:'/uploadFilePath',
                success:function( data ) { },
                error:function( err ) { },
                buttonText : '',
                accept: {
                    title: "Images",
                    extensions: 'gif,jpg,jpeg,bmp,png'
                },
                thumb:{
                    width:120,
                    height:90,
                    quality:100,
                    allowMagnify:true,
                    crop:true,
                    type:"image/jpeg"
                }
            });


        });
    </script>
@endsection