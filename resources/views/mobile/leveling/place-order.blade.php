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
    <link rel="stylesheet" href="/mobile/lib/css/mobileSelect.css">
    <link rel="stylesheet" href="/mobile/css/withdrawls.css">
</head>

<body ontouchstart>
    <!-- header -->
    <div class="header">
        <div class="weui-flex">
            <div class="weui-flex__item">填写代练订单</div>
            <a href="./index.html" class="back iconfont icon-back"></a>
        </div>
    </div>
    <!-- header -->
    <!-- main -->
    <div class="main">

        <form action="">
            <input type="hidden" name="game_name" value="{{ $gameName }}"></input>
            <input type="hidden" name="type" value="{{ $type }}"></input>
            <div class="weui-cells weui-cells_form  weui-cells_radio">
                <div class="pic_box" style="height: 130px;">
                    <div class="pic">
                        <div class="title">英雄联盟-排位</div>
                        <img src="/mobile/lib/images/pic.png" alt="" style="top: 35px;">
                        <div class="new_pic" style="top: 35px;">{{ $payment }}元</div>
                        <div class="old_pic" style="top: 60px;">代练价格
                            <s>{{ $showPrice }}元</s>
                        </div>
                    </div>
                    <div class="time">
                        <div class="title">{{ $startLevel }} - {{ $endLevel }}</div>
                        <img src="/mobile/lib/images/time.png" alt="" style="top: 35px;">
                        <div class="time_" style="top: 35px;">{{ $showTime }}</div>
                        <div class="old_pic" style="top: 60px;">预计耗时</div>
                    </div>
                </div>
                <div class="weui-cell weui-cell_select">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            区
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <div class="trigger5 operation operation1" id="region">请选择</div>
                        <select class="weui-select f-dn" name="region">
                            <option selected="" value=""></option>
                        </select>
                    </div>
                </div>
                <div class="weui-cell weui-cell_select">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            服
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <div class="trigger5 operation operation2" id="server">请选择</div>
                        <select class="weui-select f-dn" name="server">
                            <option selected="" value=""></option>
                        </select>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            角色名称
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" name="role" placeholder="请输入">
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            账号
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" name="account" placeholder="请输入">
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            密码
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" name="password" placeholder="请输入">
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            玩家电话
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" name="client_qq" placeholder="请输入">
                    </div>
                </div>
                <p style="text-indent: 20px;margin-top:20px;background-color: #fff;height: 30px;line-height: 30px;border-bottom: 1px solid #eee;">支付方式</p>
                <label class="weui-cell weui-check__label" style="border-bottom: 1px solid #eee;">
                    <div class="weui-cell__bd">
                        <i class="iconfont icon-zhifubaozhifu"></i>
                        <p style="width: 70%;height: 45px;line-height: 45px;text-indent: 10px;">支付宝</p>
                    </div>
                    <div class="weui-cell__hd">
                        <input type="radio" class="weui-check" name="alipay" id="alipay" checked="checked">
                        <i class="weui-icon-checked"></i>
                    </div>
                </label>
                <label class="weui-cell weui-check__label" style="border-bottom: 1px solid #eee;" >
                    <div class="weui-cell__bd">
                        <i class="iconfont icon-weixin1"></i>
                        <p style="width: 70%;height: 45px;line-height: 45px;text-indent: 10px;">微信</p>
                    </div>
                    <div class="weui-cell__hd">
                        <input type="radio" class="weui-check" name="wechat" id="wechat">
                        <i class="weui-icon-checked"></i>
                    </div>
                </label>
                <button type="submit" class="weui-btn weui-btn_default tb-bg" href="javascript:" id="showTooltips">我要代练</a>
            </div>
        </form>
    </div>
    <!-- main -->
    <script src="/mobile/lib/js/jquery-2.1.4.js"></script>
    <script src="/mobile/lib/js/fastclick.js"></script>
    <script src="/mobile/lib/js/mobileSelect.js"></script>
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
    </script>
    <script>
        var max_h = $(window).height() / 3 * 2 + 'px';
        var max_menu = $(window).height() / 3 * 2 - 115 + 'px';

        // 提现成功后的提示
        //  $.toast("提现成功");
    </script>
    <script>
        var game_name=$("input[name='game_name']").val();
        var type=$("input[name='type']").val();

        $(document).on("click", "#server", function () {
            if ($('select[name="server"] option:selected').text() === '') {
                $.toast("请选择区", "cancel");
            }
        });

        var mobileSelect1 = new MobileSelect({
            trigger: '#region',
            title: '区',
            wheels: [{
                data: ['请选择']
            }],
            position: [0, 0],
            transitionEnd: function (indexArr, data) {
                //console.log(data);
            },
            callback: function (indexArr, data) {
                $('select[name="region"] option:selected').text(data).val(data);
                var region = $("select[name='region']").val();
                console.log(data);
                    $.post("{{ route('mobile.leveling.servers') }}",{region:region, game_name:game_name},function(res) {
                        if(res.status == 1) {
                            console.log(res.message);
                            mobileSelect2.updateWheel(0,res.message) //更改第1个轮子
                        }
                    })
            }
        });
        $.post("{{ route('mobile.leveling.regions') }}",{game_name:game_name, type:type},function(res) {
            if(res.status == 1) {
                console.log();
               mobileSelect1.updateWheel(0,res.message) //更改第1个轮子
            }

        })

        var mobileSelect2 = new MobileSelect({
            trigger: '#server',
            title: '服',
            wheels: [{
                data: ['请选择']
            }],
            position: [0, 0],
            transitionEnd: function (indexArr, data) {
                //console.log(data);
            },
            callback: function (indexArr, data) {
                $('select[name="server"] option:selected').text(data).val(data);
                var server = $("select[name='server']").val();
                console.log(data);
            }
        });
    </script>
</body>

</html>