// window._ = require('lodash');
import _ from 'lodash';
import Vue from 'vue';
import VueRouter from 'vue-router';
import ElementUI from 'element-ui';
import '../iconfont/iconfont.css';
import '../sass/app.scss';
import api from './config/api'
import router from './config/route';

Vue.use(ElementUI, {size:"small"});
Vue.use(VueRouter);

// 挂载 api
Vue.prototype.$api = api;
// 布局
Vue.component('App', require('./components/App.vue'));

const app = new Vue({
    el: '#app',
    router,
});
