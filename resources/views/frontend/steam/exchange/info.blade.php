<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/frontend/exchange/js/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/exchange/css/common.css">
    <link rel="stylesheet" href="/frontend/exchange/less/phone.css">
    <title>CDKey信息</title>
</head>

<body>
<div class="title">
    <img src="/frontend/exchange/images/phone/title_03.png" alt="">
</div>
<div class="cdkey-info">

    <div class="game-info">
                <span class="name">
                        <h3>商品名称：</h3>
                        <p>{{$cdkeyLibrary->cdkey->goodses->name ?? ''}}</p>
                 </span>
        <span class="money">
                    <h3>商品面值：</h3>
                    <p>{{$cdkeyLibrary->cdkey->goodses->price ?? ''}}元</p>
                </span>
        <span class="state">
                    <h3>卡状态：</h3>
                        <p>{{config('frontend.cdkeyLibraries_status')[$cdkeyLibrary->status] ?? ''}}</p>
                     </span>

        <span class="state">
                    <h3>订单状态：</h3>
                {{--@if(isset($orderError->status) && $orderError->status == 3)
                    @if(isset($order->status) && $order->status == 1)
                        <p>{{config('backend.status')[$order->status ?? ''] ?? ''}}</p>
                    @else
                        <p>{{config('backend.status')[$orderError->status ?? ''] ?? ''}}</p>
                    @endif
                @else--}}
                    <p>{{config('backend.status')[$order->status ?? ''] ?? ''}}</p>
                {{--@endif--}}
        </span>



        <span class="state">
             <h3>失败原因：</h3>
             <p>{{$order->message ?? ''}}</p>
        </span>

    </div>

    @if($cdkeyLibrary->status == 1)
        <a href="{{url('exchange/login?cdk='.$cdkeyLibrary->cdk)}}" class="sub">确认兑换</a>
    @endif
</div>


<script src="/js/jquery-1.11.0.min.js"></script>
<script src="/frontend/exchange/js/layui/layui.js"></script>
<script>
    if (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.split(";")[1].replace(/[ ]/g, "") ==
        "MSIE8.0") {
        $(".layui-input").css({"padding": "0", "height": "40px", "text-indent": "10px", "color": "#000"})
    }
    if (document.all && window.XMLHttpRequest && !document.querySelector) {
        $(".layui-input").css({"padding": "0", "height": "40px", "text-indent": "10px", "color": "#000"})
    }
</script>
</body>

</html>