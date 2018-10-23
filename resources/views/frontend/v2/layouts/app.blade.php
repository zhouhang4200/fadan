<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="_token" content="{{ csrf_token() }}" >
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/frontend/v2/css/iview.css">
    @yield('css')
    <style scoped>
        .layout{
            border: 1px solid #d7dde4;
            background: #f5f7f9;
            position: relative;
            border-radius: 4px;
            overflow: hidden;
        }
        .layout-header-bar{
            background: #fff;
            box-shadow: 0 1px 1px rgba(0,0,0,.1);
        }
        .layout-logo-left{
            width: 90%;
            height: 30px;
            background: #5b6270;
            border-radius: 3px;
            margin: 15px auto;
        }
        .menu-icon{
            transition: all .3s;
        }
        .rotate-icon{
            transform: rotate(-90deg);
        }
        .menu-item span{
            display: inline-block;
            overflow: hidden;
            width: 117px;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: bottom;
            transition: width .2s ease .2s;
        }
        .menu-item .logo {
            width: 126px;
        }
        .menu-item i{
            transform: translateX(0px);
            transition: font-size .2s ease, transform .2s ease;
            vertical-align: middle;
            font-size: 16px;
        }
        .collapsed-menu .logo,
        .collapsed-menu span{
            width: 0;
            transition: width .2s ease;
        }
        .collapsed-menu .ivu-icon{
            padding-left: 5px;
        }
        .collapsed-menu .ivu-icon-ios-tao{
            padding-left: 0;
        }
        .collapsed-menu .ivu-icon-ios-arrow-down{
            display: none;
            transition: width .2s ease;
        }
        .collapsed-menu i{
            transform: translateX(5px);
            transition: font-size .2s ease .2s, transform .2s ease .2s;
            vertical-align: middle;
            font-size: 22px;
        }

        /**重写左侧菜单间距**/
        .ivu-menu-vertical .ivu-menu-item, .ivu-menu-vertical .ivu-menu-submenu-title {
            padding: 14px 15px;
        }
        .ivu-menu-dark.ivu-menu-vertical .ivu-menu-item, .ivu-menu-dark.ivu-menu-vertical .ivu-menu-submenu-title{
            color:#ffffff;
        }

    </style>
</head>
<body>
<div class="layout" id="app">

    <layout>
        <sider ref="side1" hide-trigger collapsible :collapsed-width="78" v-model="isCollapsed">
            <i-menu active-name="1-2" theme="dark" width="auto" :class="menuitemClasses">
                <menu-item name="1-1" style="background-color: #F78400;height: 64px;padding: 14px 0px 14px 15px;">
                    <icon type="ios-tao iconfont" style="font-size: 32px;margin-right:0"></icon>
                    <span class="logo"><img src="/frontend/v2/images/logo.png" style="vertical-align: -webkit-baseline-middle;"></span>
                </menu-item>
                <menu-item name="1-2">
                    <icon type="ios-search"></icon>
                    <span>Option 2</span>
                </menu-item>
                <menu-item name="1-3">
                    <icon type="ios-settings"></icon>
                    <span>Option 3</span>
                </menu-item>

                <submenu name="2">
                    <template slot="title">
                        <icon type="ios-settings"></icon>
                        <span>Option 3</span>
                    </template>
                    <menu-item name="2-1">Option 1</menu-item>
                    <menu-item name="2-2">Option 2</menu-item>
                </submenu>

            </i-menu>
        </sider>
        <layout>
            <i-header :style="{padding: 0}" class="layout-header-bar">
                <icon @click.native="collapsedSider" :class="rotateIcon" :style="{margin: '0 20px'}" type="md-menu" size="24"></icon>
            </i-header>
            <i-content :style="{margin: '20px', background: '#fff', minHeight: '260px'}">
                @yield('content')
            </i-content>
        </layout>
    </layout>

</div>
<script src="https://cdn.bootcss.com/vue/2.5.16/vue.js"></script>
<script src="https://unpkg.com/iview/dist/iview.min.js"></script>
<script>
    var vue  = new Vue({
        el: '#app',
        data: {
            isCollapsed: false
        },
        computed: {
            rotateIcon() {
                return [
                    'menu-icon',
                    this.isCollapsed ? 'rotate-icon' : ''
                ];
            },
            menuitemClasses() {
                return [
                    'menu-item',
                    this.isCollapsed ? 'collapsed-menu' : ''
                ]
            }
        },
        methods: {
            collapsedSider() {
                this.$refs.side1.toggleCollapse();
            }
        }
    });
</script>
@yield('js')
</body>
</html>