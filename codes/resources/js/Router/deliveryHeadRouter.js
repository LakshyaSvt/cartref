import VueRouter from 'vue-router'
import Dashboard from "@pages/delivery-head/Dashboard.vue"
import Showcases from "@pages/delivery-head/Showcases.vue";
import Profile from "@pages/Profile.vue";

const deliveryHeadRoutes = [
    {name: 'profile', path: '/delivery-head/profile', component: Profile},
    {name: 'dashboard', path: '/delivery-head/dashboard', component: Dashboard},
    {name: 'showcases', path: '/delivery-head/showcases/orders', component: Showcases},
]

const deliveryHeadRouter = new VueRouter({
    mode: 'history',
    routes: deliveryHeadRoutes,
});

export default deliveryHeadRouter;