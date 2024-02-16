import vendorRouter from "./Router/vendorRouter";
import Vue from 'vue'
import Pusher from 'pusher-js';
import VueRouter from 'vue-router'
import Multiselect from 'vue-multiselect'
import "vue-multiselect/dist/vue-multiselect.min.css"
import store from './Store/index'
import './plugins/directives.js';
import mixin from './plugins/mixin';
import Main from "./pages/vendor/Main.vue";

window.Vue = require('vue');
global.jQuery = require('jquery');

Vue.use(VueRouter);

var $ = global.jQuery;
window.$ = $;

Vue.component('main-app', require('@pages/vendor/Main.vue').default);
//main-components
Vue.component('side-bar', require('@components/VendorSideBar.vue').default);
Vue.component('navbar', require('@components/NavBar.vue').default);
//utility-components
Vue.component('Skeleton', require('@components/Skeleton.vue').default);
Vue.component('Spinner', require('@components/Spinner.vue').default);
Vue.component('StatusCheckbox', require('@components/StatusCheckbox.vue').default);
Vue.component('Wait', require('@components/Wait.vue').default);
Vue.component('Pagination', require('@components/Pagination.vue').default);
Vue.component('ImageModal', require('@components/ImageModal.vue').default);
//lib-components
Vue.component('RichTextEditor', require('@components/RichTextEditor.vue').default);
Vue.component('Multiselect', Multiselect)

//Pusher Config
window.Pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});

const app = new Vue({
    el: '#app',
    router: vendorRouter,
    store,
});
