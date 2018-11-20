<template>
    <div class="order-list">

        <div style="margin-top: 46px">

            <van-tabs
                    color="#198cff"
                    @click="onClick"
                    sticky
                    :offset-top=0
            >
                <van-tab title="全部" status="1"></van-tab>
                <van-tab title="进行中"></van-tab>
                <van-tab title="待收货"></van-tab>
                <van-tab title="完成"></van-tab>
                <van-tab title="退款中"></van-tab>
                <van-tab title="已退款"></van-tab>
            </van-tabs>


            <van-pull-refresh
                    v-show="! noData"
                    v-model="loading"
                    @refresh="onLoadData">

                <van-panel
                        v-for="item in list"
                        :key="item.id"
                        :title="item.title"
                        style="margin: 10px 10px;border: 1px solid #eee;border-radius: 8px;"
                >
                    <div slot="header" class="van-cell van-panel__header" @click="orderShow(item)">
                        <img src="/channel/images/time.png" class="game-icon">
                        <div class="van-cell__title">
                            <span>等级：{{item.demand}}</span>
                            <div class="van-cell__label">{{item.created_at}}</div>
                        </div>
                        <div class="van-cell__value"><span>{{status[item.status]}}</span></div>
                    </div>
                    <div style="padding: 15px 30px;background: #f6f7f8">
                        <div class="">
                            游戏区服：{{item.game_name ? item.game_name+'/' : ''}}{{item.game_region_name ? item.game_region_name +'/' : ''}}{{item.game_server_name}}
                        </div>
                        <div class="">
                            角色名称：{{item.game_role}}
                        </div>
                    </div>
                    <div style="padding:5px  15px;text-align: right;font-size: 12px">
                        代练价格：￥{{ item.payment_amount }}
                    </div>
                    <div slot="footer" style="text-align: right">
                        <van-button v-if="item.status === 3" type="primary" size="small" @click="complete(item)">确认收货</van-button>
                        <van-button v-if="item.status === 2" type="danger" size="small" @click="applyRefund(item)">申请退款</van-button>
                        <van-button v-if="item.status === 6" type="primary" size="small" @click="cancelRefund(item)">取消退款</van-button>
                    </div>
                </van-panel>

            </van-pull-refresh>

            <div v-show="noData" style="padding-top: 120px;text-align: center;">
                <van-icon  name="cart-o" style="font-size: 90px;color:#e4e4e4;" />
                <p>暂时没有订单</p>
                <router-link :to="{name:'order'}">
                    <van-button  type="primary" size="small" >立刻下单</van-button>
                </router-link>
            </div>

        </div>

    </div>
</template>

<script>
    import { Toast } from 'vant';
    export default {
        name: "Order",
        data() {
            return {
                status:{
                    1:'待付款',
                    2:'进行中',
                    3:'待收货',
                    4:'完成',
                    6:'退款中',
                    7:'已退款',
                },
                searchParams:{
                    status:0
                },
                list: [],
                loading:false,
                noData:false,
            };
        },

        mounted() {
            this.onLoadData();
        },

        methods: {
            // 详情页
            orderShow(item){
                this.$router.push({name:'orderDetail', query:{trade_no:item.trade_no, user_id:item.user_id}})
            },
            // 申请退款按钮
            applyRefund(item){
                this.$router.push({name:'orderRefund', query:{trade_no:item.trade_no, user_id:item.user_id}})
            },
            onClickLeft() {
                this.$router.push({path: '/channel/order'})
            },
            // 完成
            complete(item){
                this.$api.GameLevelingChannelOrderComplete({
                    user_id:item.user_id,
                    trade_no:item.trade_no,
                    game_leveling_channel_user_id:item.game_leveling_channel_user_id
                }).then(res => {
                    if (res.status === 1) {
                        Toast.success(res.message);
                    } else {
                        Toast.fail(res.message);
                    }
                    this.searchParams.status = 3;
                    this.handleOrderList();
                }).catch(err => {

                });
            },
            // 取消退款
            cancelRefund(item){
                this.$api.GameLevelingChannelOrderCancelRefund({
                    user_id:item.user_id,
                    trade_no:item.trade_no,
                    game_leveling_channel_user_id:item.game_leveling_channel_user_id
                }).then(res => {
                    if (res.status === 1) {
                        Toast.success(res.message);
                    } else {
                        Toast.fail(res.message);
                    }
                    this.searchParams.status = 6;
                    this.handleOrderList();
                }).catch(err => {

                });
            },
            // 点击获取各状态数据
            onClick(index, title) {
                if (index === 0) {
                    this.searchParams.status = 0;
                    this.onLoadData();
                } else if(index === 1) {
                    this.searchParams.status = 2;
                    this.onLoadData();
                } else if (index === 2) {
                    this.searchParams.status = 3;
                    this.onLoadData();
                } else if (index === 3) {
                    this.searchParams.status = 4;
                    this.onLoadData();
                } else if (index === 4) {
                    this.searchParams.status = 6;
                    this.onLoadData();
                } else if (index  === 5) {
                    this.searchParams.status = 7;
                    this.onLoadData();
                }
            },
            // 列表数据
            onLoadData () {
                this.$api.GameLevelingChannelOrderList(this.searchParams).then(res => {
                    this.noData = res.length == 0 ? true : false;
                    this.list = res;
                    this.loading = false;
                    this.finished = true;
                }).catch(err => {

                });
            }
        }
    }
</script>

<style lang="less">
    .order-list {
        .game-icon {
            width: 30px; height: 30px;
            line-height: 33px;
            margin-top: 5px;
            margin-right: 10px;
        }
        .van-cell {
            border-radius: 8px;
        }
    }
</style>