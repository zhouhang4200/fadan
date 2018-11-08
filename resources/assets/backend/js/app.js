import Vuex from "vuex/types/index";

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

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

Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
    el: '#app'
});
