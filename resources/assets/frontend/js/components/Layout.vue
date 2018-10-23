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
    }
    .logo {
        height: 60px;
        background-color: #ff9900;
        padding: 14px 0px 14px 18px;
    }
    .logo i {
        font-size: 32px;
        margin-right:0;
        color:#fff;
    }
    .menu-item i{
        transform: translateX(0px);
        transition: font-size .2s ease, transform .2s ease;
        vertical-align: middle;
        font-size: 16px;
    }

    /*** 重写左侧菜单间距 ***/
    .ivu-menu-dark.ivu-menu-vertical .ivu-menu-item .ivu-menu-submenu .ivu-menu-submenu-title {
        color:#ffffff;
        padding: 14px 15px;
    }
    /*** 重写头部菜单高度 ***/
    .ivu-layout-header {
        height: 60px;
        line-height: 60px;
    }

    .collapsed .ivu-tooltip .ivu-tooltip-popper {
        top:70px;
    }
    /*** 重写右侧折叠后菜单样式 ***/
    .collapsed .ivu-select-dropdown,
    .collapsed .ivu-dropdown > .ivu-select-dropdown {
        background-color: #515a6e;
        border-radius:0px;
    }
    .collapsed .ivu-dropdown-item {
        color:#fff;
    }
    .collapsed .ivu-dropdown-item:hover {
        background: #363e4f;
        color: #ff9900;
    }
    /*** 去除右侧菜单折叠动画 ***/
    .ivu-layout-sider {
        -webkit-transition: none;
        transition: none;
    }
</style>

<template>
    <div class="layout">
        <Layout>
            <Sider ref="side1" hide-trigger collapsible :collapsed-width="78" v-model="isCollapsed">
                <div class="logo">
                    <Icon type="ios-tao iconfont" v-bind:style="{'padding-left': isCollapsed ? '5px':'0'}"></Icon>
                    <img src="/frontend/v2/images/logo.png" style="vertical-align: -webkit-baseline-middle;" v-show="!isCollapsed">
                </div>
                <div class="expand">
                    <Menu active-name="2" theme="dark" width="auto" :class="" :style="expandStyle">
                        <MenuItem name="2">
                            <Icon type="ios-search"></Icon>
                            <span>Option 2</span>
                        </MenuItem>
                        <MenuItem name="3">
                            <Icon type="ios-settings"></Icon>
                            <span>Option 3</span>
                        </MenuItem>

                        <Submenu name="4">
                            <template slot="title">
                                <Icon type="ios-analytics"></Icon>
                                <span>Option 3</span>
                            </template>
                            <MenuItem name="3-1">Option 1</MenuItem>
                            <MenuItem name="3-2">Option 2</MenuItem>
                        </Submenu>
                    </Menu>
                </div>
                <div class="collapsed" :style="collapsedStyle">
                    <Tooltip content="Left Top text" placement="left-start" style="display: block">
                        <a style="padding: 14px 15px;display:block;width:78px;text-align:center">
                            <Icon type="ios-analytics" style="font-size: 22px;"></Icon>
                        </a>
                    </Tooltip>
                    <Dropdown placement="right-start" style="display: block">
                        <a href="javascript:void(0)" style="padding: 14px 15px;display:block;width:78px;text-align:center">
                            <Icon type="ios-analytics" style="font-size: 22px;"></Icon>
                        </a>
                        <DropdownMenu slot="list">
                            <DropdownItem>驴打滚</DropdownItem>
                            <DropdownItem>炸酱面</DropdownItem>
                            <DropdownItem>豆汁儿</DropdownItem>
                            <DropdownItem>冰糖葫芦</DropdownItem>
                            <DropdownItem>北京烤鸭</DropdownItem>
                        </DropdownMenu>
                    </Dropdown>
                </div>
            </Sider>
            <Layout>
                <Header :style="{padding: 0}" class="layout-header-bar">
                    <Icon @click.native="collapsedSider" :class="rotateIcon" :style="{margin: '0 20px'}" type="md-menu" size="24"></Icon>
                    <div class="" style="height: inherit;float: right;">
                        <Menu mode="horizontal"  active-name="1">
                            <MenuItem name="2">
                                <Icon type="ios-people" />
                                留言列表
                            </MenuItem>
                            <Submenu name="3">
                                <template slot="title">
                                    <Avatar style="margin-right: 8px" src="https://i.loli.net/2017/08/21/599a521472424.jpg" />
                                    王小明
                                </template>
                                <MenuGroup title="使用">
                                    <MenuItem name="3-1">新增和启动</MenuItem>
                                    <MenuItem name="3-2">活跃分析</MenuItem>
                                    <MenuItem name="3-3">时段分析</MenuItem>
                                </MenuGroup>
                                <MenuGroup title="留存">
                                    <MenuItem name="3-4">用户留存</MenuItem>
                                    <MenuItem name="3-5">流失用户</MenuItem>
                                </MenuGroup>
                            </Submenu>
                        </Menu>
                    </div>
                </Header>
                <div style="margin:10px 20px 0;background-color: #f5f7f9">
                    <Breadcrumb style="background-color: #f5f7f9">
                        <BreadcrumbItem to="/">首页</BreadcrumbItem>
                        <BreadcrumbItem>{{ this.$store.state.pageTitle }}</BreadcrumbItem>
                    </Breadcrumb>
                </div>
                <Content :style="contentContainerStyle">
                    <slot></slot>
                </Content>
            </Layout>
        </Layout>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                isCollapsed: false,
                contentContainerStyle:{
                    margin: '10px 20px 20px 20px',
                    padding: '20px',
                    background: '#fff',
                    minHeight:'',
                },
                placement: 'right-end'
            }
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
            },
            expandStyle() {
                return this.isCollapsed ? 'display:none' :  ''
            },
            collapsedStyle() {
                return this.isCollapsed ? '' :  'display:none'
            },

        },
        methods: {
            collapsedSider() {
                this.$refs.side1.toggleCollapse();
            },
            setContentContainerHeight() {
                window.fullHeight = document.documentElement.clientHeight
                return this.contentContainerStyle.minHeight = (window.fullHeight - 124) + 'px';
            },
        },
        created() {
            window.addEventListener('resize', this.setContentContainerHeight);
            this.setContentContainerHeight();
        },
        destroyed() {
            window.removeEventListener('resize', this.setContentContainerHeight)
        }
    }
</script>




