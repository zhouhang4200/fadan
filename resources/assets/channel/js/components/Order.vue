<template>
    <div class="order">
        <van-nav-bar
                :fixed=true
                title="丸子代练"
                left-text="返回"
                left-arrow
                @click-left="onClickLeft"
        />
        <div style="margin-top: 46px">
            <img class="order-poster" src="/channel/images/banner.jpg">
            <van-cell-group class="goods-cell-group">
                <van-cell title="游戏" is-link @click="onClickGame" />
                <van-cell title="代练类型" is-link @click="onClickGame" />
                <van-cell title="代练目示" is-link @click="onClickGame" />
            </van-cell-group>

            <div class="pic-box" style="margin: 30px 30px">
                <van-row >
                    <van-col span="12">
                        <div class="pic" style="height: 60px">
                            <img src="/mobile/lib/images/pic.png">
                            <div class="new-pic">待评估</div>
                            <div class="old-pic a">代练价格
                            </div>
                        </div>
                    </van-col>
                    <van-col span="12">
                        <div class="time">
                            <img src="/mobile/lib/images/time.png" alt="">
                            <div class="time">待评估</div>
                            <div class="old-pic">预计耗时</div>
                        </div>
                    </van-col>
                </van-row>
            </div>

            <div style="margin: 30px">
                <van-button size="normal" type="primary" style="width: 100%">我要代练</van-button>
            </div>
        </div>

        <van-popup
                v-model="gameShow"
                position="bottom">
            <van-picker
                    show-toolbar
                    title="请选择游戏"
                    :columns="games"
                    @change="onChange" />
        </van-popup>
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
                gameShow:false,
                games: [],
            };
        },

        computed: {},

        watch: {},

        created() {},

        mounted() {
            this.getGames();
        },

        destroyed() {},

        methods: {
            getGames() {
                this.$api.getGames().then(res => {
                   this.games = res.content;
                });
            },
            onClickGame() {
                this.gameShow = true;
            },
            onChange(picker, value, index) {
                this.$toast(`当前值：${value}, 当前索引：${index}`);
                this.gameShow = false;
            }
        }
    }
</script>

<style lang="less">
    .order {
        &-poster {
            width: 100%;
            display: block;
        }
        .pic-box{
            padding: 20px 5%;
            box-sizing: border-box;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 24px rgba(0, 0, 0, 0.18);
            .pic{
                flex: 1;
                margin-right: 10px;
                position: relative;
                .title{
                    height: 30px;
                    text-align: center;
                    font-size: 16px;
                    font-weight: 600;
                    color: #484848;
                }
                img{
                    width: 45px;
                    height: 45px;
                    vertical-align: middle;
                    position: absolute;
                    left: 0;
                    top: 10px;
                }
                .new-pic{
                    width: 80px;
                    font-size: 20px;
                    color: #3ec369;
                    position: absolute;
                    left: 55px;
                    top: 10px;
                }
                .old-pic{
                    position: absolute;
                    left: 55px;
                    top: 35px;
                    font-size: 14px;
                    color: #606060;
                    white-space: nowrap;
                    s{
                        text-decoration: line-through;
                    }
                }
            }
            .time{
                flex: 1;
                position: relative;
                .title{
                    height: 30px;
                    text-align: center;
                    font-size: 16px;
                    font-weight: 600;
                    color: #484848;
                }
                img{
                    width: 45px;
                    height: 45px;
                    vertical-align: middle;
                    position: absolute;
                    left: 0;
                    top: 10px;
                }
                .time{
                    width: 100px;
                    font-size: 20px;
                    color: #3ec369;
                    position: absolute;
                    left: 55px;
                    top: 10px;
                    white-space: nowrap;
                }
                .old-pic{
                    position: absolute;
                    left: 55px;
                    top: 35px;
                    font-size: 14px;
                    color: #606060;
                    white-space: nowrap;
                    s{
                        text-decoration: line-through;
                    }
                }
            }
        }
    }
</style>