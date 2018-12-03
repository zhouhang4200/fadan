import Vue from 'vue';
import VueRouter from 'vue-router';
import ElementUI from 'element-ui';
import api from './config/api';
import router from './config/route';

Vue.use(ElementUI, {size:"small"});
Vue.use(VueRouter);

// 挂载 api
Vue.prototype.$api = api;

const app = new Vue({
    el: '#app',
    router,
});
