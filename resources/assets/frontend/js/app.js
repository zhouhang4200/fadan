require('./bootstrap');

import Vue from 'vue';
import Vuex from 'vuex';
import ElementUI from 'element-ui';
import '../iconfont/iconfont.css';
import '../sass/app.scss';

Vue.use(ElementUI, {size:"small"});
Vue.use(Vuex);

// 布局
Vue.component('layout', require('./components/Layout.vue'));
// 代练订单列表
Vue.component('game-leveling-order', resolve => void(require(['./components/GameLevelingOrder.vue'], resolve)));
// 代练下单
Vue.component('game-leveling-order-create', resolve => void(require(['./components/GameLevelingOrderCreate.vue'], resolve)));
// 代练订单编辑
Vue.component('game-leveling-order-show', resolve => void(require(['./components/GameLevelingOrderShow.vue'], resolve)));
// 重新下单
Vue.component('game-leveling-order-repeat', resolve => void(require(['./components/GameLevelingOrderRepeat.vue'], resolve)));
// 资金流水
Vue.component('finance-amount-flow', resolve => void(require(['./components/FinanceAmountFlow.vue'], resolve)));
// 我的提现
Vue.component('finance-my-withdraw', resolve => void(require(['./components/FinanceMyWithdraw.vue'], resolve)));
// 我的资产
Vue.component('finance-my-asset', resolve => void(require(['./components/FinanceMyAsset.vue'], resolve)));
// 资产日报
Vue.component('finance-daily-asset', resolve => void(require(['./components/FinanceDailyAsset.vue'], resolve)));
// 员工统计
Vue.component('statistic-employee', resolve => void(require(['./components/StatisticEmployee.vue'], resolve)));
// 订单统计
Vue.component('statistic-order', resolve => void(require(['./components/StatisticOrder.vue'], resolve)));
// 短信统计
Vue.component('statistic-message', resolve => void(require(['./components/StatisticMessage.vue'], resolve)));
// 短信统计详情
Vue.component('statistic-message-show', resolve => void(require(['./components/StatisticMessageShow.vue'], resolve)));
// 财务订单统计
Vue.component('finance-order', resolve => void(require(['./components/FinanceOrder.vue'], resolve)));
// 我的账号
Vue.component('account-mine', resolve => void(require(['./components/AccountMine.vue'], resolve)));
// 登录记录
Vue.component('account-login-history', resolve => void(require(['./components/AccountLoginHistory.vue'], resolve)));
// 岗位管理
Vue.component('account-employee', resolve => void(require(['./components/AccountEmployee.vue'], resolve)));
// 岗位新增
Vue.component('account-employee-create', resolve => void(require(['./components/AccountEmployeeCreate.vue'], resolve)));
// 打手黑名单
Vue.component('account-black-list', resolve => void(require(['./components/AccountBlackList.vue'], resolve)));
// 实名认证
Vue.component('account-authentication', resolve => void(require(['./components/AccountAuthentication.vue'], resolve)));
// 岗位管理
Vue.component('account-station', resolve => void(require(['./components/AccountStation.vue'], resolve)));
// this.$store.state.applyConsultVisible 获取
// this.$store.commit('handlePageTitle',{pageTitle:this.pageTitle}) 修改
const store = new Vuex.Store({
    state: {
        pageTitle: '',
    },
    mutations: {
        // 页标题
        handlePageTitle(state, par){
            state.pageTitle = par.pageTitle
        }
    }
});

const app = new Vue({
    el: '#app',
    store,
});
