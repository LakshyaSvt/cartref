import Dashboard from "@pages/Dashboard.vue"
import Orders from "@pages/Orders.vue"
import Showcases from '@pages/Showcases.vue'
import Wishlist from "@pages/Wishlist.vue";
import Cart from "@pages/Cart.vue";

import Category from "@pages/Category.vue";
import SubCategory from "@pages/SubCategory.vue";
import Products from "@pages/Products.vue";
import ProductColors from "@pages/ProductColors.vue";
import ProductSizes from "@pages/ProductSizes.vue";
import ProductColorEdit from "@pages/ProductColorEdit.vue";
import ProductEdit from "@pages/ProductEdit.vue";

import Component from '@pages/Components.vue';
import HomeSliders from '@pages/HomeSliders.vue';
import CategoryComponentSliders from "@pages/CategoryComponentSliders.vue";
import Styles from "@pages/Styles.vue";
import Brands from "@pages/Brands.vue";
import Genders from "@pages/Genders.vue";
import Sizes from "@pages/Sizes.vue";
import Colors from "@pages/Colors.vue";

const routes = [
    {
        name: 'dashboard',
        path: '/admin/dashboard',
        component: Dashboard,
    },
    {
        name: 'orders',
        path: '/admin/orders',
        component: Orders,
    },
    {
        name: 'showcases',
        path: '/admin/showcases',
        component: Showcases,
    },

    /* Product Management */
    {
        name: 'products',
        path: '/admin/products',
        component: Products,
    },
    {
        name: 'product-edit',
        path: '/admin/products/:id/edit',
        component: ProductEdit,
    },
    {
        name: 'product-colors',
        path: '/admin/products/:id',
        component: ProductColors,
    },
    {
        name: 'product-color-edit',
        path: '/admin/products/:product_id/color/:color_id',
        component: ProductColorEdit,
    },
    {
        name: 'product-sizes',
        path: '/admin/products/:product_id/color/:color_id/sizes',
        component: ProductSizes,
    },
    {
        name: 'category',
        path: '/admin/product/category',
        component: Category,
    },
    {
        name: 'sub-category',
        path: '/admin/product/sub-category',
        component: SubCategory,
    },

    /* Configuration */
    {
        name: 'home-sliders',
        path: '/admin/config/home-sliders',
        component: HomeSliders,
    },
    {
        name: 'components',
        path: '/admin/config/components',
        component: Component,
    },
    {
        name: 'category-component-sliders',
        path: '/admin/config/category-component-sliders',
        component: CategoryComponentSliders,
    },
    {
        name: 'brands',
        path: '/admin/config/brands',
        component: Brands,
    },
    {
        name: 'genders',
        path: '/admin/config/genders',
        component: Genders,
    },
    {
        name: 'sizes',
        path: '/admin/config/sizes',
        component: Sizes,
    },
    {
        name: 'colors',
        path: '/admin/config/colors',
        component: Colors,
    },
    {
        name: 'styles',
        path: '/admin/config/styles',
        component: Styles,
    },

    /* Cart & Wishlist */
    {
        name: 'carts',
        path: '/admin/carts',
        component: Cart,
    },
    {
        name: 'wishlists',
        path: '/admin/wishlists',
        component: Wishlist,
    }
]

export default routes;
