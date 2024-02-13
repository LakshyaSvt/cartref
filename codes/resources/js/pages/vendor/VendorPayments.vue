<template>
   <div>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-chart-tree-map"></i>
            <h3 class="text-start my-8">Vendor Payments</h3>
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
                                @click="fetchVendorPayment(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn" title="Next"
                                @click="fetchVendorPayment(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     From
                     <div>
                        <input v-model="start_date" class="form-input !p-2" title="Choose Start Date" type="date" @change="fetchVendorPayment()">
                     </div>
                     To
                     <div>
                        <input v-model="end_date" class="form-input !p-2" title="Choose End Date" type="date" @change="fetchVendorPayment()">
                     </div>
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div v-if="keyword"
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchVendorPayment();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search !w-40" placeholder="Search" type="text" @keydown.enter="fetchVendorPayment()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchVendorPayment()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count"
                                class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchVendorPayment()">
                           <option v-for="(count, index) in $store.state.row_counts" :key="index" :value="count.toLowerCase()"
                                   class="bg-white">
                              {{ count }}
                           </option>
                        </select>
                     </div>
                  </div>
               </div>
               <template v-if="loading">
                  <Skeleton/>
               </template>
               <template v-else-if="vendor_payments && vendor_payments.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">S.No.</div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Vendor</div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">Billing Date</div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-52">Total</div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Status</div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-48">Last Update
                           </div>
                        </div>
                        <div v-for="(vendor_payment, index) in vendor_payments" v-bind:key="index"
                             :class="{ 'bg-primary-200': vendor_payment.id === editId }" class="table-row table-body hover:bg-primary-100">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">{{ pagination.from + index }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ vendor_payment.user ? vendor_payment.user.name : '-' }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ formatSimpleDate(vendor_payment.billing_date) }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">₹{{ Number(vendor_payment.total) || '0.00' }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <div>
                                 <label for="">{{ !!vendor_payment.status ? 'Paid' : 'Upcoming Payment' }}</label>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(vendor_payment.updated_at)"></div>
                              <div class="text-sm">({{ timeAgo(vendor_payment.updated_at) }})</div>
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
                        <Pagination :fetchNewData="fetchVendorPayment" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Vendor Payments Found !</p>
                  </div>
               </template>
            </div>
         </div>
      </div>
   </div>
</template>

<script>

   export default {
      name: "VendorPayments",
      data() {
         return {
            loading: true,
            vendors: [],
            vendor_payments: [],
            vendor_id: '',
            billing_date: '',
            total: 0.0,
            start_date: '',
            end_date: '',
            keyword: '',
            row_count: this.$store.state.defaultRowCount,
            showModal: false,
            imgModal: '',
            pagination: {},
            editId: '',
         }
      },
      methods: {
         fetchVendorPayment(url) {
            this.loading = true;
            url = url || '/vendor/vendor-payment'
            axios.get(url, {
               params: {
                  rows: this.row_count,
                  keyword: this.keyword.trim(),
                  status: this.status,
                  start_date: this.start_date,
                  end_date: this.end_date,
               }
            })
                .then(res => {
                   this.loading = false;
                   this.vendor_payments = res.data.data || [];
                   let {data, ...pagination} = res.data;
                   pagination.links.pop();
                   pagination.links.shift();
                   this.pagination = pagination;
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
      },
      created() {
         this.fetchVendorPayment();
         this.fetchSellers();
      },
   }
</script>
