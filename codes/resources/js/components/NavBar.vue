<template>
   <header class="header fixed w-screen z-20 top-0 bg-dark-1 shadow-md flex items-center justify-between p-2">
      <!-- logo -->
      <h1 class="w-4/12 flex gap-1 md:mx-4">
         <a class="block md:hidden" href="javascript:void(0)" @click="toggleSideBar()">
            <i class="fi fi-br-bars-sort text-white text-2xl h-8 p-1 hover:text-primary-500 duration-200"></i>
         </a>
         <router-link :to="{name:'dashboard'}" class="">
            <img alt="Cartrefs logo"
                 class="w-52 h-8 md:w-52 md:h-8 xl:w-48 xl:h-12" height="35" src="https://cartrefs.com/storage/settings/April2023/DRWK4IgZ3N2GnkZSiN2V.png" width="150">
         </router-link>
      </h1>

      <!-- hover:dropdown --->
      <div class="w-3/12 flex justify-end mx-4 group">
         <label class="text-white px-2 py-1 mr-2 font-semibold" v-if="$store.state.user">{{ $store.state.user.name }}</label>
         <div class="relative inline-block text-left ">
            <div class="menu-hover">
               <i class="fi fi-rr-circle-user text-white text-2xl h-3 p-1 hover:text-primary-500 duration-200"></i>
            </div>
            <div class="invisible absolute right-0 z-10 mt-0 w-40 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none group-hover:visible"
                 role="menu" tabindex="-1">
               <div class="py-1" role="none">
                  <router-link :to="{name:'profile'}" class="text-gray-700 block px-4 py-2 text-base hover:bg-primary-500 hover:text-white" role="menuitem" tabindex="-1">
                     <i class="fi fi-rr-user-pen mr-2"></i> Profile
                  </router-link>
                  <form :action="$store.state.url+'/logout'" method="POST">
                     <input name="_token" type="hidden" v-bind:value="$store.state.csrf">
                     <button class="text-gray-700 block w-full px-4 py-2 text-left text-base hover:bg-primary-500 hover:text-white" role="menuitem" tabindex="-1">
                        <i class="fi fi-rr-sign-out-alt mr-2"></i> Sign out
                     </button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </header>
</template>
<script>
   export default {
      name: "NavBar",
      data() {
         return {}
      },
      methods: {
         toggleSideBar() {
            document.getElementById('sidenav').classList.toggle('hidden');
            document.getElementById('sidenav').classList.toggle('max-md:w-64')
         }
      },
   }
</script>
