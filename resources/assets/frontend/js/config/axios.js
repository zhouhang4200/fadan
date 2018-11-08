import Vue from 'vue'
import axios from 'axios'
// import qs from 'qs'
import {Message, MessageBox} from 'element-ui'

// 响应时间
axios.defaults.timeout = 5 * 10000;

// 配置cookie
// axios.defaults.withCredentials = true

// 配置请求头
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';

// 配置scrf token
let token = document.head.querySelector('meta[name="csrf-token"]');
axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;

// 静态资源
Vue.prototype.$static = '';

// 配置接口地址
axios.defaults.baseURL = '';

// POST传参序列化(添加请求拦截器)
axios.interceptors.request.use(
    config => {
        // loadingInstance = Loading.service({
        //     lock: true,
        //     text: '数据加载中，请稍后...',
        //     spinner: 'el-icon-loading',
        //     background: 'rgba(0, 0, 0, 0.7)'
        // });
        // if (config.method === 'post') {
        //     config.data = qs.stringify(config.data)
        // }
        return config
    },
    err => {
        // loadingInstance.close();
        Message.error('请求错误');
        return Promise.reject(err);
    }
);

// 返回状态判断(添加响应拦截器)
axios.interceptors.response.use(
    res => {
        if (res.status === 200) {
            return res;
        } else {
            Message.error(res.data.message)
        }
    },
    err => {
        if (err.response.status === 401) {
            MessageBox.alert('您的登录信息失效需要重新登录', '登录失效', {
                confirmButtonText: '确定',
                callback: action => {
                   window.location='/login';
                }
            });
        } else {
            Message.error('请求失败，请稍后再试');
        }
        return Promise.reject(err)
    }
);

// 发送POST请求
export function post(url, params) {
    return new Promise((resolve, reject) => {
        axios
            .post(url, params)
            .then(
                res => {
                    resolve(res.data)
                },
                err => {
                    reject(err.data)
                }
            )
            .catch(err => {
                reject(err.data)
            })
    })
}
// 发送GET请求
export function get(url, params) {
    return new Promise((resolve, reject) => {
        axios
            .get(url, {
                params: params
            })
            .then(res => {
                resolve(res.data)
            })
            .catch(err => {
                reject(err.data)
            })
    })
}
