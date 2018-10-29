<template>
    <el-container>
        <el-aside v-bind:style="{'width': collapse ? '64px':'200px', 'background-color': '#515a6e'}">
            <div class="logo">
                <i class="icon-tao" style="font-size:32px;color:#fff"></i>
                <img src="/frontend/v2/images/logo.png" style="vertical-align: top" v-show="!collapse">
            </div>
            <el-menu
                    :default-openeds="['1']"
                    :unique-opened=true
                    :collapse-transition="collapseTransition"
                    default-active="1-3"
                    class="side-menu"
                    background-color="#515a6e"
                    :min-height="menuMinHeight"
                    text-color="#fff"
                    active-text-color="#ffd04b"
                    :collapse="collapse">
                <el-submenu index="1">
                    <template slot="title">
                        <i class="el-icon-location"></i>
                        <span slot="title">工作台</span>
                    </template>
                    <a href=""><el-menu-item index="1-1">代练待发</el-menu-item></a>
                    <a href="/v2/order/game-leveling/create"><el-menu-item index="1-2">代练发布</el-menu-item></a>
                    <a href="/v2/order/game-leveling"><el-menu-item index="1-3">代练订单</el-menu-item></a>
                    <a href=""><el-menu-item index="1-4">订单投诉</el-menu-item></a>
                </el-submenu>

                <el-submenu index="2">
                    <template slot="title">
                        <i class="el-icon-location"></i>
                        <span slot="title">财务</span>
                    </template>
                    <a href="/v2/finance/my-asset"><el-menu-item index="2-1">我的资产</el-menu-item></a>
                    <a href="/v2/finance/daily-asset"><el-menu-item index="2-2">资产日报</el-menu-item></a>
                    <a href="/v2/finance/amount-flow"><el-menu-item index="2-3">资金流水</el-menu-item></a>
                    <a href="/v2/finance/my-withdraw"><el-menu-item index="2-4">我的提现</el-menu-item></a>
                    <a href="/v2/statistic/employee"><el-menu-item index="2-6">员工统计</el-menu-item></a>
                    <a href="/v2/statistic/order"><el-menu-item index="2-7">订单统计</el-menu-item></a>
                    <a href="/v2/statistic/message"><el-menu-item index="2-8">短信统计</el-menu-item></a>
                    <a href="/v2/statistic/message"><el-menu-item index="2-9">短信统计</el-menu-item></a>
                </el-submenu>

                <!--没有子菜单示例-->
                <!--<el-menu-item index="4">-->
                    <!--<i class="el-icon-setting"></i>-->
                    <!--<span slot="title">导航四</span>-->
                <!--</el-menu-item>-->
            </el-menu>
        </el-aside>
        <el-container>
            <el-header style="font-size: 30px;height:60px;line-height:60px">
                <i class="icon-ios-menu" :class="rotateIcon" @click="handleCollapse"></i>
                <el-menu  class="el-menu-demo" mode="horizontal"  style="float: right">
                    <el-menu-item index="1">处理中心</el-menu-item>
                    <el-submenu index="2">
                        <template slot="title">我的工作台</template>
                        <el-menu-item index="2-1">选项1</el-menu-item>
                        <el-menu-item index="2-2">选项2</el-menu-item>
                        <el-menu-item index="2-3">选项3</el-menu-item>
                    </el-submenu>
                </el-menu>
            </el-header>
            <el-main>
                <div :style="contentContainerStyle">
                    <slot></slot>
                </div>
            </el-main>
        </el-container>
    </el-container>
</template>

<style lang="less">
    /*全局样式*/
    .main {
        margin: 20px;
    }
    .content {
        padding: 20px;
        background: rgb(255, 255, 255);
    }
    .logo {
        height: 60px;
        background-color: #ff9900;
        padding: 14px 0 14px 16px;
        line-height: 32px;
    }
    .el-header {
        color: #333;
        line-height: 60px;
        background:#fff;
        box-shadow: 0 1px 4px rgba(0,21,41,.08);
    }
    .el-aside {
        color: #333;
        box-shadow: 2px 0 6px rgba(0,21,41,.35);
        -webkit-box-shadow: 2px 0 6px rgba(0,21,41,.35);
    }
    .el-menu {
        border-right:none;
    }

    .side-menu {
        .el-menu-item {
            border-bottom-color: #ff9900;
            background-color: rgb(65, 72, 88) !important;
        }
        .menu-icon {
            -webkit-transition: all .3s;
            transition: all .3s;
        }
    }

    .side-menu:not(.el-menu--collapse) {
        width: 200px;
        min-height: 400px;
    }

    .rotate-icon {
        -webkit-transform: rotate(-90deg);
        transform: rotate(-90deg);
    }

    /*全局重写*/
    .el-main {
        padding: 0;
    }
    .el-cascader,
    .el-select {
        width: 100%;
    }
    .el-message {
        top:8px;
    }
</style>

<script>
    export default {
        data() {
            return {
                collapse: false,
                collapseTransition: false,
                menuMinHeight:'400px',
                contentContainerStyle:{
                    // padding: '20px',
                    // background: '#fff',
                    minHeight:'',
                },
            }
        },
        computed: {
            rotateIcon() {
                return [
                    'menu-icon',
                    this.collapse ? 'rotate-icon' : ''
                ];
            },
        },
        methods: {
            handleCollapse() {
                if(this.collapse) {
                    this.collapse = false;
                }else {
                    this.collapse = true;
                }
            },
            handleContentContainerStyle() {
                window.fullHeight = document.documentElement.clientHeight;
                this.menuMinHeight = window.fullHeight + 'px';
                return this.contentContainerStyle.minHeight = (window.fullHeight - 100) + 'px';
            }
        },
        created() {
            window.addEventListener('resize', this.handleContentContainerStyle);
            this.handleContentContainerStyle();
        },
        destroyed() {
            window.removeEventListener('resize', this.handleContentContainerStyle)
        },
    };
</script>