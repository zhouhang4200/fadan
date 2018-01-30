<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="_token" content="{{ csrf_token() }}" >

    <link rel="stylesheet" href="/frontend/exchange/js/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/exchange/css/common.css">
    <link rel="stylesheet" href="/frontend/exchange/less/phone.css">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script>
        //动态设置兑换说明处padding
        $(window).ready(function () {
            widthOthe();
        });
        $(window).resize(function () {
            widthOthe();
        });
        $(window).ready(function () {
            if ($(window).width() <= 750) {
                $('#imgCode input').css("width", '100%');
                $('#imgCode a').css("width", '100%');
            }
        });
        function widthOthe() {
            if ($(window).width() >= 700) {
                $('.prompt').css({'padding-left': '15%'})
            }
            if ($(window).width() < 400) {
                $('.prompt').css({'padding-left': '0'})
            }
            if ($(window).width() <= 700) {
                $("body").css("background-image", "url(/frontend/exchange/images/phone/bg_1.jpg)");
            } else {
                $('body').css({'background-image': 'url("/frontend/exchange/images/bg.jpg")'})
            }
        }
    </script>
    <style>
        .success{
            padding: 20px;
            background: #32353f;
            color: #fff;
        }
    </style>
    <title>登录</title>
</head>

<body>
<div class="title">
    <img src="/frontend/exchange/images/phone/title_03.png" alt="">
</div>
<div class="login">
    <form class="layui-form">
        <input type='hidden' name="_token" value="{{ csrf_token() }}">
        <input type="hidden" id="lo_value" name="lo_value"/>
        <input type="hidden" name="cdk" value="{{$cdkey->cdk}}"/>
        <div class="layui-form-item">
            <div class="layui-input-block colr">Steam账号</div>
            <div class="layui-input-block">
                <input type="text" name="username" value="" lay-verify="required" autocomplete="no" placeholder="请输入账号"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block colr">密码</div>
            <div class="layui-input-block">
                <input type="password" name="password" value="" lay-verify="required" placeholder="请输入密码" autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item none" id="phoneNum">
            <div class="layui-input-block colr">手机令牌</div>
            <div class="layui-input-block">
                <input type="text" name="phoneNum" lay-verify="phoneNum" placeholder="请输入令牌" autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item none" id="mailCode">
            <div class="layui-input-block colr">邮箱验证码</div>
            <div class="layui-input-block">
                <input type="text" name="mailCode" lay-verify="mailCode" placeholder="请输入邮箱验证码" autocomplete="off"
                       class="layui-input">
            </div>
        </div>

        <div class="layui-form-item none" id="imgCode">
            <div class="layui-input-block colr">图片验证码 </div>
            <div class="layui-input-block imgcod">
                <input type="text" name="imgCode" lay-verify="imgCode" placeholder="请输入验证码" autocomplete="off" class="layui-input">
                <a href="#"><img src="" alt="" class="verification"></a>
            </div>
        </div>

        <!-- 移动端 验证码换行 -->
        <div class="layui-form-item phone none">
            <div class="layui-input-block colr">图片验证码 </div>
            <div class="layui-input-block">
                <input type="text" name="imgCod" lay-verify="imgCod" placeholder="请输入验证码" autocomplete="off" class="layui-input">
                <img src="../images/phone/vkey.png" alt="" class="verification">
            </div>
        </div>

        <div class="layui-form-item" style="margin-top:50px;">
            <div class="layui-input-block">

                {{--<button class="sub" lay-submit="" lay-filter="sub">立即提交</button>--}}
                <button class="layui-btn sub" lay-submit lay-filter="sub">立即提交</button>
            </div>
        </div>
        <div class="layui-form-item" style="margin-top:50px;min-width:275px;color:#fff">
            <div class="layui-input-block">
                <div class="title"><img src="/frontend/exchange/images/phone/1.png" alt=""> 兑换说明 <img
                            src="/frontend/exchange/images/phone/2.png" alt=""></div>
                <div class="prompt">
                    <span>1.  &nbsp;steam游戏代购是通过礼物的形式送到您的steam账号中</span><br>
                    <span>2.  &nbsp;交易过程是由系统自动完成，所以需要获取您的登录信息</span><br>
                    <span>3.  &nbsp;当订单状态显示充值成功后，即可登录steam平台领取礼物</span><br>
                    <span>4.  &nbsp;非中国区注册的账号无法领取礼物，可联系卖家售后</span><br>
                </div>
            </div>
        </div>

    </form>

</div>

<script src="/frontend/exchange/js/layui/layui.js"></script>

<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});
    layui.use(['form', 'layedit', 'laydate'], function () {
        var layer = layui.layer,
            form = layui.form;

        form.on('submit(sub)', function(data){
            var index = layer.load(1, {
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });
            $.post('{{ url('exchange/login-exchange')}}', {data:data.field},function (result) {
                // 需要验证码
                if (result.status == 3) {
                    $('#imgCode').removeClass('none');
                    $('.verification').attr('src',   result.data.code_img);
                    $('#lo_value').val(result.data.lo_value);
                    $('#phoneNum').addClass('none');
                    $('#mailCode').addClass('none');
                }
                if (result.status == 1) {
                    $('#phoneNum').removeClass('none');
                    $('#phoneNum').removeClass('none');
                    $('#mailCode').addClass('none');
                    $('#imgCode').addClass('none');
                    $('#lo_value').val(result.data.lo_value);
                }
                if (result.status == 2) {
                    $('#mailCode').removeClass('none');
                    $('#lo_value').val(result.data.lo_value);
                    $('#phoneNum').addClass('none');
                    $('#imgCode').addClass('none');
                }
                layer.close(index);
                if (result.status == 7) {
                    layer.msg('兑换成功，请稍后查询兑换状态');
                } else {
                    layer.msg(result.message);
                }
            }, 'json');
            return false;
        });
    });
    if (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.split(";")[1].replace(/[ ]/g, "") ==
        "MSIE8.0") {
        $(".layui-input").css({"padding":"0","height":"40px","text-indent":"10px","color":"#000"})
    }
    if (document.all && window.XMLHttpRequest && !document.querySelector) {
        $(".layui-input").css({"padding":"0","height":"40px","text-indent":"10px"})
        layui.use(['form', 'layedit', 'laydate'], function () {
            var layer = layui.layer;
            layer.alert("为了您正常使用本系统请升级更高版本浏览器", {
                title: '提示',
                icon: 2
            })
        });
    }
    /*if(document.addEventListener != true){
        console.log(1)
        $(".layui-input").css({"padding":"0","height":"40px","text-indent":"10px","color":"#000"})
    }*/
</script>
</body>

</html>