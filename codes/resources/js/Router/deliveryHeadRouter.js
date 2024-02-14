import VueRouter from 'vue-router'
import Dashboard from "@pages/delivery-head/Dashboard.vue"
import Showcases from "@pages/delivery-head/Showcases.vue";

const adminRoutes = [
    {name: 'dashboard', path: '/delivery-head/dashboard', component: Dashboard},
    {name: 'showcases', path: '/delivery-head/showcases/orders', component: Showcases},
]

const adminRouter = new VueRouter({
    mode: 'history',
    routes: adminRoutes,
});

export default adminRouter;