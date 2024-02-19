import VueRouter from 'vue-router'
import Dashboard from "@pages/vendor/Dashboard.vue"
import Orders from "@pages/vendor/Orders.vue";
import Showcases from "@pages/vendor/Showcases.vue";
import Products from "@pages/vendor/Products.vue";
import ProductBulkUpload from "@pages/vendor/ProductBulkUpload.vue";
import ProductColors from "@pages/vendor/ProductColors.vue";
import ProductSizes from "@pages/vendor/ProductSizes.vue";
import ProductColorEdit from "@pages/vendor/ProductColorEdit.vue";
import ProductCreateOrEdit from "@pages/vendor/ProductCreateOrEdit.vue";
import VendorPayments from "@pages/vendor/VendorPayments";
import Coupon from "@pages/vendor/Coupon";
import Profile from "@pages/Profile.vue";

const vendorRoutes = [
    {name: 'profile', path: '/vendor/profile', component: Profile},

    {name: 'dashboard', path: '/vendor/dashboard', component: Dashboard},
    {name: 'orders', path: '/vendor/orders', component: Orders},
    {name: 'showcases', path: '/vendor/showcases/orders', component: Showcases},

    /* Product Management */
    {name: 'products', path: '/vendor/products', component: Products},
    {name: 'product-bulk-upload', path: '/vendor/products/bulk-upload', component: ProductBulkUpload},
    {name: 'product-edit', path: '/vendor/products/:id/edit', component: ProductCreateOrEdit},
    {name: 'product-create', path: '/vendor/products/create', component: ProductCreateOrEdit},
    {name: 'product-colors', path: '/vendor/products/:id', component: ProductColors},
    {name: 'product-color-edit', path: '/vendor/products/:product_id/color/:color_id', component: ProductColorEdit},
    {name: 'product-sizes', path: '/vendor/products/:product_id/color/:color_id/sizes', component: ProductSizes},

    /*vendor payment*/
    {name: 'vendor-payments', path: '/vendor/vendor-payments', component: VendorPayments},
    {name: 'coupon', path: '/vendor/coupon', component: Coupon},
]


const vendorRouter = new VueRouter({
    mode: 'history',
    routes: vendorRoutes,
});

export default vendorRouter;