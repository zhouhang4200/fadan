import Vue from 'vue'

const NoticeConstructor = Vue.extend(require('./Test.vue')); // 直接将Vue组件作为Vue.extend的参数

let nId = 1;

const Notice = (content) => {
    let id = 'notice-' + nId++;

    const NoticeInstance = new NoticeConstructor({
        data: {
            content: content
        }
    }); // 实例化一个带有content内容的Notice

    NoticeInstance.id = id;
    NoticeInstance.vm = NoticeInstance.$mount(); // 挂载但是并未插入dom，是一个完整的Vue实例
    NoticeInstance.vm.visible = true;
    NoticeInstance.dom = NoticeInstance.vm.$el;
    document.body.appendChild(NoticeInstance.dom); // 将dom插入body
    NoticeInstance.dom.style.zIndex = nId + 1001; // 后插入的Notice组件z-index加一，保证能盖在之前的上面
    return NoticeInstance.vm;
};

export default {
    install: Vue => {
        Vue.prototype.$test = Notice // 将Notice组件暴露出去，并挂载在Vue的prototype上
    }
}