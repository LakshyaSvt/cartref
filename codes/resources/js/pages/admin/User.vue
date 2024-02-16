<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rs-users-alt"></i>
            <h3 class="text-start my-8">User</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg">
            <div class="block">
               <div class="flex flex-wrap items-center justify-between py-4">
                  <div class="flex flex-wrap text-base text-gray-700 gap-2">
                     <div>
                        {{ pagination.from || '0' }} - {{ pagination.to || '0' }} of {{ pagination.total || '0' }}
                     </div>
                     <div>
                        <button :disabled="!pagination.prev_page_url" class="prev-next-btn"
                                title="Previous"
                                @click="fetchUser(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn"
                                title="Next" @click="fetchUser(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="relative">
                        <select v-model="status"
                                class="block appearance-none w-32 leading-tight h-full cursor-pointer text-black bg-white border border-gray-400 focus:outline-none hover:shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-none font-medium rounded-lg text-sm px-3 py-2"
                                title="Status"
                                @change="fetchUser()">
                           <option class="bg-gray-100" value="">All</option>
                           <option class="bg-gray-100" value="1">Published</option>
                           <option class="bg-gray-100" value="0">Un Published</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div v-if="keyword"
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchUser();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text"
                               @keydown.enter="fetchUser()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchUser()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count"
                                class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchUser()">
                           <option v-for="(count, index) in $store.state.row_counts" :key="index" :value="count.toLowerCase()"
                                   class="bg-white">
                              {{ count }}
                           </option>
                        </select>
                     </div>
                  </div>
               </div>
               <template v-if="dataLoading">
                  <Skeleton/>
               </template>
               <template v-else-if="users && users.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                              <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1 px-2">
                                 <div class="flex items-center">
                                    <input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" type="checkbox">
                                 </div>
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">
                                 S.No.
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 px-4 w-44">
                                 Avatar
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Role
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Name
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Email
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Mobile
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Brand
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Company Name
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 GST IN
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 City
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Showcase At Home
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Status
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-36">
                                 Last Update
                              </div>
                              <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                 Actions
                              </div>
                           </div>
                        <div v-for="(user, index) in users" v-bind:key="index" class="table-row table-body hover:bg-primary-100">
                              <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                                 <div class="flex items-center">
                                    <input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" type="checkbox">
                                 </div>
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">
                                 {{ pagination.from + index }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm p-1 px-2 text-center !align-middle">
                                 <img v-if="user.avatar"
                                      :alt="user.avatar"
                                      :src="$store.state.storageUrl + user.avatar"
                                      class="w-20 h-16 border border-gray-400 mx-auto p-1 rounded-[50%]" @click="imageModal($store.state.storageUrl + user.avatar)">
                                 <p v-else class="text-center text-gray-800">--No Image--</p>
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                                 {{ user.role ? user.role.display_name : '-' }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 {{ user.name || '-' }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 {{ user.email || '-' }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 {{ user.mobile || '-' }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 {{ user.brand_name || '-' }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 {{ user.company_name || '-' }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 {{ user.gst_number || '-' }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 {{ user.city || '-' }}
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 <StatusCheckbox :id="user.id" :status="!!user.showcase_at_home" :update="(id, value) => { updateStatus(id, value, 'showcase_at_home') }"/>
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                 <StatusCheckbox :id="user.id" :status="!!user.status" :update="updateStatus"/>
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                                 <div class="font-normal text-gray-900" v-html="formDateTime(user.updated_at)"></div>
                                 <div class="text-sm">({{ timeAgo(user.updated_at) }})</div>
                              </div>
                              <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                                 <div class="flex gap-4 items-center justify-center">
                                    <a class="font-medium cursor-pointer text-yellow-500" href="#" type="button">
                                       <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                    </a>
                                    <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                                       @click="deleteUser(user.id)">
                                       <i class="fi fi-rr-trash w-5 h-5 text-xl"></i>
                                    </a>
                                 </div>
                              </div>
                           </div>
                     </div>
                  </div>
                  <div class="flex items-center justify-between py-4">
                     <div>
                        <p class="text-base text-gray-700">
                           Showing
                           <span class="font-medium">{{ pagination.from || '0' }}</span>
                           to
                           <span class="font-medium">{{ pagination.to || '0' }}</span>
                           of
                           <span class="font-medium">{{ pagination.total || '0' }}</span>
                           results
                        </p>
                     </div>
                     <Pagination :fetchNewData="fetchUser" :pagination="pagination"/>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Users Found !</p>
                  </div>
               </template>
            </div>
         </div>
      </div>
      <ImageModal :hide="closeImageModal" :img="imgModal" :show="showModal"></ImageModal>
   </div>
</template>

<script>
   import Checkbox from "@components/Checkbox.vue";

   export default {
      name: "User",
      components: {Checkbox},
      data() {
         return {
            loading: false,
            dataLoading: true,
            showModal: false,
            imgModal: '',
            users: [{}],
            roles: [],
            keyword: '',
            status: '',
            row_count: this.$store.state.defaultRowCount,
            pagination: {},
         }
      },
      methods: {
         imageModal(img) {
            this.showModal = true;
            this.imgModal = img;
         },
         closeImageModal() {
            this.showModal = false;
         },
         updateStatus(id, status, field = 'status') {
            axios.put('/admin/user/' + id, {field: status})
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   let index = this.users.findIndex(a => a.id === id)
                   this.$set(this.users, index, res.data.data)
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         deleteUser(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            this.loading = true;
            axios.delete('/admin/user/' + id)
                .then(res => {
                   this.loading = true;
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchUser();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchUser(url) {
            this.dataLoading = true;
            url = url || '/admin/user'
            axios.get(url, {
               params: {
                  rows: this.row_count,
                  keyword: this.keyword.trim(),
                  status: this.status,
               }
            })
                .then(res => {
                   this.dataLoading = false;
                   this.loading = false;
                   this.users = res.data.data;
                   let {data, ...pagination} = res.data;
                   pagination.links.pop();
                   pagination.links.shift();
                   this.pagination = pagination;
                })
                .catch(err => {
                   this.dataLoading = false;
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchRole(url) {
            this.dataLoading = true;
            url = url || '/admin/role'
            axios.get(url, {
               params: {
                  rows: 'all',
               }
            })
                .then(res => {
                   this.dataLoading = false;
                   this.loading = false;
                   this.roles = res.data.data;
                   let {data, ...pagination} = res.data;
                   pagination.links.pop();
                   pagination.links.shift();
                   this.pagination = pagination;
                })
                .catch(err => {
                   this.dataLoading = false;
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         }
      },
      created() {
         this.fetchRole();
         this.fetchUser();
      },
   }
</script>

<style lang="scss" scoped>
.table-head-wrapper {
   position: sticky;
   top: 0;
   z-index: 1; /* Ensure it appears above other content */
}

.table-body-wrapper {
   /* Adjust the top margin to accommodate the sticky header */
   margin-top: 12rem;/* Height of the sticky header */
}
</style>
