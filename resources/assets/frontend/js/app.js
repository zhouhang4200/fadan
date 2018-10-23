require('./bootstrap');

import Vue from 'vue';
import Vuex from 'vuex';
import iView from 'iview';
import { MessageBox } from 'element-ui';
import Test from './components/Test/Test.js';
import CustomModal from './components/CustomModal/CustomModal.js';
import '../iconfont/iconfont.css';
import '../less/theme.less';

Vue.use(iView);
Vue.use(Vuex);
Vue.use(MessageBox);


Vue.component('layout', require('./components/Layout.vue'));
Vue.component('game-leveling-order', require('./components/GameLevelingOrder.vue'));

const store = new Vuex.Store({
    state: {
        pageTitle: ''
    },
    mutations: {
        setPageTitle(state, par){
            state.pageTitle = par.pageTitle
        }
    }
});

const app = new Vue({
    el: '#app',
    store,
});
