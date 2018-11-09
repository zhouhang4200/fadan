import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const App = resolve => void(require(['../components/App'], resolve));

//router
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            name: "login",
            menu: false,
            path: "/v2/login",
            meta:{title:'登录'},
            component: resolve => void(require(['../components/Login'], resolve)),
        },
        {
            name: "order",
            icon: "el-icon-goods",
            path: '/v2/order/',
            redirect:'/v2/order/game-leveling',
            component: App,
            // canReuse: false,
            meta:{title:'订单'},
            menu:true,
            children: [
                {
                    name: "gameLevelingOrderTaobao",
                    menu: true,
                    path: "game-leveling/taobao",
                    meta:{title:'代练待发'},
                    component: resolve => void(require([ '../components/order/game-leveling/Taobao'], resolve)),
                },
                {
                    name: "gameLevelingOrderCreate",
                    menu: true,
                    path: "game-leveling/create",
                    meta:{title:'代练发布'},
                    component: resolve => void(require([ '../components/order/game-leveling/Create'], resolve)),
                },
                {
                    name: "gameLevelingOrder",
                    menu: true,
                    path: "game-leveling",
                    meta:{title:'代练订单'},
                    component: resolve => void(require(['../components/order/game-leveling/List'], resolve)),
                },
                {
                    name: "gameLevelingOrderBusinessmanComplain",
                    menu: true,
                    path: "game-leveling/businessman-complain",
                    meta:{title:'订单投诉'},
                    component: resolve => void(require([ '../components/order/game-leveling/BusinessmanComplain'], resolve)),
                },
                {
                    name: "gameLevelingOrderShow",
                    menu: false,
                    path: "game-leveling/show",
                    meta:{title:'订单详情'},
                    component: resolve => void(require([ '../components/order/game-leveling/Show'], resolve)),
                },
                {
                    name: "gameLevelingOrderRepeat",
                    menu: false,
                    path: "game-leveling/repeat",
                    meta:{title:'订单重发'},
                    component: resolve => void(require([ '../components/order/game-leveling/Repeat'], resolve)),
                },
            ]
        },
        {
            name: "finance",
            icon: "el-icon-date",
            path: '/v2/finance/',
            redirect:'/v2/finance/asset',
            component: App,
            meta:{title:'财务'},
            menu:true,
            children: [
                {
                    name: "financeAsset",
                    path: "asset",
                    menu:true,
                    meta:{title:'我的资金'},
                    component: resolve => void(require([ '../components/finance/Asset'], resolve)),
                },
                {
                    name: "financeAmountFlow",
                    path: "amount-flow",
                    menu:true,
                    meta:{title:'资金流水'},
                    component: resolve => void(require([ '../components/finance/AmountFlow'], resolve)),
                },
                {
                    name: "financeDailyAsset",
                    path: "daily-asset",
                    menu:true,
                    meta:{title:'资金日报'},
                    component: resolve => void(require([ '../components/finance/DailyAsset'], resolve)),
                },
                {
                    name: "financeWithdraw",
                    path: "withdraw",
                    menu:true,
                    meta:{title:'我的提现'},
                    component:resolve => void(require([ '../components/finance/Withdraw'], resolve)),
                },
                {
                    name: "financeStatisticEmployee",
                    path: "statistic-employee",
                    menu:true,
                    meta:{title:'员工统计'},
                    component: resolve => void(require([ '../components/finance/StatisticEmployee'], resolve)),
                },
                {
                    name: "financeStatisticOrder",
                    path: "statistic-order",
                    menu:true,
                    meta:{title:'订单统计'},
                    component: resolve => void(require([ '../components/finance/StatisticOrder'], resolve)),
                },
                {
                    name: "financeStatisticMessage",
                    path: "statistic-message",
                    menu:true,
                    meta:{title:'短信统计'},
                    component: resolve => void(require([ '../components/finance/StatisticMessage'], resolve)),
                },
                {
                    name: "financeStatisticMessageShow",
                    path: "statistic-message-show",
                    meta:{title:'短信统计详情'},
                    component: resolve => void(require([ '../components/finance/StatisticMessageShow'], resolve)),
                },
                {
                    name: "financeOrder",
                    path: "order",
                    menu:true,
                    meta:{title:'财务订单统计'},
                    component: resolve => void(require([ '../components/finance/Order'], resolve)),
                },
            ]
        },
        {
            name: "account",
            icon: "el-icon-news",
            path: '/v2/account/',
            component: App,
            redirect:'/v2/account/mine',
            meta:{title:'账号'},
            menu:true,
            children: [
                {
                    name: "mine",
                    path: "mine",
                    menu:true,
                    meta:{title:'我的账号'},
                    component: resolve => void(require([ '../components/account/Mine'], resolve)),
                },
                {
                    name: "accountAuthentication",
                    path: "authentication",
                    menu:true,
                    meta:{title:'实名认证'},
                    component: resolve => void(require([ '../components/account/Authentication'], resolve)),
                },
                {
                    name: "accountLoginHistory",
                    path: "login-history",
                    menu:true,
                    meta:{title:'登录记录'},
                    component: resolve => void(require([ '../components/account/LoginHistory'], resolve)),
                },
                {
                    name: "accountEmployee",
                    path: "employee",
                    menu:true,
                    meta:{title:'员工管理'},
                    component: resolve => void(require([ '../components/account/Employee'], resolve)),
                },
                {
                    name: "accountStation",
                    path: "station",
                    menu:true,
                    meta:{title:'岗位管理'},
                    component: resolve => void(require([ '../components/account/Station'], resolve)),
                },
                {
                    name: "accountBlackList",
                    path: "black-list",
                    menu:true,
                    meta:{title:'打手黑名单'},
                    component: resolve => void(require([ '../components/account/BlackList'], resolve)),
                },
            ]
        },
        {
            name: "设置",
            icon: "el-icon-setting",
            path: '/v2/setting/',
            redirect:'/v2/setting/message',
            component: App,
            meta:{title:'设置'},
            menu:true,
            children: [
                {
                    name: "settingMessage",
                    path: "message",
                    menu:true,
                    meta:{title:'短信管理'},
                    component: resolve => void(require([ '../components/setting/Message'], resolve)),
                },
                {
                    name: "settingGoods",
                    path: "goods",
                    menu:true,
                    meta:{title:'抓取商品配置'},
                    component: resolve => void(require([ '../components/setting/Goods'], resolve)),
                },
                {
                    name: "settingAuthorize",
                    path: "authorize",
                    menu:true,
                    meta:{title:'店铺授权'},
                    component: resolve => void(require([ '../components/setting/Authorize'], resolve)),
                },
                {
                    name: "settingAuxiliary",
                    path: "auxiliary",
                    menu:true,
                    meta:{title:'代练发单辅助'},
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
router.beforeEach((to, from, next) => {

    if(to.name == 'login' ) {
        Vue.component('App', require('../components/Login.vue'));
    } else if(to.name == 'register') {
        Vue.component('App', require('../components/Register.vue'));
    } else {
        Vue.component('App', require('../components/Main.vue'));
    }

    if (to.meta.title) {
        document.title = '淘宝发单平台 - ' + to.meta.title;
    }

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
// 后置拦截器
router.afterEach((to, from, next) => {

});
export default router;
