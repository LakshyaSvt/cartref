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
                           <i class="fi fi-rr-house-chimney mr-2"></i> Showcasess
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
                        <router-link :to="{name:'announcement'}" class="dropdown-item">
                           <i class="fi fi-rr-megaphone mr-2"></i> Announcement
                        </router-link>
                     </li>
                  </ul>
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
      name: "VendorSideBar",
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
            this.showcases = this.$route.path.startsWith('/vendor/showcases');
            this.product = this.$route.path.startsWith('/vendor/product');
            this.vendor = this.$route.path.startsWith('/vendor/vendor');
            this.lead = this.$route.path.startsWith('/vendor/lead');
            this.collection = this.$route.path.startsWith('/vendor/collection');
            this.config = this.$route.path.startsWith('/vendor/config');
            this.control = this.$route.path.startsWith('/vendor/control');
            this.blog = this.$route.path.startsWith('/vendor/blog');
            this.user = this.$route.path.startsWith('/vendor/user');
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
