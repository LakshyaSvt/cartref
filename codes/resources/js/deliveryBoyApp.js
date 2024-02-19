import Vue from 'vue'
import VueRouter from 'vue-router'
import deliveryBoyRouter from "./Router/deliveryBoyRouter";
import store from './Store/index'
import mixin from './plugins/mixin';
import './plugins/directives.js';

window.Vue = require('vue');
global.jQuery = require('jquery');

Vue.use(VueRouter);

var $ = global.jQuery;
window.$ = $;

Vue.component('main-app', require('@pages/delivery-boy/Main.vue').default);
//main-components
Vue.component('side-bar', require('@components/DeliveryBoySideBar.vue').default);
Vue.component('navbar', require('@components/NavBar.vue').default);
//utility-components
Vue.component('Skeleton', require('@components/Skeleton.vue').default);
Vue.component('Spinner', require('@components/Spinner.vue').default);
Vue.component('Wait', require('@components/Wait.vue').default);
Vue.component('Pagination', require('@components/Pagination.vue').default);
Vue.component('ImageModal', require('@components/ImageModal.vue').default);

const app = new Vue({
    el: '#app',
    router: deliveryBoyRouter,
    store
});
