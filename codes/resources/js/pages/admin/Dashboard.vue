<template>
   <div class="container flex flex-col mx-auto">
      <div class="w-full px-4 md:px-2">
         <div class="container flex flex-col items-center gap-16 mx-auto my-10">
            <div class="grid w-full grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
               <div class="dashboard-card">
                  <i class="fi fi-rr-box-open text-4xl w-8 h-8 text-primary-600"></i>
                  <p class="text-2xl font-extrabold text-dark-grey-900">{{ stats?.product || '0' }}</p>
                  <p class="text-3xl leading-7 text-dark-grey-600">Products</p>
                  <router-link :to="{name:'products'}" class="px-8 py-2 mt-4 text-white bg-primary-600 hover:bg-primary-700 rounded-lg text-lg">View all</router-link>
               </div>
               <div class="dashboard-card">
                  <i class="fi fi-rr-boxes text-4xl w-8 h-8 text-primary-600"></i>
                  <p class="text-2xl font-extrabold text-dark-grey-900">{{ stats?.newOrder || '0' }}</p>
                  <p class="text-3xl leading-7 text-dark-grey-600">New Orders</p>
                  <router-link :to="{name:'orders'}" class="px-8 py-2 mt-4 text-white bg-primary-600 hover:bg-primary-700 rounded-lg text-lg">View all</router-link>
               </div>
               <div class="dashboard-card">
                  <i class="fi fi-sr-boxes text-4xl w-8 h-8 text-primary-600"></i>
                  <p class="text-2xl font-extrabold text-dark-grey-900">{{ stats?.order || '0' }}</p>
                  <p class="text-3xl leading-7 text-dark-grey-600">Orders</p>
                  <router-link :to="{name:'orders'}" class="px-8 py-2 mt-4 text-white bg-primary-600 hover:bg-primary-700 rounded-lg text-lg">View all</router-link>
               </div>
               <div class="dashboard-card">
                  <i class="fi fi-rr-house-chimney text-4xl w-8 h-8 text-primary-600"></i>
                  <p class="text-2xl font-extrabold text-dark-grey-900">{{ stats?.newShowcase || '0' }}</p>
                  <p class="text-3xl leading-7 text-dark-grey-600">New Showcases</p>
                  <router-link :to="{name:'showcases'}" class="px-8 py-2 mt-4 text-white bg-primary-600 hover:bg-primary-700 rounded-lg text-lg">View all</router-link>
               </div>
               <div class="dashboard-card">
                  <i class="fi fi-sr-house-chimney text-4xl w-8 h-8 text-primary-600"></i>
                  <p class="text-2xl font-extrabold text-dark-grey-900">{{ stats?.showcase || '0' }}</p>
                  <p class="text-3xl leading-7 text-dark-grey-600">Showcases</p>
                  <router-link :to="{name:'showcases'}" class="px-8 py-2 mt-4 text-white bg-primary-600 hover:bg-primary-700 rounded-lg text-lg">View all</router-link>
               </div>
               <div class="dashboard-card">
                  <i class="fi fi-rr-users-alt text-4xl w-8 h-8 text-primary-600"></i>
                  <p class="text-2xl font-extrabold text-dark-grey-900">{{ stats?.user || '0' }}</p>
                  <p class="text-3xl leading-7 text-dark-grey-600">Users</p>
                  <router-link :to="{name:'user'}" class="px-8 py-2 mt-4 text-white bg-primary-600 hover:bg-primary-700 rounded-lg text-lg">View all</router-link>
               </div>
               <div class="dashboard-card">
                  <i class="fi fi-rr-paper-plane text-4xl w-8 h-8 text-primary-600"></i>
                  <p class="text-2xl font-extrabold text-dark-grey-900">{{ stats?.post || '0' }}</p>
                  <p class="text-3xl leading-7 text-dark-grey-600">Posts</p>
                  <router-link :to="{name:'post'}" class="px-8 py-2 mt-4 text-white bg-primary-600 hover:bg-primary-700 rounded-lg text-lg">View all</router-link>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script>
   export default {
      data() {
         return {
            stats: {}
         }
      },
      methods: {
         fetchStatsData() {
            this.loading = true;
            axios
                .get('/admin/dashboard-count')
                .then(res => {
                   this.stats = res.data;
                   this.loading = false;
                })
         }
      },
      created() {
         this.fetchStatsData();
      }
   }
</script>
