<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-shipping-timed"></i>
            <h3 class="text-start my-8">Delivery Areas</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="block">
               <form @submit.prevent="editOrCreateDeliveryArea()">
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">City <span class="text-red-600">*</span></label>
                        <input id="city" v-model="city" class="form-input" placeholder="Agra" type="text">
                     </div>
                     <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">State <span class="text-red-600">*</span></label>
                        <input id="state" v-model="state" class="form-input" placeholder="Uttar Pradesh" type="text">
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Start at <span class="text-red-600">*</span></label>
                        <input id="start_at" v-model="start_at" class="form-input" required type="time">
                     </div>
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">End at <span class="text-red-600">*</span></label>
                        <input id="end_at" v-model="end_at" class="form-input" required type="time">
                     </div>
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Abbreviation <span class="text-red-600">*</span></label>
                        <input id="abbreviation" v-model="abbreviation" class="form-input" required type="time">
                     </div>
                  </div>
                  <div class="text-center">
                     <button class="submit-btn" type="submit">
                        {{ this.editId ? 'Update' : 'Create' }}
                     </button>
                     <button class="clear-btn" type="button" @click="clear()">
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
                                @click="fetchDeliveryArea(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn"
                                title="Next" @click="fetchDeliveryArea(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="relative">
                        <select v-model="status"
                                class="block appearance-none w-32 leading-tight h-full cursor-pointer text-black bg-white border border-gray-400 focus:outline-none hover:shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-none font-medium rounded-lg text-sm px-3 py-2"
                                title="Status"
                                @change="fetchDeliveryArea()">
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
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchDeliveryArea();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text"
                               @keydown.enter="fetchDeliveryArea()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchDeliveryArea()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count"
                                class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchDeliveryArea()">
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
               <template v-else-if="delivery_areas && delivery_areas.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              S.No.
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              City
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Abbreviation
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              State
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Timings
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Status
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Creation On
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Last Update
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Actions
                           </div>
                        </div>
                        <div v-for="(delivery_area, index) in delivery_areas" v-bind:key="index"
                             :class="{ 'bg-primary-200': delivery_area.id === editId }" class="table-row table-body hover:bg-primary-100">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">
                              {{ pagination.from + index }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              {{ delivery_area.city || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              {{ delivery_area.abbreviation || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ delivery_area.state || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ formatHourMinute(delivery_area.start_at) || '-' }} to {{ formatHourMinute(delivery_area.end_at) || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="delivery_area.id" :status="!!delivery_area.status" :update="updateStatus"/>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(delivery_area.created_at)"></div>
                              <div class="text-sm">({{ timeAgo(delivery_area.created_at) }})</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(delivery_area.updated_at)"></div>
                              <div class="text-sm">({{ timeAgo(delivery_area.updated_at) }})</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex gap-4 items-center justify-center">
                                 <a class="font-medium cursor-pointer text-yellow-500" href="#" type="button"
                                    @click="editDeliveryArea(delivery_area.id)">
                                    <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                 </a>
                                 <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                                    @click="deleteDeliveryArea(delivery_area.id)">
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
                        <Pagination :fetchNewData="fetchDeliveryArea" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Delivery Areas Found !</p>
                  </div>
               </template>
            </div>
         </div>
      </div>
   </div>
</template>

<script>
   export default {
      name: "DeliveryArea",
      data() {
         return {
            loading: false,
            dataLoading: true,
            delivery_areas: [{}],
            city: '',
            abbreviation: '',
            state: '',
            start_at: '',
            end_at: '',
            keyword: '',
            status: '',
            row_count: this.$store.state.defaultRowCount,
            pagination: {},
            editId: '',
         }
      },
      methods: {
         updateStatus(id, status) {
            axios.put('/admin/delivery-area/' + id, {status})
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   let index = this.delivery_areas.findIndex(a => a.id === id)
                   this.$set(this.delivery_areas, index, res.data.data)
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         clear() {
            this.city = '';
            this.abbreviation = '';
            this.state = '';
            this.start_at = '';
            this.end_at = '';
            this.editId = '';
            $('form').trigger("reset");
         },
         editDeliveryArea(id) {
            this.loading = true;
            axios.get('/admin/delivery-area/' + id)
                .then(res => {
                   this.editId = res.data.data.id;
                   this.city = res.data.data.city;
                   this.abbreviation = res.data.data.abbreviation;
                   this.state = res.data.data.state;
                   this.start_at = res.data.data.start_at;
                   this.end_at = res.data.data.end_at;
                   this.loading = false;
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         deleteDeliveryArea(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            this.loading = true;
            axios.delete('/admin/delivery-area/' + id)
                .then(res => {
                   this.loading = true;
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchDeliveryArea();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         editOrCreateDeliveryArea() {
            let url, data;
            if (this.editId) {
               url = '/admin/delivery-area/' + this.editId;
               data = {
                  _method: 'PUT',
                  id: this.editId,
                  city: this.city,
                  abbreviation: this.city,
                  state: this.state,
                  start_at: this.start_at,
                  end_at: this.end_at,
               }
            } else {
               url = '/admin/delivery-area'
               data = {
                  city: this.city,
                  abbreviation: this.abbreviation,
                  state: this.state,
                  start_at: this.start_at,
                  end_at: this.end_at,
               }
            }
            axios.post(url, data)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.clear();
                   this.fetchDeliveryArea();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchDeliveryArea();
                })
         },
         fetchDeliveryArea(url) {
            this.dataLoading = true;
            url = url || '/admin/delivery-area'
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
                   this.delivery_areas = res.data.data;
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
         this.fetchDeliveryArea();
      },
   }
</script>

<style lang="scss" scoped></style>
