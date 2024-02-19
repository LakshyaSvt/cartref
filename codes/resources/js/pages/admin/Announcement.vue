<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-folder-tree"></i>
            <h3 class="text-start my-8">Announcement</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="block">
               <form @submit.prevent="editOrCreateAnnouncement()">
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-2/5 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Priority <span class="text-red-600">*</span></label>
                        <select v-model="priority" class="form-input" required>
                           <option selected value="high">High</option>
                           <option value="regular">Regular</option>
                           <option value="low">Low</option>
                        </select>
                     </div>
                     <div class="mb-5 md:w-2/5 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="sellers">Sellers <span class="text-red-600">*</span></label>
                        <Multiselect v-model="sellers" :multiple="true" :options="sellerDropdown" :showLabels="false" label="name" trackBy="id"/>
                     </div>
                     <div class="mb-5 w-1/5 md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="for_all_vendors">All Sellers </label>
                        <label class="relative inline-flex items-center cursor-pointer">
                           <input id="for_all_vendors" v-model="for_all_vendors" :checked="!!for_all_vendors" class="sr-only peer" type="checkbox" value="1">
                           <Checkbox/>
                        </label>
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="message">Message <span class="text-red-600">*</span></label>
                        <textarea id="message" v-model="message" class="form-input" required></textarea>
                     </div>
                  </div>
                  <div class="text-center">
                     <button class="submit-btn" type="submit">
                        {{ this.editId ? 'Update' : 'Create' }}
                     </button>
                     <button class="clear-btn" type="button"
                             @click="clear()">
                        Clear
                     </button>
                  </div>
               </form>
            </div>
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
                                @click="fetchAnnouncement(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn"
                                title="Next" @click="fetchAnnouncement(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="relative">
                        <select v-model="status"
                                class="block appearance-none w-32 leading-tight h-full cursor-pointer text-black bg-white border border-gray-400 focus:outline-none hover:shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-none font-medium rounded-lg text-sm px-3 py-2"
                                title="Status"
                                @change="fetchAnnouncement()">
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
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchAnnouncement();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text"
                               @keydown.enter="fetchAnnouncement()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchAnnouncement()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count"
                                class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchAnnouncement()">
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
               <template v-else-if="announcements && announcements.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              S.No.
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">
                              Sellers
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-56">
                              Message
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Priority
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Status
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Last Update
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Actions
                           </div>
                        </div>
                        <div v-for="(announcement, index) in announcements" v-bind:key="index"
                             :class="{ 'bg-primary-200': announcement.id === editId }" class="table-row table-body hover:bg-primary-100">

                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">
                              {{ pagination.from + index }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-1 text-center">
                              <div v-if="announcement?.users && announcement.users.length > 0" class="flex flex-wrap px-2 py-1 gap-1 justify-center">
                                 <div v-for="seller in announcement.users" :key="seller.id"
                                      class="text-xs rounded-lg text-white p-1 bg-primary-500 px-1 m-auto w-auto">
                                    <span>{{ seller.name }}</span>
                                 </div>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              {{ announcement.message || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ announcement.priority || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="announcement.id" :status="!!announcement.is_active" :update="updateStatus"/>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(announcement.updated_at)"></div>
                              <div class="text-sm">({{ timeAgo(announcement.updated_at) }})</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex gap-4 items-center justify-center">
                                 <a class="font-medium cursor-pointer text-yellow-500" href="#" type="button"
                                    @click="editAnnouncement(announcement.id)">
                                    <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                 </a>
                                 <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                                    @click="deleteAnnouncement(announcement.id)">
                                    <i class="fi fi-rr-trash w-5 h-5 text-xl"></i>
                                 </a>
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
                        <Pagination :fetchNewData="fetchAnnouncement" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Announcements Found !</p>
                  </div>
               </template>
            </div>
         </div>
      </div>
   </div>
</template>

<script>
   import Checkbox from "@components/Checkbox.vue";

   export default {
      name: "Announcement",
      components: {Checkbox},
      data() {
         return {
            loading: false,
            dataLoading: true,
            all_sellers: [],
            announcements: [{}],
            sellers: [],
            priority: 'high',
            for_all_vendors: 0,
            message: '',
            keyword: '',
            status: '',
            row_count: this.$store.state.defaultRowCount,
            pagination: {},
            editId: '',
         }
      },
      computed: {
         sellerDropdown() {
            return this.all_sellers.map(seller => ({id: seller.id, name: seller.name}))
         }
      },
      methods: {
         updateStatus(id, status) {
            axios.put('/admin/announcement/' + id, {is_active: status})
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   let index = this.announcements.findIndex(a => a.id === id)
                   this.$set(this.announcements, index, res.data.data)
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         clear() {
            this.message = '';
            this.priority = '';
            this.editId = '';
            this.sellers = [];
            this.for_all_vendors = 0;
            $('form').trigger("reset");
         },
         editAnnouncement(id) {
            this.loading = true;
            axios.get('/admin/announcement/' + id)
                .then(res => {
                   this.editId = res.data.data.id;
                   this.message = res.data.data.message;
                   this.priority = res.data.data.priority;
                   this.for_all_vendors = res.data.data.for_all_vendors;
                   this.sellers = res.data.data.users.map(user => ({id: user.id, name: user.name}));
                   this.loading = false;
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         deleteAnnouncement(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            this.loading = true;
            axios.delete('/admin/announcement/' + id)
                .then(res => {
                   this.loading = true;
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchAnnouncement();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         editOrCreateAnnouncement() {
            let url, data;
            let sellers = this.sellers.map(seller => seller.id)
            if (this.editId) {
               url = '/admin/announcement/' + this.editId;
               data = {
                  _method: 'PUT',
                  id: this.editId,
                  message: this.message.trim(),
                  priority: this.priority,
                  sellers: sellers,
                  for_all_vendors: !!this.for_all_vendors,
               }
            } else {
               url = '/admin/announcement'
               data = {
                  message: this.message.trim(),
                  priority: this.priority,
                  sellers: sellers,
                  for_all_vendors: !!this.for_all_vendors,
               }
            }
            axios.post(url, data)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.clear();
                   this.fetchAnnouncement();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchAnnouncement();
                })
         },
         fetchSellers() {
            axios
                .get('/admin/user', {
                   params: {
                      rows: 'all',
                      status: 1,
                      roles: JSON.stringify(["Vendor"])
                   }
                })
                .then(res => {
                   this.all_sellers = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchAnnouncement(url) {
            this.dataLoading = true;
            url = url || '/admin/announcement'
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
                   this.announcements = res.data.data;
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
         this.fetchSellers();
         this.fetchAnnouncement();
      },
   }
</script>

<style lang="scss" scoped></style>
