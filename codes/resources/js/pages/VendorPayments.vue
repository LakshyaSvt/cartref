<template>
  <div>
    <div class="container mx-auto my-2 px-4">
      <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
        <i class="fi fi-rr-chart-tree-map"></i>
        <h3 class="text-start my-8">Vendor Payments</h3>
      </div>

      <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
        <div class="block">
          <form @submit.prevent="editOrCreateVendorPayment()">
            <div class="md:flex mb-3">
              <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                <label for="vendor_id" class="block mb-2 text-sm font-bold text-gray-900">Vendor <span class="text-red-600">*</span></label>
                <select v-model="vendor_id" id="vendor_id" class="form-input" required>
                  <option value="" selected>Select Vendor</option>
                  <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">{{ vendor.name }} ({{vendor.brand_name}})</option>
                </select>
              </div>
              <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                <label for="billing_date" class="block mb-2 text-sm font-bold text-gray-900">Date <span class="text-red-600">*</span></label>
                <input type="date" v-model="billing_date" id="billing_date" class="form-input" placeholder="Peter England" required>
              </div>
              <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                <label for="total" class="block mb-2 text-sm font-bold text-gray-900">Total <span class="text-red-600">*</span></label>
                <input type="number" step="0.01" v-model="total" id="total" class="form-input" placeholder="1299.50" required>
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="submit-btn">
                {{ this.editId ? 'Update' : 'Create' }}
              </button>
              <button type="button" @click="clear()"
                class="text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-300 font-bold  rounded-lg text-base mx-1 px-5 py-2.5">
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
                <button :disabled="!pagination.prev_page_url" @click="fetchVendorPayment(pagination.prev_page_url)"
                  title="Previous"
                  class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50">
                  <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                </button>
                <button :disabled="!pagination.next_page_url" @click="fetchVendorPayment(pagination.next_page_url)" title="Next"
                  class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50">
                  <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                </button>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <label for="table-search" class="sr-only">Search</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer"
                  @click="keyword = ''; fetchVendorPayment();" v-if="keyword">
                  <i class="fi fi-rr-cross-small mr-1"></i>
                </div>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" v-else>
                  <i class="fi fi-rr-search mr-1"></i>
                </div>
                <input type="text" v-model="keyword" class="search !w-40" placeholder="Search" @keydown.enter="fetchVendorPayment()">
              </div>
              <div class="flex border border-gray-600 rounded-lg bg-white">
                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                  @click="fetchVendorPayment()">
                  <i class="ffi fi-rr-refresh mr-1"></i>
                </button>
                <select class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer"
                  @change="fetchVendorPayment()" v-model="row_count">
                  <option :value="count.toLowerCase()" v-for="(count, index) in $store.state.row_counts" :key="index"
                    class="bg-white">
                    {{ count }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          <template v-if="loading">
            <Skeleton />
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
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Actions</div>
                </div>
                <div v-for="(vendor_payment, index) in vendor_payments" v-bind:key="index"
                  class="table-row table-body hover:bg-primary-100" :class="{ 'bg-primary-200': vendor_payment.id === editId }">
                  <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">{{ pagination.from + index }}</div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ vendor_payment.user ? vendor_payment.user.name : '-'  }}</div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ formatSimpleDate(vendor_payment.billing_date) }}</div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">₹{{ Number(vendor_payment.total) || '0.00' }}</div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    <StatusCheckbox :id="vendor_payment.id" :status="!!vendor_payment.status" :update="updateStatus" onTitle="Upcoming Payment" offTitle="Paid"/>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(vendor_payment.updated_at)"></div>
                    <div class="text-sm">({{ timeAgo(vendor_payment.updated_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                    <div class="flex gap-4 items-center justify-center">
                      <a href="javascript:void(0)" @click="editVendorPayment(vendor_payment.id)" type="button"
                        class="font-medium cursor-pointer text-yellow-500">
                        <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                      </a>
                      <a href="javascript:void(0)" @click="deleteVendor(vendor_payment.id)" type="button"
                        class="font-medium cursor-pointer text-red-500">
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
                <Pagination :pagination="pagination" :fetchNewData="fetchVendorPayment" />
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
import formatDate from "../plugins/formatDate";

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
      keyword: '',
      row_count: this.$store.state.defaultRowCount,
      showModal: false,
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
    updateStatus(id, status) {
      axios.put('/admin/vendor-payment/' + id, { status })
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
        }
      })
        .then(res => {
          this.loading = false;
          this.vendor_payments = res.data.data || [];
          let { data, ...pagination } = res.data;
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
