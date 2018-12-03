import Vue from 'vue';
import VeeValidate, {Validator} from 'vee-validate';
import zh_CN from 'vee-validate/dist/locale/zh_CN'
import VueI18n from 'vue-i18n';

Vue.use(VueI18n);
const i18n = new VueI18n({
    locale: 'zh_CN',
});

Vue.use(VeeValidate, {
    i18n,
    i18nRootKey: 'validation',
    dictionary: {
        zh_CN:{
            messages: {
                required: field => `${field}不能为空`
            }
        }
    }
});
Validator.extend('phone', {
    getMessage: field => "请输入正确的手机号码",
    validate: value => value.length === 11 && /^((13|14|15|17|18)[0-9]{1}\d{8})$/.test(value)
});