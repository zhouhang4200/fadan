<template>
    <div class="order-pay-success">

        <van-nav-bar
                :fixed=true
                title="支付成功"
                left-text="返回"
                left-arrow
                @click-left="onClickLeft"
        />

        <div style="margin:0 auto;padding: 50% 0;text-align: center;">
            <van-icon
                    name="checked"
                    style="font-size: 80px;color:#4b0"
            />
            <h3>支付成功</h3>
            <p>正在给您安排代练员，完成后订单会变成交易成功哦！</p>
        </div>

        <van-goods-action >
            <van-goods-action-big-btn
                    :to="{name:'orderDetail', param:{trade_no:$route.params.trade_no}}"
            >
                订单详情
            </van-goods-action-big-btn>

            <van-goods-action-big-btn
                    primary
                    :to="{name:'order'}"
            >
                关闭
            </van-goods-action-big-btn>

        </van-goods-action>

    </div>
</template>

<script>
    export default {
        name: "OrderPaySuccess",
        methods: {
            onClickLeft() {
                this.$router.push({path: '/channel/order'})
            },
        },
    }
</script>