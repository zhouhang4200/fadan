<template>
    <div class="order-list">
        <van-nav-bar
                title="订单列表"
                left-text="返回"
                left-arrow
        />

        <van-tabs color="#198cff" @click="onClick">
            <van-tab title="全部"></van-tab>
            <van-tab title="进行中"></van-tab>
            <van-tab title="待收货"></van-tab>
            <van-tab title="完成"></van-tab>
            <van-tab title="退款中"></van-tab>
            <van-tab title="已退款"></van-tab>
        </van-tabs>
        <van-list
                v-model="loading"
                :finished="finished"
                @load="onLoad"
        >
            <van-panel
                    icon="van-icon van-icon-clock"
                    title="标题"
                    desc="描述信息"
                    status="状态"
                    v-for="item in list"
                    :key="item.id"
                    :title="item.title"
            >
                <div slot="header" class="van-cell van-panel__header">
                    <img src="/channel/images/time.png" class="game-icon">
                    <div class="van-cell__title"><span>等级：{{item.demand}}</span>
                        <div class="van-cell__label">{{item.created_at}}</div>
                    </div>
                    <div class="van-cell__value"><span>{{status[item.status]}}</span></div>
                </div>

                <div style="padding: 15px 30px;background: #f6f7f8">
                    <div class="">

                    </div>
                    <div class="">

                    </div>
                </div>
                <div style="padding: 15px 30px;background: #f6f7f8">
                    <div class="">
                        游戏区服：{{item.game_name}}/{{item.game_region_name}}/{{item.game_server_name}}
                    </div>
                    <div class="">
                        角色名称：{{item.game_role}}
                    </div>
                </div>
                <div style="padding:5px  15px;text-align: right;font-size: 12px">
                    代练价格：￥{{ item.payment_amount }}
                </div>
                <div slot="footer" style="text-align: right">
                    <van-button v-if="item.status === 3" size="small" @click="complete(item)">确认收货</van-button>
                    <van-button v-if="item.status === 2" size="small" @click="applyRefund(item)">申请退款</van-button>
                    <van-button v-if="item.status === 6" size="small" @click="cancelRefund(item)">取消退款</van-button>
                </div>
            </van-panel>
        </van-list>

    </div>
</template>

<script>
    export default {
        name: "Order",

        mixins: [],

        components: {},

        props: {},

        data() {
            return {
                status:{
                    1:'代付款',
                    2:'进行中',
                    3:'待收货',
                    4:'完成',
                    6:'退款中',
                    7:'已退款',
                },
                searchParams:{
                    status:''
                },
                list: [],
                loading: false,
                finished: false
            };
        },

        computed: {},

        watch: {},

        created() {
            this.handleOrderList();
        },

        mounted() {
        },

        destroyed() {
        },

        methods: {
            // 完成
            complete(item){
                this.$api.GameLevelingChannelOrderComplete({user_id:item.user_id, trade_no:item.trade_no, game_leveling_channel_user_id:item.game_leveling_channel_user_id}).then(res => {
                    this.$message({
                        showClose: true,
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 申请退款
            applyRefund(item){

            },
            // 取消退款
            cancelRefund(item){
                console.log(222);
                this.$api.GameLevelingChannelOrderCancelRefund({user_id:item.user_id, trade_no:item.trade_no, game_leveling_channel_user_id:item.game_leveling_channel_user_id}).then(res => {
                   console.log(res);
                }).catch(err => {

                });
            },
            // 点击获取各状态数据
            onClick(index, title) {
                console.log(index)
                if (index === 0) {
                    this.handleOrderList();
                } else if(index === 1) {
                    this.searchParams.status = 2;
                    this.handleOrderList();
                } else if (index === 2) {
                    this.searchParams.status = 3;
                    this.handleOrderList();
                } else if (index === 3) {
                    this.searchParams.status = 4;
                    this.handleOrderList();
                } else if (index === 4) {
                    this.searchParams.status = 6;
                    this.handleOrderList();
                } else if (index  === 5) {
                    this.searchParams.status = 7;
                    this.handleOrderList();
                }
            },
            // 列表数据
            handleOrderList () {
                this.$api.GameLevelingChannelOrderList(this.searchParams).then(res => {
                    this.list = res;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
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
    }
</style>