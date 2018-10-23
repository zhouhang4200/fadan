import Vue from 'vue'

const CustomModal = Vue.extend(require('./CustomModal.vue')); // 直接将Vue组件作为Vue.extend的参数

let nId = 1;

const Modal = (content) => {
    let id = 'notice-' + nId++;

    const CustomModalInstance = new CustomModal({
        data: {
            content: content
        }
    });

    CustomModalInstance.id = id;
    CustomModalInstance.vm = CustomModalInstance.$mount(); // 挂载但是并未插入dom，是一个完整的Vue实例
    CustomModalInstance.vm.visible = true;
    CustomModalInstance.dom = CustomModalInstance.vm.$el;
    document.body.appendChild(CustomModalInstance.dom); // 将dom插入body
    CustomModalInstance.dom.style.zIndex = nId + 1001; // 后插入的Notice组件z-index加一，保证能盖在之前的上面
    return CustomModalInstance.vm;
};

export default {
    install: Vue => {
        Vue.prototype.$customModal = Modal
    }
}