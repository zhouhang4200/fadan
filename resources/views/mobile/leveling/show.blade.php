@extends('mobile.layouts.app')

@section('title')
    订单详情
@endsection
@section('css')
    <link rel="stylesheet" href="/mobile/lib/css/function.css">
    <link rel="stylesheet" href="/mobile/css/order-info.css">
@endsection

@section('header')
    <div class="header">
        <div class="weui-flex">
            <div class="weui-flex__item">订单详情</div>
            <a href="{{ route('mobile.leveling.demand') }}" class="back iconfont icon-back"></a>
        </div>
    </div>
@endsection
@section('content')
        <div class="order-info">
            <div class="padding-20-30">
                <form>
                    <input type="hidden" name="original_price" value="{{ $mobileOrder->original_price }}">
                    <input type="hidden" name="security_deposit" value="{{ $mobileOrder->security_deposit }}">
                    <input type="hidden" name="efficiency_deposit" value="{{ $mobileOrder->efficiency_deposit }}">
                <div class="order-info-title">
                    <h1>订单信息</h1>
                    <p class="price" id="price"></p>
                    <p style="text-align: center;color: #595959;height: 30px;line-height: 20px;">
                        @if($mobileOrder->status == 0)
                            未付款
                        @else
                            {{ config('order.status_leveling')[$mobileOrder->status] }}
                        @endif
                    </p>
                </div>
                <div class="order-info-item">
                    <div class="weui-flex">
                        <div class="title">
                            <div class="content">订单编号</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->no }}</div>
                        </div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">
                            <div class="content">下单时间</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->created_at }}</div>
                        </div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">
                            <div class="content">完成时间</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $orderDetail['checkout_time'] ?: '暂无' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="order-info">
            <div class="padding-20-30">
                <div class="order-info-title" style="border: 0;">
                    <h1>账号信息</h1>
                </div>
                <div class="order-info-item">
                    <div class="weui-flex color_52">
                        <div class="title">
                            <div class="content">游戏区服</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->game_name }}/{{ $mobileOrder->region }}/{{ $mobileOrder->server }}</div>
                        </div>
                    </div>
                    <div class="weui-flex  color_52">
                        <div class="title">
                            <div class="content">账号</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->account }}</div>
                        </div>
                    </div>
                    <div class="weui-flex  color_52">
                        <div class="title">
                            <div class="content">密码</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->password }}</div>
                        </div>
                    </div>
                    <div class="weui-flex  color_52">
                        <div class="title">
                            <div class="content">角色名称</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->role }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="order-info">
            <div class="padding-20-30">
                <div class="order-info-title" style="border: 0;">
                    <h1>代练信息</h1>
                </div>
                <div class="order-info-item">
                    <div class="weui-flex color_52">
                        <div class="title">
                            <div class="content">代练目标</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->demand }}</div>
                        </div>
                    </div>
                    <div class="weui-flex  color_52">
                        <div class="title">
                            <div class="content">代练类型</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->game_leveling_type }}</div>
                        </div>
                    </div>
                    <div class="weui-flex  color_52">
                        <div class="title">
                            <div class="content">代练价格</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content" id="original_price"></div>
                        </div>
                    </div>
                    <div class="weui-flex  color_52">
                        <div class="title">
                            <div class="content">预计耗时</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">@if($mobileOrder->game_leveling_day)
                                {{ $mobileOrder->game_leveling_day }}天
                                @endif
                                @if($mobileOrder->game_leveling_hour)
                                {{ $mobileOrder->game_leveling_hour }}小时
                                @endif </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="order-info">
            <div class="padding-20-30">
                <div class="order-info-title" style="border-bottom: none;">
                    <h1>联系人信息</h1>
                </div>
                <div class="order-info-item">
                    <div class="weui-flex  color_52">
                        <div class="title">
                            <div class="content">玩家电话</div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="content">{{ $mobileOrder->user_qq }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script>
        $(function () {
            FastClick.attach(document.body);
        });
        var original_price = $("input[name='original_price']").val();
        var efficiency_deposit = $("input[name='efficiency_deposit']").val();
        var security_deposit = $("input[name='security_deposit']").val();
        $("#original_price").html(formatterPrecision2(original_price)+'元');
        $("#price").html(formatterPrecision2(original_price)+'元');
        function formatterPrecision2(value) {
            var number = Number(value);
            if (isNaN(number) || number == 0) {
                return '';
            } else {
                return number.toFixed(2);
            }
        }

    </script>
    <script src="/mobile/lib/js/layer.js"></script>
@endsection