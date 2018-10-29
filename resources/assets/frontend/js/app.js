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
Vue.component('game-leveling-order', require('./components/GameLevelingOrder.vue'));
// 代练下单
Vue.component('game-leveling-order-create', require('./components/GameLevelingOrderCreate.vue'));
// 代练订单编辑
Vue.component('game-leveling-order-show', require('./components/GameLevelingOrderShow.vue'));
// 资金流水
Vue.component('amount-flow', require('./components/AmountFlow.vue'));
// 我的资产
Vue.component('my-asset', require('./components/MyAsset.vue'));
// 资产日报
Vue.component('daily-asset', require('./components/DailyAsset.vue'));
// 员工统计
Vue.component('statistic-employee', require('./components/StatisticEmployee.vue'));
// 订单统计
Vue.component('statistic-order', require('./components/StatisticOrder.vue'));
// 短信统计
Vue.component('statistic-message', require('./components/StatisticMessage.vue'));
// 短信统计详情
Vue.component('statistic-message-show', require('./components/StatisticMessageShow.vue'));
// 财务订单统计
Vue.component('finance-order', require('./components/FinanceOrder.vue'));
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
