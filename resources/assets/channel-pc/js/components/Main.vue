<template>

    <div id="warp">

        <div class="header">
            <div class="container">
                <a href="" class="logo">
                    <img src="/channel-pc/images/logo.png" alt="">
                </a>
                <div class="nav">
                    <ul>
                        <li>
                            <router-link :to="{name:'order'}">
                                首页
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{name:'orderList'}">
                                我的订单
                            </router-link>
                        </li>
                        <li>
                            <a href="">
                                手机版本
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="user">
                    <el-button type="primary" round>登录</el-button>
                </div>
            </div>
        </div>

        <div class="main">

            <div class="container">

                <router-view></router-view>

            </div>

        </div>

        <div class="footer">
            <div class="container">

            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: 'Main',
        data() {
            return {

            }
        },
        computed: {
            currentRoute() {
                return function (name) {
                    if (this.$route.name  === name) {
                        return ['current']
                    } else {
                        return ''
                    }
                }

            },
            display() {
                if (this.$route.name != 'orderDetail' && this.$route.name != 'orderRefund' && this.$route.name != 'paySuccess') {
                    return true;
                }
            }
        }
        ,
        methods: {
            test() {
                this.$router.push({
                    name: 'orderRefund',
                    param: {
                        'trade_on': 'sd'
                    }
                })
            }
        }
    }
</script>

<style lang="less">

    .container {
        width: 1000px;
        margin: 0 auto;
    }

    .box-shadow {
        /*border-radius: 12px;*/
        overflow: hidden;
        box-shadow: 0 8px 16px 0 rgba(7, 17, 27, .05);
    }

    .header {
        position: fixed;
        top: 0;
        z-index: 999;
        height: 60px;
        width: 100%;
        line-height: 60px;
        font-size: 12px;
        background: #fff;
        color: #606060;
        box-shadow: 0 8px 16px 0 rgba(28, 31, 33, .1);
        .logo {
            float: left;
            width: 250px;
            height: 60px;

            img {
                vertical-align: middle
            }
        }
        .nav {
            margin-left: 20px;
            float: left;
            font-size: 14px;
            ul {
                li {
                    float: left;
                    a {
                        padding: 0 30px;
                        display: block;
                        color: #222;
                        text-decoration: none;

                    }
                    a:hover {
                        color: #409EFF;
                        border-bottom: 2px solid #409EFF;
                    }
                    .router-link-exact-active {
                        color: #409EFF;
                        border-bottom: 2px solid #409EFF;
                    }
                }
            }
        }

        .user {
            float: right;
        }
    }

    .main {
         margin-top: 75px;
    }

    .footer {
        position: relative;
        padding: 45px 0;
        line-height: 24px;
        color: #b0b0b0;
        background: #434654;
        font-size: 12px;
        box-shadow: 0 2px 5px 0 rgba(29, 8, 8, .12)
    }
</style>