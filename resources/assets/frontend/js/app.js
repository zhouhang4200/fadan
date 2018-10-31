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
// 资金流水
Vue.component('amount-flow', resolve => void(require(['./components/AmountFlow.vue'], resolve)));
// 我的资产
Vue.component('my-asset', resolve => void(require(['./components/MyAsset.vue'], resolve)));
// 资产日报
Vue.component('daily-asset', resolve => void(require(['./components/DailyAsset.vue'], resolve)));
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
