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
                  <router-link :to="{ name: 'showcases' }" class="single-item">
                     <i class="fi fi-rr-house-chimney"></i>
                     <span class="">Showcases</span>
                  </router-link>
               </li>
            </ul>
         </div>
      </div>
   </div>
</template>

<script>
   export default {
      name: "DeliveryBoySideBar",
      data() {
         return {}
      },
      methods: {},
      created() {

      }
   }
</script>
<style scoped>
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
