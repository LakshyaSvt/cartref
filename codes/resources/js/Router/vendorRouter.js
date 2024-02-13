import VueRouter from 'vue-router'
import Dashboard from "@pages/vendor/Dashboard.vue"
import Orders from "@pages/vendor/Orders.vue";
import Showcases from "@pages/vendor/Showcases.vue";

const adminRoutes = [
    {name: 'dashboard', path: '/vendor/dashboard', component: Dashboard},
    {name: 'orders', path: '/vendor/orders', component: Orders},
    {name: 'showcases', path: '/vendor/showcases/orders', component: Showcases},

    // /* Product Management */
    // {name: 'products', path: '/vendor/products', component: Products},
    // {name: 'product-bulk-upload', path: '/vendor/products/bulk-upload', component: ProductBulkUpload},
    // {name: 'product-edit', path: '/vendor/products/:id/edit', component: ProductCreateOrEdit},
    // {name: 'product-create', path: '/vendor/products/create', component: ProductCreateOrEdit},
    // {name: 'product-colors', path: '/vendor/products/:id', component: ProductColors},
    // {name: 'product-color-edit', path: '/vendor/products/:product_id/color/:color_id', component: ProductColorEdit},
    // {name: 'product-sizes', path: '/vendor/products/:product_id/color/:color_id/sizes', component: ProductSizes},
    //
    // /*Control Panel*/
    // {name: 'coupon', path: '/vendor/control/coupon', component: Coupon},
]


const adminRouter = new VueRouter({
    mode: 'history',
    routes: adminRoutes,
});

export default adminRouter;