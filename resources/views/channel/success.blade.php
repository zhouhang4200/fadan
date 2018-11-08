@extends('mobile.layouts.app')

@section('title')
    支付状态
@endsection
@section('css')
    <link rel="stylesheet" href="/mobile/css/withdrawls.css">
@endsection

@section('header')
    <div class="header">
        <div class="weui-flex">
            <div class="weui-flex__item">付款成功</div>
        </div>
    </div>
@endsection
@section('content')
    <div class="main" style="text-align: center;">
        <img src="/mobile/lib/images/success.png" alt="" style="width: 60px;height: 60px;margin-top: 100px;">
        <p style="text-align: center;font-size: 16px;color: #58b720;margin-top: 10px;font-weight: 600;">付款成功</p>
        <p style="font-size: 14px;color: #939393;margin-top: 12px;">正在给你安排代练员，完成后订单会变成交易成功哦</p>
        <div class="footer">
            <a class="weui-btn weui-btn_default tb-bg" href="{{ route('mobile.leveling.show', ['id' => $mobileOrder->id]) }}" style="margin-right: 1px;">订单详情</a>
            <a class="weui-btn weui-btn_default tb-bg" id="close" href="{{ route('mobile.leveling.demand') }}">关闭</a>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function () {
            FastClick.attach(document.body);
        });
    </script>
    <script src="/mobile/lib/js/swiper.js"></script>
    <script>
        $(".swiper-container").swiper({
            loop: true,
            autoplay: 3000
        });
    </script>
@endsection