<template>
  <div>
    <div class="container mx-auto my-2 px-4">
      <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
        <i class="fi fi-rr-chart-tree-map"></i>
        <h3 class="text-start my-8">Vendors</h3>
      </div>

      <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
        <div class="block">
          <form @submit.prevent="editOrCreateVendor()">
            <div class="md:flex mb-3">
              <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                <label for="brand_name" class="block mb-2 text-sm font-bold text-gray-900">Brand Name <span class="text-red-600">*</span></label>
                <input type="text" v-model="brand_name" id="brand_name" class="form-input" placeholder="Peter England" required>
              </div>
              <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                <label for="registered_company_name" class="block mb-2 text-sm font-bold text-gray-900">Registered Company Name <span class="text-red-600">*</span></label>
                <input type="text" v-model="registered_company_name" id="registered_company_name" class="form-input" placeholder="ABC Enterprise" required>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                <label for="contact_name" class="block mb-2 text-sm font-bold text-gray-900">Contact Name <span class="text-red-600">*</span></label>
                <input type="text" v-model="contact_name" id="contact_name" class="form-input" placeholder="John Doe" required>
              </div>
              <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                <label for="contact_number" class="block mb-2 text-sm font-bold text-gray-900">Contact Number <span class="text-red-600">*</span></label>
                <input type="text" v-model="contact_number" id="contact_number" class="form-input" placeholder="9879XXX1234" required>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                <label for="email_address" class="block mb-2 text-sm font-bold text-gray-900">Email Address <span class="text-red-600">*</span></label>
                <input type="text" v-model="email_address" id="email_address" class="form-input" placeholder="johndoe@gmail.com" required>
              </div>
              <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                <label for="gst_number" class="block mb-2 text-sm font-bold text-gray-900">GST IN <span class="text-red-600">*</span></label>
                <input type="text" v-model="gst_number" id="gst_number" class="form-input" placeholder="ABC1234XXE" required>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 w-full mx-2 my-1">
                <label for="marketplaces" class="block mb-2 text-sm font-bold text-gray-900">Listed on Marketplaces </label>
                <textarea type="text" v-model="marketplaces" id="marketplaces" class="form-input h-16" placeholder="Amazon, Flipkart, Myntra, etc"></textarea>
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="submit-btn">
                {{ this.editId ? 'Update' : 'Create' }}
              </button>
              <button type="button" @click="clear()"
                class="clear-btn">
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
                <button :disabled="!pagination.prev_page_url" @click="fetchVendor(pagination.prev_page_url)"
                  title="Previous"
                  class="prev-next-btn">
                  <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                </button>
                <button :disabled="!pagination.next_page_url" @click="fetchVendor(pagination.next_page_url)" title="Next"
                  class="prev-next-btn">
                  <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                </button>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <label for="table-search" class="sr-only">Search</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer"
                  @click="keyword = ''; fetchVendor();" v-if="keyword">
                  <i class="fi fi-rr-cross-small mr-1"></i>
                </div>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" v-else>
                  <i class="fi fi-rr-search mr-1"></i>
                </div>
                <input type="text" v-model="keyword" class="search !w-40" placeholder="Search" @keydown.enter="fetchVendor()">
              </div>
              <div class="flex border border-gray-600 rounded-lg bg-white">
                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                  @click="fetchVendor()">
                  <i class="ffi fi-rr-refresh mr-1"></i>
                </button>
                <select class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer"
                  @change="fetchVendor()" v-model="row_count">
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
          <template v-else-if="vendors && vendors.length > 0">
            <div class="clear-right overflow-x-auto">
              <div class="table border-solid border border-gray-500 w-full">
                <div class="table-row table-head">
                  <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">S.No.</div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Brand Name</div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">Contact</div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-52">Registered Company Name</div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">Listed on Marketplaces</div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">GST</div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-48">Last Update
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Actions</div>
                </div>
                <div v-for="(vendor, index) in vendors" v-bind:key="index"
                  class="table-row table-body hover:bg-primary-100" :class="{ 'bg-primary-200': vendor.id === editId }">
                  <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">{{ pagination.from + index }}</div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ vendor.brand_name }}</div>
                  <div class="table-cell border-t border-l border-gray-500 p-1 text-sm text-center">
                    <div class="flex items-center gap-2">
                      <div class="font-semibold">Name: </div>
                      <div class="font-normal text-gray-500">{{ vendor.contact_name }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                      <div class="font-semibold">Email: </div>
                      <div class="font-normal text-gray-500">{{ vendor.email_address }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                      <div class="font-semibold">Contact No: </div>
                      <div class="font-normal text-gray-500">{{ vendor.contact_number }}</div>
                    </div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ vendor.registered_company_name || '-' }}</div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1" :title="vendor?.marketplaces">
                    <template v-if="vendor.marketplaces">
                      {{ vendor.marketplaces.length > 100 ? (vendor.marketplaces.substr(0, 100) + '...') : vendor.marketplaces }}
                    </template>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ vendor.gst_number || '-' }}</div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(vendor.updated_at)"></div>
                    <div class="text-sm">({{ timeAgo(vendor.updated_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                    <div class="flex gap-4 items-center justify-center">
<!--                      <a href="javascript:void(0)" @click="editVendor(vendor.id)" type="button"-->
<!--                        class="font-medium cursor-pointer text-yellow-500">-->
<!--                        <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>-->
<!--                      </a>-->
                      <a href="javascript:void(0)" @click="deleteVendor(vendor.id)" type="button"
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
                <Pagination :pagination="pagination" :fetchNewData="fetchVendor" />
              </div>
            </div>
          </template>
          <template v-else>
            <div>
              <p class="text-center text-2xl">No Vendors Found !</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Vendors",
  data() {
    return {
      loading: true,
      vendors: [{marketplaces :'...'}],
      brand_name: '',
      contact_name: '',
      contact_number: '',
      email_address: '',
      registered_company_name: '',
      gst_number: '',
      marketplaces: '',

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
      this.brand_name = '';
      this.contact_name = '';
      this.contact_number = '';
      this.email_address = '';
      this.registered_company_name = '';
      this.gst_number = '';
      this.marketplaces = '';
      this.editId = '';
    },
    editVendor(id) {
      axios.get('/admin/vendor/' + id)
        .then(res => {
          this.editId = res.data.data.id;
          this.brand_name = res.data.data.brand_name;
          this.contact_name = res.data.data.contact_name;
          this.contact_number = res.data.data.contact_number;
          this.email_address = res.data.data.email_address;
          this.registered_company_name = res.data.data.registered_company_name;
          this.gst_number = res.data.data.gst_number;
          this.marketplaces = res.data.data.marketplaces;
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
      axios.delete('/admin/vendor/' + id)
        .then(res => {
          this.show_toast(res.data.status, res.data.msg);
          this.fetchVendor();
        })
        .catch(err => {
          err.handleGlobally && err.handleGlobally();
          this.fetchVendor();
        })
    },
    editOrCreateVendor() {
      let url, data;
      data = {
        'brand_name': this.brand_name,
        'contact_name': this.contact_name,
        'contact_number': this.contact_number,
        'email_address': this.email_address,
        'registered_company_name': this.registered_company_name,
        'gst_number': this.gst_number,
        'marketplaces': this.marketplaces,
      }
      if (this.editId) {
        url = '/admin/vendor/' + this.editId;
        data._method = 'PUT';
        data.id = this.editId;
      } else {
        url = '/admin/vendor'
      }
      axios.post(url, data)
        .then(res => {
          this.show_toast(res.data.status, res.data.msg);
          this.clear();
          this.fetchVendor();
        })
        .catch(err => {
          err.handleGlobally && err.handleGlobally();
          this.fetchVendor();
        })
    },
    fetchVendor(url) {
      this.loading = true;
      url = url || '/admin/vendor'
      axios.get(url, {
        params: {
          rows: this.row_count,
          keyword: this.keyword.trim(),
          status: this.status,
        }
      })
        .then(res => {
          this.loading = false;
          this.vendors = res.data.data || [];
          let { data, ...pagination } = res.data;
          pagination.links.pop();
          pagination.links.shift();
          this.pagination = pagination;
        })
        .catch(err => {
          this.loading = false;
          err.handleGlobally && err.handleGlobally();
        })
    }
  },
  created() {
    this.fetchVendor();
  },
}
</script>
