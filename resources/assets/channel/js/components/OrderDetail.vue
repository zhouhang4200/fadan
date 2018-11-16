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
                    <van-field
                            :value="order.trade_no"
                            label="订单编号"
                            disabled
                    />
                    <van-field
                            :value="order.created_at"
                            label="下单时间"
                            disabled
                    />
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
                    <van-field
                            :value="order.game_region_name+'/'+order.game_server_name"
                            label="游戏区服"
                            disabled
                    />
                    <van-field
                            :value="order.game_account"
                            label="账号"
                            disabled
                    />
                    <van-field
                            :value="order.game_password"
                            label="密码"
                            disabled
                    />
                    <van-field
                            :value="order.game_role"
                            label="角色名称"
                            disabled
                    />
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
                    <van-field
                            :value="order.title"
                            label="代练目标"
                            disabled
                    />
                    <van-field
                            :value="order.game_leveling_type_name"
                            label="代练类型"
                            disabled
                    />
                    <van-field
                            :value="order.payment_amount"
                            label="代练价格"
                            disabled
                    />
                    <van-field
                            :value="order.day+'天'+order.hour+'小时'"
                            label="预计耗时"
                            disabled
                    />
                </van-cell-group>
            </section>

            <section class="van-doc-demo-block">
                <h2 class="van-doc-demo-block__title">
                    <van-icon
                            name="completed"
                            style="font-size: 18px;vertical-align: middle;"
                    />
                    联系信息
                </h2>
                <van-cell-group>
                    <van-field
                            :value="order.player_phone"
                            label="玩家电话"
                            disabled
                    />
                    <van-field
                            :value="order.player_qq"
                            label="玩家QQ"
                            disabled
                    />
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
                    v-if="order.status == 2"
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
                order: {
                    trade_no:'',
                    created_at:'',
                    game_region_name:'',
                    game_server_name:'',
                    game_account:'',
                    game_password:'',
                    game_role:'',
                    title:'',
                    game_leveling_type_name:'',
                    payment_amount:'',
                    day:'',
                    hour:'',
                    player_phone:'',
                    player_qq:'',
                    status:''
                },
                loading: false,
                finished: false
            };
        },
        created() {
            this.handleOrder();
        },
        methods: {
            // 页面数据
            handleOrder() {
                this.$api.GameLevelingChannelOrderShow({
                    user_id:this.$route.query.user_id,
                    trade_no:this.$route.query.trade_no
                }).then(res => {
                    this.order=res;
                }).catch(err => {

                });
            },
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