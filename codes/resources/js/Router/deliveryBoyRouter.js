import VueRouter from 'vue-router'
import Dashboard from "@pages/delivery-boy/Dashboard.vue"
import Showcases from "@pages/delivery-boy/Showcases.vue";

const adminRoutes = [
    {name: 'dashboard', path: '/delivery-boy/dashboard', component: Dashboard},
    {name: 'showcases', path: '/delivery-boy/showcases/orders', component: Showcases},
]

const adminRouter = new VueRouter({
    mode: 'history',
    routes: adminRoutes,
});

export default adminRouter;