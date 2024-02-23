<template>
   <div>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-money-bill-wave"></i>
            <h3 class="text-start my-8">Vendor Payments</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="block">
               <form @submit.prevent="editOrCreateVendorPayment()">
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="vendor_id">Vendor <span class="text-red-600">*</span></label>
                        <select id="vendor_id" v-model="vendor_id" class="form-input" required>
                           <option selected value="">Select Vendor</option>
                           <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">{{ vendor.name }} ({{ vendor.brand_name }})</option>
                        </select>
                     </div>
                     <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="billing_date">Date <span class="text-red-600">*</span></label>
                        <input id="billing_date" v-model="billing_date" class="form-input" placeholder="Peter England" required type="date">
                     </div>
                     <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="total">Total <span class="text-red-600">*</span></label>
                        <input id="total" v-model="total" class="form-input" placeholder="1299.50" required step="0.01" type="number">
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
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-52">UTR</div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Status</div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-48">Last Update
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Actions</div>
                        </div>
                        <div v-for="(vendor_payment, index) in vendor_payments" v-bind:key="index"
                             :class="{ 'bg-primary-200': vendor_payment.id === editId }" class="table-row table-body hover:bg-primary-100">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">{{ pagination.from + index }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ vendor_payment.user ? vendor_payment.user.name : '-' }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ formatSimpleDate(vendor_payment.billing_date) }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">₹{{ Number(vendor_payment.total) || '0.00' }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ vendor_payment.utr_no || '-' }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="vendor_payment.id" :status="!!vendor_payment.status" :update="updateStatus" offTitle="Paid" onTitle="Upcoming Payment"/>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(vendor_payment.updated_at)"></div>
                              <div class="text-sm">({{ timeAgo(vendor_payment.updated_at) }})</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex gap-4 items-center justify-center">
                                 <a class="font-medium cursor-pointer text-yellow-500" href="javascript:void(0)" type="button"
                                    @click="editVendorPayment(vendor_payment.id)">
                                    <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                 </a>
                                 <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                                    @click="deleteVendor(vendor_payment.id)">
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
      <Modal :hide="closeModal" :show="showModal" title="Payment Paid">
         <!-- Modal body -->
         <form @submit.prevent="makeStatusLive()">
            <div class="py-2 px-4">
               <div class="md:flex mb-3">
                  <div class="mb-2 w-full">
                     <label class="block mb-2 text-sm font-bold text-gray-900" for="utr_no">UTR No <span class="text-red-600">*</span></label>
                     <input id="utr_no" v-model="utr_no" class="form-input" placeholder="12333XXX004567878XX" required type="text">
                  </div>
               </div>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
               <button class="submit-btn" type="submit">Submit</button>
            </div>
         </form>
      </Modal>
   </div>
</template>

<script>
   import Modal from "@components/Modal.vue";

   export default {
      name: "VendorPayments",
      components: {Modal},

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
            modal_id: '',
            utr_no: '',
            imgModal: '',
            pagination: {},
            editId: '',
         }
      },
      methods: {
         clear() {
            this.vendor_id = '';
            this.billing_date = '';
            this.total = 0.0;
            this.editId = '';
         },
         closeModal() {
            this.showModal = false;
            this.modal_id = '';

            //just refreshing that row
            this.fetchVendorPayment();
         },
         updateStatus(id, status) {
            if (status) {
               this.modal_id = id;
               this.showModal = true;
            } else {
               this.changeStatus(id, {status})
            }
         },
         makeStatusLive() {
            this.changeStatus(this.modal_id, {"status": 1, "utr_no": this.utr_no});
            this.closeModal();
         },
         changeStatus(id, data) {
            axios.put('/admin/vendor-payment/' + id, data)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   let index = this.vendor_payments.findIndex(vendor => vendor.id === id)
                   this.$set(this.vendor_payments, index, res.data.data)
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         editVendorPayment(id) {
            axios.get('/admin/vendor-payment/' + id)
                .then(res => {
                   this.editId = res.data.data.id;
                   this.vendor_id = res.data.data.vendor_id;
                   this.billing_date = res.data.data.billing_date;
                   this.total = res.data.data.total;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         deleteVendor(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            this.loading = true;
            axios.delete('/admin/vendor-payment/' + id)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchVendorPayment();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchVendorPayment();
                })
         },
         editOrCreateVendorPayment() {
            let url, data;
            data = {
               vendor_id: this.vendor_id,
               billing_date: this.billing_date,
               total: this.total,
            }
            if (this.editId) {
               url = '/admin/vendor-payment/' + this.editId;
               data._method = 'PUT';
               data.id = this.editId;
            } else {
               url = '/admin/vendor-payment'
            }
            axios.post(url, data)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.clear();
                   this.fetchVendorPayment();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchVendorPayment();
                })
         },
         fetchVendorPayment(url) {
            this.loading = true;
            url = url || '/admin/vendor-payment'
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
                   this.vendors = res.data.data;
                })
                .catch(err => {
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
