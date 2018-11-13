<template>
    <div id="app">
        <router-view/>
        <van-tabbar v-model="active">
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
</style>