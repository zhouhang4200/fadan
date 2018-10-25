require('./bootstrap');

import Vue from 'vue';
import Vuex from 'vuex';
import ElementUI from 'element-ui';
import '../iconfont/iconfont.css';
import '../sass/app.scss';

Vue.use(ElementUI, {size:"small"});
Vue.use(Vuex);


Vue.component('layout', require('./components/Layout.vue'));
Vue.component('game-leveling-order', require('./components/GameLevelingOrder.vue'));
//资金流水
Vue.component('amount-flow', require('./components/AmountFlow.vue'));
// 我的资产
Vue.component('my-asset', require('./components/MyAsset.vue'));
// 资产日报
Vue.component('daily-asset', require('./components/DailyAsset.vue'));

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
