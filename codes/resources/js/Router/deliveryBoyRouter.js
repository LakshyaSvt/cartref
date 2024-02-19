import VueRouter from 'vue-router'
import Dashboard from "@pages/delivery-boy/Dashboard.vue"
import Showcases from "@pages/delivery-boy/Showcases.vue";
import Profile from "@pages/Profile.vue";

const deliveryBoyRoutes = [
    {name: 'profile', path: '/delivery-boy/profile', component: Profile},
    {name: 'dashboard', path: '/delivery-boy/dashboard', component: Dashboard},
    {name: 'showcases', path: '/delivery-boy/showcases/orders', component: Showcases},
]

const deliveryBoyRouter = new VueRouter({
    mode: 'history',
    routes: deliveryBoyRoutes,
});

export default deliveryBoyRouter;