@extends('mobile.layouts.app')

@section('title')
    我要代练
@endsection
@section('css')
    <link rel="stylesheet" href="/mobile/lib/css/mobileSelect.css">
    <link rel="stylesheet" href="/mobile/css/withdrawls.css">
</head>
@endsection
@section('header')
    <div class="header">
        <div class="weui-flex">
            <div class="weui-flex__item">填写代练订单</div>
            <a href="{{ route('mobile.leveling.demand') }}" class="back iconfont icon-back"></a>
        </div>
    </div>
@endsection
@section('content')
        <form action="{{ route('mobile.leveling.pay') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="game_name" value="{{ $gameName }}"></input>
            <input type="hidden" name="game_leveling_type" value="{{ $type }}"></input>
            <input type="hidden" name="channel" value="1"></input>
            <input type="hidden" name="payment" value="{{ $payment }}"></input>
            <input type="hidden" name="price" value="{{ $price }}"></input>
            <input type="hidden" name="time" value="{{ $time }}"></input>
            <input type="hidden" name="startLevel" value="{{ $startLevel }}"></input>
            <input type="hidden" name="endLevel" value="{{ $endLevel }}"></input>
            <input type="hidden" name="security_deposit" value="{{ $securityDeposit }}"></input>
            <input type="hidden" name="efficiency_deposit" value="{{ $efficiencyDeposit }}"></input>
            <input type="hidden" name="game_leveling_day" value="{{ $day }}"></input>
            <input type="hidden" name="game_leveling_hour" value="{{ $hour }}"></input>
            <input type="hidden" name="show_price" value="{{ $showPrice }}"></input>
            <input type="hidden" name="demand" value="{{ $startLevel }}-{{ $endLevel }}"></input>
            <div class="weui-cells weui-cells_form  weui-cells_radio">
                <div class="pic_box" style="height: 130px;">
                    <div class="pic">
                        <div class="title">英雄联盟-排位</div>
                        <img src="/mobile/lib/images/pic.png" alt="" style="top: 35px;">
                        <div class="new_pic" style="top: 35px;">{{ $payment }}元</div>
                        <div class="old_pic" style="top: 60px;">原价
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
                        <select class="weui-select f-dn" name="server" >
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
                        <input class="weui-input" type="text" name="role" placeholder="请输入" required>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            账号
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" name="account" placeholder="请输入" required>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            密码
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" name="password" placeholder="请输入" required>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label class="weui-label">
                            玩家电话
                        </label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="number" pattern="[0-9]*" name="client_phone" placeholder="请输入" required>
                    </div>
                </div>
                <p style="text-indent: 20px;margin-top:20px;background-color: #fff;height: 30px;line-height: 30px;border-bottom: 1px solid #eee;">支付方式</p>
                <label class="weui-cell weui-check__label" style="border-bottom: 1px solid #eee;">
                    <div class="weui-cell__bd">
                        <i class="iconfont icon-zhifubaozhifu"></i>
                        <p style="width: 70%;height: 45px;line-height: 45px;text-indent: 10px;">支付宝</p>
                    </div>
                    <div class="weui-cell__hd">
                        <input type="radio" class="weui-check" name="pay_type" value="1" id="alipay" checked="checked">
                        <i class="weui-icon-checked"></i>
                    </div>
                </label>
                <label class="weui-cell weui-check__label" style="border-bottom: 1px solid #eee;" >
                    <div class="weui-cell__bd">
                        <i class="iconfont icon-weixin1"></i>
                        <p style="width: 70%;height: 45px;line-height: 45px;text-indent: 10px;">微信</p>
                    </div>
                    <div class="weui-cell__hd">
                        <input type="radio" class="weui-check" name="pay_type" value="2" id="wechat">
                        <i class="weui-icon-checked"></i>
                    </div>
                </label>
                <button type="submit" disabled class="weui-btn weui-btn_default" id="showTooltips">我要代练</a>
            </div>
        </form>
@endsection
@section('js')
    <script src="/mobile/lib/js/mobileSelect.js"></script>
    <script>
        $(function () {
            FastClick.attach(document.body);
        });
    </script>

    <script>
        $.toast.prototype.defaults.duration = 800;
        var game_name=$("input[name='game_name']").val();
        var type=$("input[name='type']").val();

        var newPrice = $("input[name='payment']").val();
        var oldPrice = $("input[name='show_price']").val();
        $(".new_pic").html(formatterPrecision2(newPrice)+'元');
        $(".old_pic s").html(formatterPrecision2(oldPrice)+'元');

        var session = "{{ session('miss') }}";
        if (session) {
            $.toast(session, 'text');
        }
        $('input[name="role"]').click(function(){
            if ( $('select[name="region"] option:selected').val() == '' || $('select[name="server"] option:selected').val() == '' ) {
                $.toast("请先选择区服信息",'text');
            }
        })
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
                
                    $.post("{{ route('mobile.leveling.servers') }}",{region:region, game_name:game_name},function(res) {
                        if(res.status == 1) {
                            mobileSelect2.updateWheel(0,res.message) //更改第1个轮子
                        }
                    })
            }
        });
        
        $.post("{{ route('mobile.leveling.regions') }}",{game_name:game_name, type:type},function(res) {
            if(res.status == 1) {
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
                $('#showTooltips').addClass('tb-bg').attr('disabled',false)
            }
        });

        function formatterPrecision2(value) {
            var number = Number(value);
            if (isNaN(number) || number == 0) {
                return '';
            } else {
                return number.toFixed(2);
            }
        }
        
        $(document).on("click", ".operation2", function () {
            if ($('select[name="region"] option:selected').text() === '') {

                $.toast("请选择大区", "text");
                $('.mobileSelect').removeClass('mobileSelect-show');
            }
        })
    </script>
@endsection