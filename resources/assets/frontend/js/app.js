// window._ = require('lodash');
import _ from 'lodash';
import Vue from 'vue';
import VueRouter from 'vue-router';
import ElementUI from 'element-ui';
import '../iconfont/iconfont.css';
import '../sass/app.scss';
import "./static/gt.js";
import "./static/encrypt.js";
import api from './config/api';
import router from './config/route';
import VueSocketIO from 'vue-socket.io';

Vue.use(new VueSocketIO({
    debug: true,
    connection: window.location.hostname
}));
Vue.use(ElementUI, {size:"small"});
Vue.use(VueRouter);

// 挂载 api
Vue.prototype.$api = api;

const app = new Vue({
    el: '#app',
    router,
});
