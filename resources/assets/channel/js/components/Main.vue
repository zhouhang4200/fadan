<template>
    <div id="app" style="background: #fafafa">
        <router-view/>
        <van-tabbar v-model="active" v-if="display">
            <van-tabbar-item icon="wap-home" :to="{ path: '/channel/order' }">首页</van-tabbar-item>
            <van-tabbar-item icon="records"  :to="{ path: '/channel/order/list' }">订单</van-tabbar-item>
        </van-tabbar>
    </div>
</template>

<script>
    export default {
        name:'Main',
        data() {
            return {
                active: 0,
                icon: {
                    normal: '//img.yzcdn.cn/icon-normal.png',
                    active: '//img.yzcdn.cn/icon-active.png'
                }
            }
        },
        computed: {
            display() {
                if (this.$route.name != 'orderDetail' && this.$route.name != 'orderRefund') {
                    return true;
                }
            }
        },
        methods: {
            onClickLeft() {
                this.$toast('返回');
            },
            onClickRight() {
            }
        }
    }
</script>

<style lang="less">
    body {
        font-size: 12px;
    }
    .van-doc-demo-block__title {
        margin: 0;
        font-weight: 400;
        font-size: 14px;
        color: rgba(69,90,100,.6);
        padding: 10px 15px;
    }
</style>