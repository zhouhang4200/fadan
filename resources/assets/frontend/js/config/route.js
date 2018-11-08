import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const Container = resolve => void(require(['../components/Container'], resolve));

//router
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            title: "订单",
            name: "order",
            icon: "el-icon-goods",
            path: '/v2/order/',
            component: Container,
            // canReuse: false,
            children: [
                {
                    title: "代练待发",
                    name: "gameLevelingOrderTaobao",
                    menu: true,
                    path: "game-leveling/taobao",
                    component: resolve => void(require([ '../components/order/game-leveling/Taobao'], resolve)),
                },
                {
                    title: "代练发布",
                    name: "gameLevelingOrderCreate",
                    menu: true,
                    path: "game-leveling/create",
                    component: resolve => void(require([ '../components/order/game-leveling/Create'], resolve)),
                },
                {
                    title: "代练订单",
                    name: "gameLevelingOrder",
                    menu: true,
                    path: "game-leveling",
                    component: resolve => void(require(['../components/order/game-leveling/List'], resolve)),
                },
                {
                    title: "订单投诉",
                    name: "gameLevelingOrderBusinessmanComplain",
                    menu: true,
                    path: "game-leveling/businessman-complain",
                    component: resolve => void(require([ '../components/order/game-leveling/BusinessmanComplain'], resolve)),
                },
                {
                    title: "订单详情",
                    name: "gameLevelingOrderShow",
                    menu: false,
                    path: "game-leveling/show",
                    component: resolve => void(require([ '../components/order/game-leveling/Show'], resolve)),
                },
                {
                    title: "订单重发",
                    name: "gameLevelingOrderRepeat",
                    menu: false,
                    path: "game-leveling/repeat",
                    component: resolve => void(require([ '../components/order/game-leveling/Repeat'], resolve)),
                },
            ]
        },
        {
            title: "财务",
            name: "finance",
            icon: "el-icon-date",
            path: '/v2/finance/',
            component: Container,
            // canReuse: false,
            children: [
                {
                    title: "我的资金",
                    name: "financeAsset",
                    path: "asset",
                    menu:true,
                    component: resolve => void(require([ '../components/finance/Asset'], resolve)),
                },
                {
                    title: "资金流水",
                    name: "financeAmountFlow",
                    path: "amount-flow",
                    menu:true,
                    component: resolve => void(require([ '../components/finance/AmountFlow'], resolve)),
                },
                {
                    title: "资金日报",
                    name: "financeDailyAsset",
                    path: "daily-asset",
                    menu:true,
                    component: resolve => void(require([ '../components/finance/DailyAsset'], resolve)),
                },
                {
                    title: "我的提现",
                    name: "financeWithdraw",
                    path: "withdraw",
                    menu:true,
                    component:resolve => void(require([ '../components/finance/Withdraw'], resolve)),
                },
                {
                    title: "员工统计",
                    name: "financeStatisticEmployee",
                    path: "statistic-employee",
                    menu:true,
                    component: resolve => void(require([ '../components/finance/StatisticEmployee'], resolve)),
                },
                {
                    title: "订单统计",
                    name: "financeStatisticOrder",
                    path: "statistic-order",
                    menu:true,
                    component: resolve => void(require([ '../components/finance/StatisticOrder'], resolve)),
                },
                {
                    title: "短信统计",
                    name: "financeStatisticMessage",
                    path: "statistic-message",
                    menu:true,
                    component: resolve => void(require([ '../components/finance/StatisticMessage'], resolve)),
                },
                {
                    title: "短信统计详情",
                    name: "financeStatisticMessageShow",
                    path: "statistic-message-show",
                    component: resolve => void(require([ '../components/finance/StatisticMessageShow'], resolve)),
                },
                {
                    title: "财务订单统计",
                    name: "financeOrder",
                    path: "order",
                    menu:true,
                    component: resolve => void(require([ '../components/finance/Order'], resolve)),
                },
            ]
        },
        {
            title: "账号",
            name: "account",
            icon: "el-icon-news",
            path: '/v2/account/',
            component: Container,
            // canReuse: false,
            children: [
                {
                    title: "我的账号",
                    name: "account",
                    path: "/",
                    menu:true,
                    component: resolve => void(require([ '../components/account/Mine'], resolve)),
                },
                {
                    title: "实名认证",
                    name: "accountAuthentication",
                    path: "authentication",
                    menu:true,
                    component: resolve => void(require([ '../components/account/Authentication'], resolve)),
                },
                {
                    title: "登录记录",
                    name: "accountLoginHistory",
                    path: "login-history",
                    menu:true,
                    component: resolve => void(require([ '../components/account/LoginHistory'], resolve)),
                },
                {
                    title: "员工管理",
                    name: "accountEmployee",
                    path: "employee",
                    menu:true,
                    component: resolve => void(require([ '../components/account/Employee'], resolve)),
                },
                {
                    title: "岗位管理",
                    name: "accountStation",
                    path: "station",
                    menu:true,
                    component: resolve => void(require([ '../components/account/Station'], resolve)),
                },
                {
                    title: "打手黑名单",
                    name: "accountBlackList",
                    path: "black-list",
                    menu:true,
                    component: resolve => void(require([ '../components/account/BlackList'], resolve)),
                },
            ]
        },
        {
            title: "设置",
            name: "设置",
            icon: "el-icon-setting",
            path: '/v2/setting/',
            component: Container,
            // canReuse: false,
            children: [
                {
                    title: "短信管理",
                    name: "settingMessage",
                    path: "message",
                    menu:true,
                    component: resolve => void(require([ '../components/setting/Message'], resolve)),
                },
                {
                    title: "抓取商品配置",
                    name: "settingGoods",
                    path: "goods",
                    menu:true,
                    component: resolve => void(require([ '../components/setting/Goods'], resolve)),
                },
                {
                    title: "店铺授权",
                    name: "settingAuthorize",
                    path: "authorize",
                    menu:true,
                    component: resolve => void(require([ '../components/setting/Authorize'], resolve)),
                },
                {
                    title: "代练发单辅助",
                    name: "settingAuxiliary",
                    path: "auxiliary",
                    menu:true,
                    component: resolve => void(require([ '../components/setting/Auxiliary'], resolve)),
                },
            ]
        }
    ],
});

// 访问权限
function canVisit(to) {
    return true;
}

//vue-router 前置拦截器
//vue-router拦截器
router.beforeEach((to, from, next) => {
    if (to.path == '/login') {
        // store.commit('setStateValue', { 'is_login': false, 'admin_data': { username: '', permission_text: '' } });
        next();
        return false;
    }
    // if (!store.state.is_login) {
    //     // // 获取登录信息
    //     // axios.get('/backend/login-status').then(response => {
    //     //     let { status, data, message } = response.data;
    //     //     if (status && Object.keys(data).length > 0) {
    //     //         store.commit('setStateValue', { 'is_login': true, 'admin_data': data.list });
    //     //         next();
    //     //         return false;
    //     //     } else {
    //     //         next({ path: '/login' });
    //     //         return false;
    //     //     }
    //     // });
    // }
    next();
});
router.afterEach((to, from, next) => {
    // 获取面包屑
    let breadcrumb_data = [
        {path: to.path, text: to.name}
    ];
    // store.commit('changeBreadcrumb', breadcrumb_data);
});
export default router;
