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
                        <!--<li>-->
                            <!--<a href="">-->
                                <!--手机版本-->
                            <!--</a>-->
                        <!--</li>-->
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
            <div class="container" style="padding-top: 35px">
                <div class="fl" style="width: 200px">
                    <div class="">在线客服: </div>
                    <div class="">服务时间: 09:00-23:00</div>
                </div>
                <div class="fl" style="width: 1px;height: 60px; background: #b0b0b0;"></div>
                <div class="fl nav" style="padding-left: 40px">
                    <div class="" style="margin-bottom: 5px;">
                        <ul>
                            <li>关注我们</li>
                            <li>联系我们</li>
                            <li>免责声明隐私政策</li>
                            <li>商务合作</li>
                        </ul>
                        <div class="clear-float"></div>
                    </div>
                    <div style="margin-bottom: 5px;">网站备案:鄂ICP备17017191号　　网络文化经营许可证:鄂网文(2017) 8165-175号</div>
                    <div style="margin-bottom: 5px;">copyrighto 2017湖北氟金网络科技有限公司All Rights Reserved</div>
                </div>
                <div class="clear-float"></div>
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

    .el-form-item.is-success .el-input__inner,
    .el-form-item.is-success .el-input__inner:focus,
    .el-form-item.is-success .el-textarea__inner,
    .el-form-item.is-success .el-textarea__inner:focus{
        border: 1px solid #dcdfe6;
    }
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
        min-height: calc(100vh - 125px);
    }

    .footer {
        height: 125px;
        /*line-height: 24px;*/
        color: #b0b0b0;
        background: #434654;
        font-size: 12px;
        -webkit-box-shadow: 0 2px 5px 0 rgba(29, 8, 8, 0.12);
        box-shadow: 0 2px 5px 0 rgba(29, 8, 8, .12);
        .nav {
            ul li {
                float: left;
                padding-right: 15px;
            }
        }
    }
</style>