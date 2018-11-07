import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const Container = resolve => void(require(['../components/Container'], resolve));

//router
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            name: "订单",
            icon: "el-icon-goods",
            path: '/v2/order/',
            component: Container,
            // canReuse: false,
            children: [
                {
                    name: "代练待发",
                    menu: true,
                    path: "game-leveling-taobao",
                    component: resolve => void(require([ '../components/GameLevelingTaobao'], resolve)),
                },
                {
                    name: "代练发布",
                    menu: true,
                    path: "game-leveling-order-create",
                    component: resolve => void(require([ '../components/GameLevelingOrderCreate'], resolve)),
                },
                {
                    name: "代练订单",
                    menu: true,
                    path: "game-leveling",
                    component: resolve => void(require(['../components/GameLevelingOrder'], resolve)),
                },
                {
                    name: "订单投诉",
                    menu: true,
                    path: "game-leveling-businessman-complain",
                    component: resolve => void(require([ '../components/GameLevelingBusinessmanComplain'], resolve)),
                },
                {
                    name: "订单详情",
                    menu: false,
                    path: "game-leveling-order-show",
                    component: resolve => void(require([ '../components/GameLevelingOrderShow'], resolve)),
                },
                {
                    name: "订单重发",
                    menu: false,
                    path: "game-leveling-order-repeat",
                    component: resolve => void(require([ '../components/GameLevelingOrderRepeat'], resolve)),
                },
            ]
        },
        {
            name: "财务",
            icon: "el-icon-date",
            path: '/v2/finance/',
            component: Container,
            // canReuse: false,
            children: [
                {
                    name: "我的资金",
                    path: "asset",
                    menu:true,
                    component: resolve => void(require([ '../components/FinanceMyAsset'], resolve)),
                },
                {
                    name: "资金流水",
                    path: "flow",
                    menu:true,
                    component: resolve => void(require([ '../components/FinanceAmountFlow'], resolve)),
                },
                {
                    name: "资金日报",
                    path: "report-daily",
                    menu:true,
                    component: resolve => void(require([ '../components/FinanceDailyAsset'], resolve)),
                },
                {
                    name: "我的提现",
                    path: "withdraw",
                    menu:true,
                    component:resolve => void(require([ '../components/FinanceMyWithdraw'], resolve)),
                },
                {
                    name: "员工统计",
                    path: "statistic/employee",
                    menu:true,
                    component: resolve => void(require([ '../components/StatisticEmployee'], resolve)),
                },
                {
                    name: "订单统计",
                    path: "statistic/order",
                    menu:true,
                    component: resolve => void(require([ '../components/StatisticOrder'], resolve)),
                },
                {
                    name: "短信统计",
                    path: "statistic/message",
                    menu:true,
                    component: resolve => void(require([ '../components/StatisticMessage'], resolve)),
                },
                {
                    name: "短信统计详情",
                    path: "statistic/message/show",
                    component: resolve => void(require([ '../components/StatisticMessageShow'], resolve)),
                },
                {
                    name: "财务订单统计",
                    path: "order",
                    menu:true,
                    component: resolve => void(require([ '../components/FinanceOrder'], resolve)),
                },
            ]
        },
        {
            name: "账号",
            icon: "el-icon-news",
            path: '/v2/account/',
            component: Container,
            // canReuse: false,
            children: [
                {
                    name: "我的账号",
                    path: "/",
                    menu:true,
                    component: resolve => void(require([ '../components/AccountMine'], resolve)),
                },
                {
                    name: "实名认证",
                    path: "authentication",
                    menu:true,
                    component: resolve => void(require([ '../components/AccountAuthentication'], resolve)),
                },
                {
                    name: "登录记录",
                    path: "login/history",
                    menu:true,
                    component: resolve => void(require([ '../components/AccountLoginHistory'], resolve)),
                },
                {
                    name: "员工管理",
                    path: "employee",
                    menu:true,
                    component: resolve => void(require([ '../components/AccountEmployee'], resolve)),
                },
                {
                    name: "岗位管理",
                    path: "station",
                    menu:true,
                    component: resolve => void(require([ '../components/AccountStation'], resolve)),
                },
            ]
        },
        {
            name: "设置",
            icon: "el-icon-setting",
            path: '/v2/setting/',
            component: Container,
            // canReuse: false,
            children: [
                {
                    name: "短信管理",
                    path: "message",
                    menu:true,
                    component: resolve => void(require([ '../components/SettingMessage'], resolve)),
                },
                {
                    name: "抓取商品配置",
                    path: "goods",
                    menu:true,
                    component: resolve => void(require([ '../components/SettingGoods'], resolve)),
                },
                {
                    name: "店铺授权",
                    path: "authorize",
                    menu:true,
                    component: resolve => void(require([ '../components/SettingAuthorize'], resolve)),
                },
                {
                    name: "自动加价",
                    path: "auxiliary",
                    menu:true,
                    component: resolve => void(require([ '../components/SettingAuxiliary'], resolve)),
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
