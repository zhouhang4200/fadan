// window._ = require('lodash');
import _ from 'lodash';
import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import ElementUI from 'element-ui';
import '../iconfont/iconfont.css';
import '../sass/app.scss';
import api from './config/api'
import router from './config/route';

Vue.use(ElementUI, {size:"small"});
Vue.use(Vuex);
Vue.use(VueRouter);


// this.$store.state.applyConsultVisible 获取
// this.$store.commit('handlePageTitle',{pageTitle:this.pageTitle}) 修改
const store = new Vuex.Store({
    state: {
        openMenu: ['1'],
        openSubmenu: '',
        pageTitle: '',
    },
    mutations: {
        // 页标题
        handlePageTitle(state, par){
            state.pageTitle = par.pageTitle
        },
        // handleOpenMenu(state, par){
        //     state.openMenu[0] = par
        // },
        // handleOpenSubmenu(state, par){
        //     state.openSubmenu = par
        // },
    }
});
// 挂载 api
Vue.prototype.$api = api;
// 布局
Vue.component('App', require('./components/App.vue'));

const app = new Vue({
    el: '#app',
    store,
    router,
});
