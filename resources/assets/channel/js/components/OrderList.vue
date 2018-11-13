<template>
    <div class="order-list">
        <van-nav-bar
                title="订单列表"
                left-text="返回"
                left-arrow
                @click-left="onClickLeft"
                @click-right="onClickRight"
        />

        <van-tabs :stick=true color="#198cff">
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
                    :key="item"
                    :title="item"
            >
                <div slot="header" class="van-cell van-panel__header">
                    <img src="/channel/images/time.png" class="game-icon">
                    <div class="van-cell__title"><span>2</span>
                        <div class="van-cell__label">描述信息</div>
                    </div>
                    <div class="van-cell__value"><span>状态</span></div>
                </div>

                <div style="padding: 15px 30px;background: #f6f7f8">
                    <div class="">sdfsdf</div>
                    <div class="">sdfsdf</div>
                </div>
                <div style="padding:5px  15px;text-align: right;font-size: 12px">
                    代练价格：￥{{ item }}
                </div>
                <div slot="footer" style="text-align: right">
                    <van-button size="small">确认收货</van-button>
                    <van-button size="small">申请退款</van-button>
                    <van-button size="small">取消退款</van-button>
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
                list: [],
                loading: false,
                finished: false
            };
        },

        computed: {},

        watch: {},

        created() {
        },

        mounted() {
        },

        destroyed() {
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