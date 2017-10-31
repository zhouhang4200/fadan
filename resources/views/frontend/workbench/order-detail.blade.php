<html>
<header>

</header>
<style>
    html, body {
        background-color: #eee;
        font-size: 14px;
    }
    * {
        margin: 0;
        padding: 0;
    }
    .big{
        -webkit-box-flex: 1;
        overflow: auto;
        overflow-x: hidden;
    }
    .bg-white {
        background-color: white;
    }
    .cm-margin {
        margin-top: 15px;
    }
    .cm-item li {
        border-bottom: 1px solid rgba(0, 0, 0, .1);
    }
    .cm-item li .item-banner {
        width: 20%;
    }
    .cm-padding {
        padding: 10px 15px;
    }
    .cm-border {
        border: 1px solid rgba(0, 0, 0, .1);
        border-radius: 3px;
    }
    li {
        list-style: none;
    }
    .left {
        float: left;
    }
    .overflow {
        overflow: hidden;
    }
</style>
<body>
<div class="big">

    <div class="cm-padding">
        <div class="bg-white cm-border overflow order-in">
            <div class="left cm-padding" style="width: 50%">
                <p>下单时间<br><span>{{ $order->created_at }}</span></p>
            </div>
            <div class="left cm-padding" style="border:none">
                <p>
                    完结时间<br><span></span>
                </p>
            </div>
        </div>

        <ul class="bg-white cm-border cm-item overflow cm-margin">
            <li class="overflow">
                <div class="item-banner left cm-padding">状态</div>
                <div class="item-content left cm-padding"> {{ config('order.status')[$order->status] }}</div>
            </li>
        </ul>

        <ul class="bg-white cm-border cm-item overflow cm-margin">
            <li class="overflow">
                <div class="item-banner left cm-padding">订单</div>
                <div class="item-content left cm-padding">{{ $order->no }}</div>
            </li>
            <li class="overflow">
                <div class="item-banner left cm-padding">类型</div>
                <div class="item-content left cm-padding">{{ $order->service_name }}</div>
            </li>
            <li class="overflow">
                <div class="item-banner left cm-padding">游戏</div>
                <div class="item-content left cm-padding">{{ $order->game_name }}</div>
            </li>
            <li class="overflow">
                <div class="item-banner left cm-padding">商品</div>
                <div class="item-content left cm-padding">{{ $order->goods_name }}</div>
            </li>
            <li class="overflow">
                <div class="item-banner left cm-padding">单价</div>
                <div class="item-content left cm-padding">{{ $order->price }}</div>
            </li>
            <li class="overflow">
                <div class="item-banner left cm-padding">数量</div>
                <div class="item-content left cm-padding">{{ $order->quantity }}</div>
            </li>
            <li class="overflow">
                <div class="item-banner left cm-padding">总价</div>
                <div class="item-content left cm-padding">{{ $order->amount }}</div>
            </li>
        </ul>

        <ul class="bg-white cm-border cm-item overflow cm-margin">
            @forelse($order->detail as $item)
                @if($item->field_name != 'quantity')
                <li class="overflow">
                    <div class="item-banner left cm-padding">{{ $item->field_display_name }}</div>
                    <div class="item-content left cm-padding">{{ $item->field_value }}</div>
                </li>
                @endif
            @empty
            @endforelse

        </ul>
    </div>
</div>
</body>
</html>