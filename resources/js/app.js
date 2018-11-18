
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import iView from 'iview';
import VeeValidate, {Validator} from 'vee-validate';
import zh_CN from './locale/zh_CN';
import 'iview/dist/styles/iview.css';
import API from './api/api';
Vue.prototype.$api = API;

Validator.localize('zh_CN', zh_CN);

Vue.use(iView);
Vue.use(VeeValidate, {
    events: 'input|blur',
    locale: 'zh_CN',
    fieldsBagName: 'fieldBags',
});
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('user', require('./components/admin/user.vue'));
Vue.component('profile', require('./components/admin/profile.vue'));
Vue.component('role', require('./components/admin/role.vue'));
Vue.component('phone', require('./components/home/phone'));


const app = new Vue({
    el: '#app',
    created() {
        this.init();
    },
    methods: {
        init() {
            this.$Message.config({
                top: 50,
                duration: 3
            });
        
            this.$Notice.config({
                top: 50,
                duration: 3
            });
        },
    }
});
