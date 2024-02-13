import vendorRouter from "./Router/vendorRouter";
import Vue from 'vue'
import VueRouter from 'vue-router'
import Multiselect from 'vue-multiselect'
import "vue-multiselect/dist/vue-multiselect.min.css"
import store from './Store/index'
import './plugins/directives.js';
import mixin from './plugins/mixin';

window.Vue = require('vue');
global.jQuery = require('jquery');

Vue.use(VueRouter);

var $ = global.jQuery;
window.$ = $;

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

const app = new Vue({
    el: '#app',
    router: vendorRouter,
    store
});