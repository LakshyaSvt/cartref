import Vue from 'vue'
import Vuex from 'vuex'
import './axioscustom';

Vue.use(Vuex);

//init store
const store = new Vuex.Store({
    state: {
        url: window.location.origin,
        baseUrl: window.location.origin + "/api",
        assetUrl: window.location.origin,
        storageUrl: "https://cartrefs.com" + '/storage/',
        defaultRowCount: '25',
        row_counts: [
            '25',
            '50',
            '100',
            '250',
            'All'
        ],
        user: {
            authenticated: true,
        },
        product_filter:{
            url: "/admin/product",
            keyword: "",
            seller_id: "",
            status: "",
            parent_category_id: "",
            sub_category_id: "",
            row_count: "25",
        }
    },
    mutations: {},
    actions: {},
})
export default store;
