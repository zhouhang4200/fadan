import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

//router
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '*',
            redirect: '/order',
            component: () => import('../components/Main'),
            children: [
                {
                    name: "order",
                    path: "order",
                    meta:{
                        title:'下单'
                    },
                    component: () => import('../components/Order'),
                },
                {
                    name: "orderList",
                    path: "order/list",
                    meta:{
                        title:'订单列表'
                    },
                    component: () => import('../components/OrderList'),
                },
            ]
        },
    ],
});

//vue-router 前置拦截器
router.beforeEach((to, from, next) => {

    if (to.meta.title) {
        document.title = '淘宝发单平台 - ' + to.meta.title;
    }

    if (to.path == '/login') {
        // store.commit('setStateValue', { 'is_login': false, 'admin_data': { username: '', permission_text: '' } });
        next();
        return false;
    }

    // if (!store.state.is_login) {
    //     // // 获取登录信息
    //     // axios.get('/backend/login-status').then(response => {
    //     //     let { status, data, message } = response.data;
    //     //     if (status && Object.keys(data).length > 0) {
    //     //         store.commit('setStateValue', { 'is_login': true, 'admin_data': data.list });
    //     //         next();
    //     //         return false;
    //     //     } else {
    //     //         next({ path: '/login' });
    //     //         return false;
    //     //     }
    //     // });
    // }
    next();
});
// 后置拦截器
router.afterEach((to, from, next) => {

});
export default router;
