<!DOCTYPE html>
<html>

<head>
    <title>我要发单</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.">
    <link rel="stylesheet" href="/mobile/lib/css/weui.min.css">
    <link rel="stylesheet" href="/mobile/lib/css/jquery-weui.css">
    <link rel="stylesheet" href="/mobile/lib/css/font.css">
    <link rel="stylesheet" href="/mobile/lib/css/reset.css">
    <link rel="stylesheet" href="/mobile/lib/css/common.css">
    <link rel="stylesheet" href="/mobile/css/withdrawls.css">
</head>

<body style="background-color: #fff;">
    <!-- header -->
    <div class="header">
        <div class="weui-flex">
            <div class="weui-flex__item">付款成功</div>
        </div>
    </div>
    <!-- header -->
    <!-- main -->
    <div class="main" style="text-align: center;">
        <img src="/mobile/lib/images/success.png" alt="" style="width: 60px;height: 60px;margin-top: 100px;">
        <p style="text-align: center;font-size: 16px;color: #58b720;margin-top: 10px;font-weight: 600;">付款成功</p>
        <p style="font-size: 14px;color: #939393;margin-top: 12px;">正在给你安排代练员，完成后订单会变成交易成功哦</p>
       <div class="footer">
            <a class="weui-btn weui-btn_default tb-bg" href="{{ route('mobile.leveling.show', ['id' => $mobileOrder->id]) }}" style="margin-right: 1px;">订单详情</a>
                <a class="weui-btn weui-btn_default tb-bg" href="javascript:" id="close" href="{{ route('mobile.leveling.demand') }}">关闭</a>
       </div>
    </div>
    <!-- main -->
    <script src="/mobile/lib/js/jquery-2.1.4.js"></script>
    <script src="/mobile/lib/js/fastclick.js"></script>
    <script>
        $(function () {
            FastClick.attach(document.body);
        });
    </script>
    <script src="/mobile/lib/js/jquery-weui.js"></script>
    <script src="/mobile/lib/js/swiper.js"></script>
    <script>
            $(".swiper-container").swiper({
              loop: true,
              autoplay: 3000
            });

            function close(){
                window.location.href="{{ route('mobile.leveling.demand') }}";
            }
    </script>
</body>

</html>