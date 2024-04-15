import VueRouter from 'vue-router'
import Dashboard from "@pages/admin/Dashboard.vue"
import Orders from "@pages/admin/Orders.vue"
import Showcases from '@pages/admin/Showcases.vue'
import DeliveryArea from '@pages/admin/DeliveryArea.vue'
import Category from "@pages/admin/Category.vue";
import SubCategory from "@pages/admin/SubCategory.vue";
import Products from "@pages/admin/Products.vue";
import ProductBulkUpload from "@pages/admin/ProductBulkUpload.vue";
import ProductColors from "@pages/admin/ProductColors.vue";
import ProductSizes from "@pages/admin/ProductSizes.vue";
import ProductColorEdit from "@pages/admin/ProductColorEdit.vue";
import ProductCreateOrEdit from "@pages/admin/ProductCreateOrEdit.vue";
import Vendors from '@pages/admin/Vendors.vue';
import VendorPayments from '@pages/admin/VendorPayments.vue';
import Contacts from "@pages/admin/Contacts.vue";
import CollectionGroups from "@pages/admin/CollectionGroups.vue";
import CollectionImages from "@pages/admin/CollectionImages.vue";
import NewsLetter from "@pages/admin/NewsLetter.vue";
import Component from '@pages/admin/Components.vue';
import HomeSliders from '@pages/admin/HomeSliders.vue';
import CategoryComponentSliders from "@pages/admin/CategoryComponentSliders.vue";
import Styles from "@pages/admin/Styles.vue";
import Brands from "@pages/admin/Brands.vue";
import Genders from "@pages/admin/Genders.vue";
import Sizes from "@pages/admin/Sizes.vue";
import Colors from "@pages/admin/Colors.vue";
import Seo from "@pages/admin/Seo.vue";
import ProductReview from "@pages/admin/ProductReview.vue";
import Coupon from "@pages/admin/Coupon.vue";
import User from "@pages/admin/User.vue";
import UserCreateOrEdit from "@pages/admin/UserCreateOrEdit.vue";
import Role from "@pages/admin/Role.vue";
import Announcement from "@pages/admin/Announcement.vue";
import Wishlist from "@pages/admin/Wishlist.vue";
import Cart from "@pages/admin/Cart.vue";
import PostCategory from "@pages/admin/PostCategory.vue";
import Post from "@pages/admin/Post.vue";
import Profile from "@pages/Profile.vue";
import Vendor from '../pages/admin/Vendor.vue'
import NormalUser from '../pages/admin/NormalUser.vue'
import Client from '../pages/admin/Client.vue'
import DeliveryBoy from '../pages/admin/DeliveryBoy.vue'
import DeliveryHead from '../pages/admin/DeliveryHead.vue'

const adminRoutes = [
    {name: 'profile', path: '/admin/profile', component: Profile},

    {name: 'dashboard', path: '/admin/dashboard', component: Dashboard},
    {name: 'orders', path: '/admin/orders', component: Orders},
    {name: 'showcases', path: '/admin/showcases/orders', component: Showcases},
    {name: 'delivery-area', path: '/admin/showcases/delivery-area', component: DeliveryArea},

    /* Product Management */
    {name: 'products', path: '/admin/products', component: Products},
    {name: 'brands', path: '/admin/product/brands', component: Brands},
    {name: 'sizes', path: '/admin/product/sizes', component: Sizes},
    {name: 'colors', path: '/admin/product/colors', component: Colors},
    {name: 'genders', path: '/admin/product/genders', component: Genders},
    {name: 'styles', path: '/admin/product/styles', component: Styles},
    {name: 'product-bulk-upload', path: '/admin/products/bulk-upload', component: ProductBulkUpload},
    {name: 'product-edit', path: '/admin/products/:id/edit', component: ProductCreateOrEdit},
    {name: 'product-create', path: '/admin/products/create', component: ProductCreateOrEdit},
    {name: 'product-colors', path: '/admin/products/:id', component: ProductColors},
    {name: 'product-color-edit', path: '/admin/products/:product_id/color/:color_id', component: ProductColorEdit},
    {name: 'product-sizes', path: '/admin/products/:product_id/color/:color_id/sizes', component: ProductSizes},
    {name: 'category', path: '/admin/product/category', component: Category},
    {name: 'sub-category', path: '/admin/product/sub-category', component: SubCategory},

    /*Vendor*/
    {name: 'vendor-payments', path: '/admin/vendor-payments', component: VendorPayments},

    /*Lead*/
    {name: 'contacts', path: '/admin/lead/contacts', component: Contacts},
    {name: 'newsletter', path: '/admin/lead/newsletter', component: NewsLetter},
    {name: 'vendor', path: '/admin/lead/vendor', component: Vendors},

    /*Collections*/
    {name: 'collection-groups', path: '/admin/collection/groups', component: CollectionGroups},
    {name: 'collection-images', path: '/admin/collection/images', component: CollectionImages},

    /* Configuration */
    {name: 'home-sliders', path: '/admin/config/home-sliders', component: HomeSliders},
    {name: 'components', path: '/admin/config/components', component: Component},
    {name: 'category-component-sliders', path: '/admin/config/category-component-sliders', component: CategoryComponentSliders},

    /*Control Panel*/
    {name: 'seo', path: '/admin/control/seo', component: Seo},
    {name: 'product-review', path: '/admin/control/product-review', component: ProductReview},
    {name: 'coupon', path: '/admin/control/coupon', component: Coupon},
    {name: 'announcement', path: '/admin/control/announcement', component: Announcement},

    /*User Management*/
    {name: 'user', path: '/admin/users', component: User},
    {name: 'user-create', path: '/admin/user/create', component: UserCreateOrEdit},
    {name: 'user-edit', path: '/admin/users/edit/:id', component: UserCreateOrEdit},
    // {name: 'role', path: '/admin/user/role', component: Role},

    /*Vendor Management*/
    {name: 'vendor-user', path: '/admin/vendors', component: Vendor},

    /*Normal User Management*/
    {name: 'normal-user', path: '/admin/normal-user', component: NormalUser},

    /*Client Management*/
    {name: 'client', path: '/admin/client', component: Client},

    /*Delivery Boy Management*/
    {name: 'delivery-boy', path: '/admin/delivery-boy', component: DeliveryBoy},

    /*Delivery Head Management*/
    {name: 'delivery-head', path: '/admin/delivery-head', component: DeliveryHead},

    /*Post & Category*/
    {name: 'post', path: '/admin/post/blog', component: Post},
    {name: 'post-category', path: '/admin/post/category', component: PostCategory},

    /* Cart & Wishlist */
    {name: 'carts', path: '/admin/carts', component: Cart},
    {name: 'wishlists', path: '/admin/wishlists', component: Wishlist}
]


const adminRouter = new VueRouter({
    mode: 'history',
    routes: adminRoutes,
});

export default adminRouter;
