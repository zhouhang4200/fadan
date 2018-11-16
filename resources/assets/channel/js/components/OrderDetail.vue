<template>
    <div class="order-list">

        <div style="margin-top: 46px">
            <section class="van-doc-demo-block">
                <h2 class="van-doc-demo-block__title">
                    <van-icon
                            name="pending-orders"
                            style="font-size: 18px;vertical-align: middle;"
                    />
                    订单信息
                </h2>
                <van-cell-group>
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                </van-cell-group>
            </section>

            <section class="van-doc-demo-block">
                <h2 class="van-doc-demo-block__title">
                    <van-icon
                            name="contact"
                            style="font-size: 18px;vertical-align: bottom;"
                    />
                    账号信息
                </h2>
                <van-cell-group>
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                </van-cell-group>
            </section>

            <section class="van-doc-demo-block">
                <h2 class="van-doc-demo-block__title">
                    <van-icon
                            name="completed"
                            style="font-size: 18px;vertical-align: middle;"
                    />
                    代练信息
                </h2>
                <van-cell-group>
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                    <van-cell value="内容" />
                </van-cell-group>
            </section>
        </div>

        <van-goods-action >

            <van-goods-action-big-btn icon="">
                <van-icon
                        name="chat"
                        style="font-size: 20px;vertical-align: middle;"
                />
                联系客服
            </van-goods-action-big-btn>

            <van-goods-action-big-btn
                    primary
                    :to="{name:'orderRefund', param:{trade_no:$route.params.trade_no}}"
            >
                申请退款
            </van-goods-action-big-btn>

        </van-goods-action>

    </div>
</template>

<script>
    export default {
        name: "OrderDetail",

        data() {
            return {
                list: [],
                loading: false,
                finished: false
            };
        },

        methods: {
            onClickLeft() {
                this.$router.push({path: '/channel/order'})
            },
            onLoad() {
                // 异步更新数据
                setTimeout(() => {
                    for (let i = 0; i < 10; i++) {
                        this.list.push(this.list.length + 1);
                    }
                    // 加载状态结束
                    this.loading = false;

                    // 数据全部加载完成
                    if (this.list.length >= 40) {
                        this.finished = true;
                    }
                }, 500);
            }
        },

    }
</script>

<style lang="less">
    .order-list {
        .game-icon {
            width: 30px;
            height: 30px;
            line-height: 33px;
            margin-top: 5px;
            margin-right: 10px;
        }
    }
</style>