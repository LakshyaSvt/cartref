<template>
   <div class="max-h-screen bg-gray-100 z-10 fixed left-0 bottom-0 top-12 xl:top-16">
      <div id="sidenav" class="sidebar group hidden md:block">
         <div class="flex h-screen overflow-auto custom-scrollbar flex-col justify-between pb-6">
            <ul class="space-y-1 tracking-wide">
               <li class="min-w-max">
                  <router-link :to="{ name: 'dashboard' }" class="single-item">
                     <i class="fi fi-rr-dashboard"></i>
                     <span class="-mr-1 font-medium">Dashboard</span>
                  </router-link>
               </li>
               <li class="min-w-max">
                  <router-link :to="{ name: 'orders' }" class="single-item">
                     <i class="fi fi-rr-boxes"></i>
                     <span class="">Orders</span>
                  </router-link>
               </li>
               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': showcases }" class="group dropdown-btn"
                     href="javascript:void(0)" @click="showcases = !showcases;">
                     <i class="fi fi-rr-house-chimney"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                          <span>Showcases At Home</span>
                          <i v-if="showcases" class="fi fi-rr-angle-small-up mr-2"></i>
                          <i v-if="!showcases" class="fi fi-rr-angle-small-down mr-2"></i>
                      </span>
                  </a>
                  <ul v-if="showcases" class="dropdown-menu">
                     <li>
                        <router-link :to="{ name: 'showcases' }" class="dropdown-item">
                           <i class="fi fi-rr-house-chimney mr-2"></i> Showcases
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'delivery-area'}" class="dropdown-item">
                           <i class="fi fi-rr-shipping-timed mr-2"></i> Delivery Areas
                        </router-link>
                     </li>
                  </ul>
               </li>
               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': product }" class="group dropdown-btn" href="javascript:void(0)"
                     @click="product = !product;">
                     <i v-if="product" class="fi fi-rr-folder-open"></i>
                     <i v-if="!product" class="fi fi-rr-folder"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                          <span>Product Management</span>
                          <i v-if="product" class="fi fi-rr-angle-small-up mr-2"></i>
                          <i v-if="!product" class="fi fi-rr-angle-small-down mr-2"></i>
                      </span>
                  </a>
                  <ul v-if="product" class="dropdown-menu">
                     <li>
                        <router-link :to="{ name: 'products' }" class="dropdown-item">
                           <i class="fi fi-rr-box-open mr-2"></i> Products
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'category' }" class="dropdown-item">
                           <i class="fi fi-rr-chart-tree-map mr-2"></i> Category
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'sub-category' }" class="dropdown-item">
                           <i class="fi fi-rr-folder-tree mr-2"></i> Sub Category
                        </router-link>
                     </li>
                     <!-- <li>
                         <a href="#" class="dropdown-item">
                             <i class="fi fi-rr-note-medical mr-2"></i> SKU Report
                         </a>
                     </li> -->
                  </ul>
               </li>
               <!-- <li class="min-w-max">
                   <a href="#" class="single-item">
                       <i class="fi fi-rr-lightbulb-on"></i>
                       <span>Services</span>
                   </a>
               </li> -->
               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': vendor }" class="group dropdown-btn" href="javascript:void(0)"
                     @click="vendor = !vendor;">
                     <i class="fi fi-rs-users-alt"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                                <span>Vendor Management</span>
                                <i v-if="vendor" class="fi fi-rr-angle-small-up mr-2"></i>
                                <i v-if="!vendor" class="fi fi-rr-angle-small-down mr-2"></i>
                            </span>
                  </a>
                  <ul v-if="vendor" class="dropdown-menu">
                     <li>
                        <router-link :to="{name:'vendor'}" class="dropdown-item">
                           <i class="fi fi-ss-users-alt mr-2"></i> Vendors
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'vendor-payments'}" class="dropdown-item">
                           <i class="fi fi-rr-money-bill-wave mr-2"></i> Vendor Payments
                        </router-link>
                     </li>
                  </ul>
               </li>
               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': collection }" class="group dropdown-btn"
                     href="javascript:void(0)" @click="collection = !collection;">
                     <i class="fi fi-rr-gallery"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                        <span>Collections</span>
                         <i v-if="collection" class="fi fi-rr-angle-small-up mr-2"></i>
                         <i v-if="!collection" class="fi fi-rr-angle-small-down mr-2"></i>
                     </span>
                  </a>
                  <ul v-if="collection" class="dropdown-menu">
                     <li>
                        <router-link :to="{name:'collection-groups'}" class="dropdown-item">
                           <i class="fi fi-rr-folders mr-2"></i> Groups
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'collection-images'}" class="dropdown-item">
                           <i class="ffi fi-rr-gallery mr-2"></i> Collection Images
                        </router-link>
                     </li>
                  </ul>
               </li>
               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': config }" class="group dropdown-btn" href="javascript:void(0)"
                     @click="config = !config;">
                     <i class="fi fi-rr-settings"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                          <span>Configurations</span>
                          <i v-if="config" class="fi fi-rr-angle-small-up mr-2"></i>
                          <i v-if="!config" class="fi fi-rr-angle-small-down mr-2"></i>
                      </span>
                  </a>
                  <ul v-if="config" class="dropdown-menu">
                     <li>
                        <router-link :to="{ name: 'home-sliders' }" class="dropdown-item">
                           <i class="fi fi-rr-circle-h mr-2"></i> Home Sliders
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'components' }" class="dropdown-item">
                           <i class="fi fi-rs-puzzle-piece mr-2"></i> Components
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'category-component-sliders' }" class="dropdown-item">
                           <i class="fi fi-rr-settings-sliders mr-2"></i> Category Sliders
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'brands' }" class="dropdown-item">
                           <i class="fi fi-rr-circle-b mr-2"></i> Brands
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'genders' }" class="dropdown-item">
                           <i class="fi fi-rr-venus-mars mr-2"></i> Genders
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'sizes' }" class="dropdown-item">
                           <i class="fi fi-rr-ruler-combined mr-2"></i> Sizes
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'colors' }" class="dropdown-item">
                           <i class="fi fi-rr-palette mr-2"></i> Colors
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{ name: 'styles' }" class="dropdown-item">
                           <i class="fi fi-rr-circle-s mr-2"></i> Styles
                        </router-link>
                     </li>
                  </ul>
               </li>
               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': control }" class="group dropdown-btn" href="javascript:void(0)"
                     @click="control = !control;">
                     <i class="fi fi-rr-broadcast-tower"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                          <span>Control Panel</span>
                          <i v-if="control" class="fi fi-rr-angle-small-up mr-2"></i>
                          <i v-if="!control" class="fi fi-rr-angle-small-down mr-2"></i>
                      </span>
                  </a>
                  <ul v-if="control" class="dropdown-menu">
                     <li>
                        <router-link :to="{name:'seo'}" class="dropdown-item">
                           <i class="fi fi-rr-site mr-2"></i> SEO
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'product-review'}" class="dropdown-item">
                           <i class="fi fi-rr-star-sharp-half-stroke mr-2"></i> Product Reviews
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'coupon'}" class="dropdown-item">
                           <i class="fi fi-rr-scissors mr-2"></i> Coupons
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'announcement'}" class="dropdown-item">
                           <i class="fi fi-rr-megaphone mr-2"></i> Announcement
                        </router-link>
                     </li>
                  </ul>
               </li>

               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': lead }" class="group dropdown-btn" href="javascript:void(0)"
                     @click="lead = !lead;">
                     <i class="fi fi-rr-phone-call"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                          <span>Leads</span>
                          <i v-if="lead" class="fi fi-rr-angle-small-up mr-2"></i>
                          <i v-if="!lead" class="fi fi-rr-angle-small-down mr-2"></i>
                      </span>
                  </a>
                  <ul v-if="lead" class="dropdown-menu">
                     <li>
                        <router-link :to="{name:'contacts'}" class="dropdown-item">
                           <i class="fi fi-rs-customer-service mr-2"></i> Contacts
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'newsletter'}" class="dropdown-item">
                           <i class="fi fi-rs-newspaper-open mr-2"></i> Newsletters
                        </router-link>
                     </li>
                  </ul>
               </li>
               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': blog }" class="group dropdown-btn" href="javascript:void(0)"
                     @click="blog = !blog;">
                     <i class="fi fi-rr-radio-alt"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                          <span>Blogs</span>
                          <i v-if="blog" class="fi fi-rr-angle-small-up mr-2"></i>
                          <i v-if="!blog" class="fi fi-rr-angle-small-down mr-2"></i>
                      </span>
                  </a>
                  <ul v-if="blog" class="dropdown-menu">
                     <li>
                        <router-link :to="{name:'post-category'}" class="dropdown-item">
                           <i class="fi fi-rr-chart-tree mr-2"></i> Categories
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'post'}" class="dropdown-item">
                           <i class="fi fi-rr-paper-plane mr-2"></i> Posts
                        </router-link>
                     </li>
                  </ul>
               </li>
               <li class="min-w-max">
                  <a :class="{ 'bg-primary-100': user }" class="group dropdown-btn" href="javascript:void(0)"
                     @click="user = !user;">
                     <i class="fi fi-br-user-gear"></i>

                     <span class="group-hover:text-gray-700 flex justify-between w-full">
                          <span>User Management</span>
                          <i v-if="user" class="fi fi-rr-angle-small-up mr-2"></i>
                          <i v-if="!user" class="fi fi-rr-angle-small-down mr-2"></i>
                      </span>
                  </a>
                  <ul v-if="user" class="dropdown-menu">
                     <li>
                        <router-link :to="{name:'user'}" class="dropdown-item">
                           <i class="fi fi-rs-users-alt mr-2"></i> Users
                        </router-link>
                     </li>
                     <li>
                        <router-link :to="{name:'role'}" class="dropdown-item">
                           <i class="fi fi-rr-lock mr-2"></i> Roles
                        </router-link>
                     </li>
                  </ul>
               </li>
               <li class="min-w-max">
                  <router-link :to="{ name: 'carts' }" class="single-item">
                     <i class="fi fi-rr-shopping-cart"></i>
                     <span>Carts</span>
                  </router-link>
               </li>
               <li class="min-w-max">
                  <router-link :to="{ name: 'wishlists' }" class="single-item">
                     <i class="fi fi-rr-heart"></i>
                     <span>Wishlists</span>
                  </router-link>
               </li>
            </ul>
            <div class="min-w-max mb-14">
               <a class="group single-item" href="#">
                  <i class="fi fi-rr-gears text-xl w-5 h-5 "></i>
                  <span>Settings</span>
               </a>
            </div>
         </div>
      </div>
   </div>
</template>

<script>
   export default {
      name: "SideBar",
      data() {
         return {
            showcases: false,
            product: false,
            vendor: false,
            lead: false,
            collection: false,
            config: false,
            control: false,
            blog: false,
            user: false,
         }
      },
      watch: {
         $route: function () {
            this.updateNav();
         }
      },
      methods: {
         updateNav() {
            this.showcases = this.$route.path.startsWith('/admin/showcases');
            this.product = this.$route.path.startsWith('/admin/product');
            this.vendor = this.$route.path.startsWith('/admin/vendor');
            this.lead = this.$route.path.startsWith('/admin/lead');
            this.collection = this.$route.path.startsWith('/admin/collection');
            this.config = this.$route.path.startsWith('/admin/config');
            this.control = this.$route.path.startsWith('/admin/control');
            this.blog = this.$route.path.startsWith('/admin/post');
            this.user = this.$route.path.startsWith('/admin/user');
         }
      },
      created() {
         this.updateNav();
      }
   }
</script>
<style>
.router-link-exact-active,
.router-link-exact-active:hover {
   background-color: #3b82f6 !important;
   color: white !important;
}

.router-link-active,
.router-link-active:hover {
   background-color: #3b82f6 !important;
   color: white !important;
}
</style>
