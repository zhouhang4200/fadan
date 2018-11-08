<template>
    <el-container>

        <el-aside :style="{'width': collapse ? '64px':'200px', 'background-color': '#515a6e'}">
            <div class="logo">
                <i class="icon-tao" style="font-size:32px;color:#fff"></i>
                <img src="/frontend/v2/images/logo.png" style="vertical-align: top" v-show="!collapse">
            </div>
            <!--:default-openeds="['1']"-->
            <el-menu
                    :default-openeds="openMenu"
                    @select="handleSelect"
                    :unique-opened=true
                    :collapse-transition=false
                    class="side-menu"
                    :router=true
                    :default-active="$route.path"
                    background-color="#515a6e"
                    :min-height="menuMinHeight"
                    text-color="#fff"
                    active-text-color="#ffd04b"
                    :collapse="collapse">

                <el-submenu v-for="item in menus" :index="item.path">
                    <template slot="title">
                        <i :class="item.icon"></i>
                        <span slot="title">{{ item.title }}</span>
                    </template>
                    <el-menu-item v-for="submenu in item.children"
                                  :index="item.path + submenu.path"
                                  v-if="submenu.menu === true">
                        {{ submenu.title }}
                    </el-menu-item>
                </el-submenu>
            </el-menu>
        </el-aside>
        <el-container>
            <el-header style="font-size: 30px;height:60px;line-height:60px">
                <i class="icon-ios-menu" :class="rotateIcon" @click="handleCollapse"></i>
                <el-menu  class="el-menu-demo" mode="horizontal"  style="float: right">
                    <el-menu-item index="1">
                        <i class="el-icon-bell"></i>
                    </el-menu-item>
                    <el-submenu index="2">
                        <template slot="title">
                            <span class="header-avatar"></span>
                            我的工作台
                        </template>
                        <el-menu-item index="2-1">修改密码</el-menu-item>
                        <el-menu-item index="2-3">{{ $route.name }}</el-menu-item>
                    </el-submenu>
                </el-menu>
            </el-header>
            <el-main>

                <div :style="contentContainerStyle">
                    <el-breadcrumb separator-class="el-icon-arrow-right"
                                   style="height: 45px;line-height: 45px;margin-left: 20px">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item>活动管理</el-breadcrumb-item>
                        <el-breadcrumb-item>1</el-breadcrumb-item>
                    </el-breadcrumb>
                    <router-view></router-view>
                </div>
            </el-main>
        </el-container>
    </el-container>
</template>

<style lang="less">
    /*全局样式*/
    .main {
        /*margin:20px;*/
        margin:0 20px 20px;
    }
    .content {
        padding: 20px;
        background: rgb(255, 255, 255);
    }
    .logo {
        width: auto;
        background-color: #ff9900;
        padding: 14px 0 14px 16px;
    }
    .header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgb(240, 171, 73);
        display: inline-flex;
        position: relative;
        top: 0;
        right: 10px;
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
    /*创建订单、查看订单、重发订单输入框左侧菜单样式*/
    .icon-button {
        line-height: 32px;
        font-size: 22px;
        height: 32px;
    }
    /*上传图片超过限制时隐藏增加图片按钮*/
    .exceed .el-upload {
        display: none;
    }
    /*预览图片*/
    .preview-image {
        width: auto;
        max-width: 800px;
        background-color: transparent;
        border: none;
        box-shadow: 0 0 0 0;
        -webkit-box-shadow: 0 0 0 0;
    }
    /*限制预览图片的最大宽度*/
    .preview-image img{
        max-width: 800px;
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
    .el-form-item.is-success .el-input__inner,
    .el-form-item.is-success .el-input__inner:focus, .el-form-item.is-success .el-textarea__inner,
    .el-form-item.is-success .el-textarea__inner:focus {
        border-color:#DCDFE6;
    }
</style>

<script>
    export default {
        name:"app",
        data() {
            return {
                collapse: false,
                menus:null,
                openMenu:['1'],
                menuMinHeight:'400px',
                contentContainerStyle:{
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
                    sessionStorage.setItem('collapse', '0');
                }else {
                    this.collapse = true;
                    sessionStorage.setItem('collapse', '1');
                }
            },
            handleContentContainerStyle() {
                window.fullHeight = document.documentElement.clientHeight;
                this.menuMinHeight = window.fullHeight + 'px';
                return this.contentContainerStyle.minHeight = (window.fullHeight - 80) + 'px';
            },
            handleSelect(key, keyPath) {
                sessionStorage.setItem('openMenu', keyPath[0]);
            }
        },
        created() {
            window.addEventListener('resize', this.handleContentContainerStyle);
            this.handleContentContainerStyle();
        },
        beforeMount(){
            this.openMenu[0] = sessionStorage.getItem('openMenu')
        },
        mounted() {
            this.collapse = (sessionStorage.getItem('collapse') == 1 ? true : false);
            this.menus = this.$router.options.routes;
        },
        destroyed() {
            window.removeEventListener('resize', this.handleContentContainerStyle)
        },
    };
</script>