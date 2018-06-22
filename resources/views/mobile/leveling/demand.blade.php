<!DOCTYPE html>
<html>
<head>
    <title>我要发单</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="_token" content="{{ csrf_token() }}" >
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
                    <img src="/mobile/lib/images/banner.jpg" />
                </div>
                <div class="swiper-slide">
                    <img src="/mobile/lib/images/game_1.jpg" />
                </div>
                <div class="swiper-slide">
                    <img src="/mobile/lib/images/game_2.jpg" />
                </div>
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
        </div>
        <form action="{{ route('mobile.leveling.place-order') }}" method="GET">
            {{ csrf_field() }}
            <input type="hidden" name="startLevel" value=""></input>
            <input type="hidden" name="endLevel" value=""></input>
            <input type="hidden" name="startNumber" value=""></input>
            <input type="hidden" name="endNumber" value=""></input>
            <input type="hidden" name="time" value=""></input>
            <input type="hidden" name="price" value=""></input>
            <input type="hidden" name="payment" value=""></input>
            <input type="hidden" name="showPrice" value=""></input>
            <input type="hidden" name="showTime" value=""></input>

            <div class="weui-cells weui-cells_form ">
                <div class="weui-cell weui-cell_select">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            游戏
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <div class="trigger5 operation operation1" style="cursor: pointer;" id="trigger1">选择游戏</div>
                        <select class="weui-select f-dn" name="game_name">
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
                        <div class="trigger5 operation operation2" style="cursor: pointer;" id="trigger2">必选</div>
                        <select class="weui-select f-dn" name="game_leveling_type">
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
                        <div class="operation operation3" style="cursor: pointer;" id="trigger3">必选</div>
                        <select class="weui-select f-dn" name="game_leveling_level">
                            <option selected="" value=""></option>
                        </select>
                    </div>
                </div>
                <div class="pic_box">
                    <div class="pic">
                        <img src="/mobile/lib/images/pic.png" alt="">
                        <div class="new_pic"></div>
                        <div class="old_pic">代练价格
                            <s></s>
                        </div>
                    </div>
                    <div class="time">
                        <img src="/mobile/lib/images/time.png" alt="">
                        <div class="time_"></div>
                        <div class="old_pic">预计耗时</div>
                    </div>
                </div>
                <button type="submit" disabled class="weui-btn weui-btn_default" id="showTooltips">我要代练</button>
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
        var mobileSelect1 = new MobileSelect({
            trigger: '#trigger1',
            title: '选择游戏',
            wheels: [{
                data: ['请选择游戏']
            }],
            position: [0, 0],
            transitionEnd: function (indexArr, data) {
                //console.log(data);
            },
            callback: function (indexArr, data) {
                $('select[name="game_name"] option:selected').text(data).val(data);
                var game_name = $("select[name='game_name']").val();
                $.post("{{ route('mobile.leveling.types') }}",{game_name:game_name},function(res) {
                    if(res.status == 1) {
                        mobileSelect2.updateWheel(0,res.message) //更改第2个轮子
                    }
                })
            }
        });

        $.post("{{ route('mobile.leveling.games') }}",{},function(res) {
            if(res.status == 1) {
               mobileSelect1.updateWheel(0,res.message) //更改第1个轮子
            }

        })
          
        // select2
        var mobileSelect2 = new MobileSelect({
            trigger: '#trigger2',
            title: '代练类型',
            wheels: [{
                data: ['请选择']
            }],
            position: [0, 0],
            transitionEnd: function (indexArr, data) {
                //console.log(data);
            },
            callback: function (indexArr, data) {
                $('select[name="game_leveling_type"] option:selected').text(data).val(data);
                var game_name=$("select[name='game_name']").val();
                var type=$("select[name='game_leveling_type']").val();
                $.post("{{ route('mobile.leveling.targets') }}", {game_name:game_name, type:type}, function(res) {
                    if(res.status == 1) {
                        var arr = []
                        for (var i in res.message) {
                            arr.push(res.message[i]); 
                        }
                        mobileSelect3.updateWheel(0,arr) //更改第3个轮子
                        mobileSelect3.updateWheel(1,arr)
                    }
                });
            }
        });
        // select3
        var mobileSelect3 = new MobileSelect({
            trigger: '#trigger3',
            title: '代练目标',
            wheels: [{
                data: ['请选择目标']
            },{
                data:['请选择目标']
            }],
            position: [0, 0],
            transitionEnd: function (indexArr, data) {
                //console.log(data);
            },
            callback: function (indexArr, data) {
                $('select[name="game_leveling_level"] option:selected').text( data).val( data);
                $('#showTooltips').addClass('tb-bg').attr('disabled',false)
                var game_name=$("select[name='game_name']").val();
                var type=$("select[name='game_leveling_type']").val();
                var level=$("select[name='game_leveling_level']").val();
                $.post("{{ route('mobile.leveling.compute') }}", {game_name:game_name,type:type,level:level}, function(res) {
                    if(res.status == 1) {
                        $("input[name='price']").val(res.message.price);
                        $("input[name='payment']").val(res.message.payment);
                        $("input[name='time']").val(res.message.time);

                        $("input[name='startNumber']").val(res.message.startNumber);
                        $("input[name='endNumber']").val(res.message.endNumber);
                        $("input[name='startLevel']").val(res.message.startLevel);
                        $("input[name='endLevel']").val(res.message.endLevel);
                        $("input[name='showPrice']").val(res.message.showPrice);
                        $("input[name='showTime']").val(res.message.showTime);

                        $(".time_").html(res.message.showTime);
                        $(".new_pic").html(res.message.payment);
                        $(".old_pic s").html(res.message.showPrice);
                    }
                });
            }
        });

        //上级select未选择的toast 
        $(document).on("click", ".operation2", function () {
            if ($('select[name="game_name"] option:selected').text() === '') {
                $.toast("请先选择代练游戏", "cancel");
                $('.mobileSelect').removeClass('mobileSelect-show');
            }
        })
        //上级select未选择的toast
        $(document).on("click", ".operation3", function () {
            if ($('select[name="game_leveling_type"] option:selected').text() === '') {
                $.toast("请先选择代练类型", "cancel");
                $('.mobileSelect').removeClass('mobileSelect-show');
            }
        })

        // 跳转
        function go() {
            var gameName=$("select[name='game_name']").val();
            var type=$("select[name='game_leveling_type']").val();
            var price=$("input[name='price']").val();
            var payment=$("input[name='payment']").val();
            var time=$("input[name='time']").val();
            var startNumber=$("input[name='startNumber']").val();
            var endNumber=$("input[name='endNumber']").val();
            var startLevel=$("input[name='startLevel']").val();
            var endLevel=$("input[name='endLevel']").val();

            $.post("{{ route('mobile.leveling.go') }}", {gameName:gameName, type:type, startLevel:startLevel, endLevel:endLevel, startNumber:startNumber, endNumber:endNumber, endLevel:endLevel, price:price, payment:payment, time:time}, function (res) {
                if (res.status != 1) {
                    // 弹出错误
                } else {

                }

            });

        }
    </script> 
</body>

</html>