import VueRouter from 'vue-router'
import Dashboard from "@pages/vendor/Dashboard.vue"

const adminRoutes = [
    {name: 'dashboard', path: '/vendor/dashboard', component: Dashboard},
    // {name: 'orders', path: '/admin/orders', component: Orders},
    // {name: 'showcases', path: '/admin/showcases/orders', component: Showcases},

    // /* Product Management */
    // {name: 'products', path: '/admin/products', component: Products},
    // {name: 'product-bulk-upload', path: '/admin/products/bulk-upload', component: ProductBulkUpload},
    // {name: 'product-edit', path: '/admin/products/:id/edit', component: ProductCreateOrEdit},
    // {name: 'product-create', path: '/admin/products/create', component: ProductCreateOrEdit},
    // {name: 'product-colors', path: '/admin/products/:id', component: ProductColors},
    // {name: 'product-color-edit', path: '/admin/products/:product_id/color/:color_id', component: ProductColorEdit},
    // {name: 'product-sizes', path: '/admin/products/:product_id/color/:color_id/sizes', component: ProductSizes},
    //
    // /*Control Panel*/
    // {name: 'coupon', path: '/admin/control/coupon', component: Coupon},
]


const adminRouter = new VueRouter({
    mode: 'history',
    routes: adminRoutes,
});

export default adminRouter;