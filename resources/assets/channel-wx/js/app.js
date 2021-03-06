import Vue from 'vue';
import VueRouter from 'vue-router';
import Vant from 'vant';
import 'vant/lib/index.css';
import api from './config/api'
import router from './config/route';
import validate from './config/validate';
import 'vant/lib/index.css';


Vue.use(Vant);
Vue.use(VueRouter);

// 挂载 api
Vue.prototype.$api = api;

const app = new Vue({
    el: '#app',
    router,
});
