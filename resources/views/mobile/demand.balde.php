<!DOCTYPE html>
<html>
<head>
    <title>我要发单</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.">
    <link rel="stylesheet" href="mobile/lib/css/weui.min.css">
    <link rel="stylesheet" href="mobile/lib/css/jquery-weui.css">
    <link rel="stylesheet" href="mobile/lib/css/font.css">
    <link rel="stylesheet" href="mobile/lib/css/reset.css">
    <link rel="stylesheet" href="mobile/lib/css/common.css">
    <link rel="stylesheet" href="mobile/lib/css/mobileSelect.css">
    <link rel="stylesheet" href="mobile/css/withdrawls.css   ">
</head>

<body ontouchstart>
    <!-- header -->
    <div class="header">
        <div class="weui-flex">
            <div class="weui-flex__item">游戏代练</div>
            <a href="./index.html" class="back iconfont icon-back"></a>
        </div>
    </div>
    <!-- header -->
    <!-- main -->
    <div class="main">
        <div class="swiper-container">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <!-- Slides -->
                <div class="swiper-slide">
                    <img src="mobile/lib/images/banner.jpg" />
                </div>
                <div class="swiper-slide">
                    <img src="mobile/lib/images/game_1.jpg" />
                </div>
                <div class="swiper-slide">
                    <img src="mobile/lib/images/game_2.jpg" />
                </div>
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
        </div>
        <form action="">
            <div class="weui-cells weui-cells_form ">
                <div class="weui-cell weui-cell_select">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            游戏
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <div class="trigger5 operation operation1">选择游戏</div>
                        <select class="weui-select f-dn" name="game">
                            <option selected="" value=""></option>
                        </select>
                    </div>
                </div>
                <div class="weui-cell weui-cell_select">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            代练类型
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <div class="trigger5 operation operation2">必选</div>
                        <select class="weui-select f-dn" name="qu">
                            <option selected="" value=""></option>
                        </select>
                    </div>
                </div>
                <div class="weui-cell weui-cell_select">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            代练目标
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <div class="operation operation3" style="cursor: pointer;" id="trigger5">必选</div>
                        <select class="weui-select f-dn" name="dailian">
                                <option selected="" value=""></option>
                            </select>
                    </div>
                </div>
                <div class="pic_box">
                    <div class="pic">
                        <img src="mobile/lib/images/pic.png" alt="">
                        <div class="new_pic">50元</div>
                        <div class="old_pic">代练价格
                            <s>60元</s>
                        </div>
                    </div>
                    <div class="time">
                        <img src="mobile/lib/images/time.png" alt="">
                        <div class="time_">1天12小时</div>
                        <div class="old_pic">预计耗时</div>
                    </div>
                </div>
                <button type="submit" class="weui-btn weui-btn_default" href="javascript:" id="showTooltips">我要代练</a>
            </div>
        </form>
    </div>
    <!-- main -->
    <script src="mobile/lib/js/jquery-2.1.4.js"></script>
    <script src="mobile/lib/js/fastclick.js"></script>
    <script src="mobile/lib/js/mobileSelect.js"></script>

    <script>
        $(function () {
            FastClick.attach(document.body);
        });
    </script>
    <script src="mobile/lib/js/jquery-weui.js"></script>
    <script src="mobile/lib/js/swiper.js"></script>
    <script>
        $(".swiper-container").swiper({
            loop: true,
            autoplay: 3000
        });
    </script>
    <script>
        var max_h = $(window).height() / 3 * 2 + 'px';
        var max_menu = $(window).height() / 3 * 2 - 115 + 'px';
        $(document).on("click", ".operation1", function () {
            $.actions({
                title: "选择游戏",
                onClose: function () {
                    console.log("close");
                },
                actions: [{
                        text: "英雄联盟",
                        onClick: function () {
                            $('.operation1').html('英雄联盟')
                            $('select[name="game"] option:selected').text('英雄联盟').val('英雄联盟')
                        }
                    },
                    {
                        text: "天天飞车",
                        onClick: function () {
                            $('.operation1').html('天天飞车')
                            $('select[name="game"] option:selected').text('天天飞车').val('天天飞车')
                        }
                    },
                    {
                        text: "王者荣耀",
                        onClick: function () {
                            $('.operation1').html('王者荣耀')
                            $('select[name="game"] option:selected').text('王者荣耀').val('王者荣耀')
                        }
                    }
                ]
            }, );

            $('.weui-actionsheet').css({
                'max-height': max_h
            });
            $('.weui-actionsheet__menu').css({
                "max-height": max_menu
            });
        });
        $(document).on("click", ".operation2", function () {
            if ($('select[name="game"] option:selected').text() === '') {
                $.toast("请先选择代练游戏", "cancel");
            } else(
                $.actions({
                    title: "选择区",
                    onClose: function () {
                        console.log("close");
                    },
                    actions: [{
                            text: "IOS QQ",
                            onClick: function () {
                                $('operation2').html('IOS QQ')
                                $('select[name="qu"] option:selected').text('IOS QQ').val(
                                    'IOS QQ')
                            }
                        },
                        {
                            text: "IOS 微信",
                            onClick: function () {
                                $('.operation2').html('IOS 微信')
                                $('select[name="qu"] option:selected').text('IOS 微信').val(
                                    'IOS 微信')
                            }
                        },
                        {
                            text: "安卓 QQ",
                            onClick: function () {
                                $('.operation2').html('安卓 QQ')
                                $('select[name="qu"] option:selected').text('安卓 QQ').val(
                                    '安卓 QQ')
                            }
                        },
                        {
                            text: "安卓 微信",
                            onClick: function () {
                                $('.operation2').html('安卓 微信')
                                $('select[name="qu"] option:selected').text('安卓 微信').val(
                                    '安卓 微信')

                            }
                        }
                    ]
                })
            )

            $('.weui-actionsheet').css({
                'max-height': max_h
            });
            $('.weui-actionsheet__menu').css({
                "max-height": max_menu
            })
        });

        // 提现成功后的提示
        //  $.toast("提现成功");
    </script>
    <script>
        var UplinkData = [
            {
                id: '青铜',
                value: '青铜',
                childs: [{
                        id: '青铜V',
                        value: '青铜V',
                        childs: [{
                                id: '青铜IV',
                                value: '青铜IV'
                            },
                            {
                                id: '青铜III',
                                value: '青铜III'
                            },
                            {
                                id: '青铜II',
                                value: '青铜II'
                            },
                            {
                                id: '青铜I',
                                value: '青铜I'
                            }
                        ]
                    },
                    {
                        id: '青铜IV',
                        value: '青铜IV',
                        childs: [{
                                id: '青铜III',
                                value: '青铜III'
                            },
                            {
                                id: '青铜II',
                                value: '青铜II'
                            },
                            {
                                id: '青铜I',
                                value: '青铜I'
                            }
                        ]
                    },
                    {
                        id: '青铜III',
                        value: '青铜III',
                        childs: [{
                                id: '青铜II',
                                value: '青铜II'
                            },
                            {
                                id: '青铜I',
                                value: '青铜I'
                            }
                        ]
                    },
                    {
                        id: '青铜II',
                        value: '青铜II',
                        childs: [{
                            id: '青铜I',
                            value: '青铜I'
                        }]
                    },
                    {
                        id: '青铜I',
                        value: '青铜I'
                    },

                ]
            },
            {
                id: '白银',
                value: '白银',
                childs: [{
                        id: '白银V',
                        value: '白银V',
                        childs: [{
                                id: '白银IV',
                                value: '白银IV'
                            },
                            {
                                id: '白银III',
                                value: '白银III'
                            },
                            {
                                id: '白银II',
                                value: '白银II'
                            },
                            {
                                id: '白银I',
                                value: '白银I'
                            }
                        ]
                    },
                    {
                        id: '白银IV',
                        value: '白银IV',
                        childs: [{
                                id: '白银III',
                                value: '白银III'
                            },
                            {
                                id: '白银II',
                                value: '白银II'
                            },
                            {
                                id: '白银I',
                                value: '白银I'
                            }
                        ]
                    },
                    {
                        id: '白银III',
                        value: '白银III',
                        childs: [{
                                id: '白银II',
                                value: '白银II'
                            },
                            {
                                id: '白银I',
                                value: '白银I'
                            }
                        ]
                    },
                    {
                        id: '白银II',
                        value: '白银II',
                        childs: [{
                            id: '白银I',
                            value: '白银I'
                        }]
                    },
                    {
                        id: '白银I',
                        value: '白银I'
                    },

                ]
            },
            {
                id: '黄金',
                value: '黄金',
                childs: [{
                        id: '黄金V',
                        value: '黄金V',
                        childs: [{
                                id: '黄金IV',
                                value: '黄金IV'
                            },
                            {
                                id: '黄金III',
                                value: '黄金III'
                            },
                            {
                                id: '黄金II',
                                value: '黄金II'
                            },
                            {
                                id: '黄金I',
                                value: '黄金I'
                            }
                        ]
                    },
                    {
                        id: '黄金IV',
                        value: '黄金IV',
                        childs: [{
                                id: '黄金III',
                                value: '黄金III'
                            },
                            {
                                id: '黄金II',
                                value: '黄金II'
                            },
                            {
                                id: '黄金I',
                                value: '黄金I'
                            }
                        ]
                    },
                    {
                        id: '黄金III',
                        value: '黄金III',
                        childs: [{
                                id: '黄金II',
                                value: '黄金II'
                            },
                            {
                                id: '黄金I',
                                value: '黄金I'
                            }
                        ]
                    },
                    {
                        id: '黄金II',
                        value: '黄金II',
                        childs: [{
                            id: '黄金I',
                            value: '黄金I'
                        }]
                    },
                    {
                        id: '黄金I',
                        value: '黄金I'
                    },

                ]
            },

        ];
        
        $(document).on("click", ".operation3", function () {
            if ($('select[name="qu"] option:selected').text() === '') {
                $.toast("请先选择代练游戏", "cancel");
                $('.mobileSelect').removeClass('mobileSelect-show');
            }else{
              
            }
        })
        var mobileSelect5 = new MobileSelect({
                    trigger: '#trigger5',
                    title: '代练目标',
                    wheels: [{
                        data: UplinkData
                    }],
                    position: [0, 0],
                    transitionEnd: function (indexArr, data) {
                        //console.log(data);
                    },
                    callback: function (indexArr, data) {
                        var templateVal = '';
                        for(var i=0 ; i <data.length;i++){
                            templateVal += data[i].value+' ';
                        }
                        $('select[name="dailian"] option:selected').text( templateVal).val( templateVal);
                        $('#showTooltips').addClass('tb-bg')
                    }
                });
    </script>
</body>

</html>