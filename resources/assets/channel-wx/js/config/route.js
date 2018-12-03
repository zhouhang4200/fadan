import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

//router
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/channel/order',
            redirect: '/channel/order',
            component: () => import('../components/Main'),
            children: [
                {
                    name: "order",
                    path: "/",
                    meta:{
                        title:'丸子代练'
                    },
                    component: () => import('../components/Order'),
                },
                {
                    name: "orderCreate",
                    path: "create",
                    meta:{
                        title:'填写订单信息'
                    },
                    component: () => import('../components/orderCreate'),
                },
                {
                    name: "orderList",
                    path: "list",
                    meta:{
                        title:'订单列表'
                    },
                    component: () => import('../components/OrderList'),
                },
                {
                    name: "orderDetail",
                    path: "detail",
                    meta:{
                        title:'订单详情'
                    },
                    component: () => import('../components/OrderDetail'),
                },
                {
                    name: "orderRefund",
                    path: "refund/:trade_no",
                    meta:{
                        title:'申请退款'
                    },
                    component: () => import('../components/OrderRefund'),
                },
                {
                    name: "paySuccess",
                    path: "pay/success",
                    meta:{
                        title:'付款成功'
                    },
                    component: () => import('../components/OrderPaySuccess'),
                },
            ]
        },
    ],
});

//vue-router 前置拦截器
router.beforeEach((to, from, next) => {
    if (to.meta.title && to.name != 'order') {
        document.title = '丸子代练 - ' + to.meta.title;
    } else {
        document.title = to.meta.title;
    }
    next();
});
// 后置拦截器
router.afterEach((to, from, next) => {

});
export default router;
