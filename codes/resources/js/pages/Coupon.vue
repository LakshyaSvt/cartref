<template>
   <div>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-chart-tree-map"></i>
            <h3 class="text-start my-8">Coupons</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="block">
               <form @submit.prevent="editOrCreateCoupon()">
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="code">Code <span class="text-red-600">*</span></label>
                        <input id="code" v-model="code" class="form-input" placeholder="CARTREFS20" required type="text">
                     </div>
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="value">Type <span class="text-red-600">*</span></label>
                        <select v-model="type" class="form-input" required>
                           <option value="PercentageOff">Percentage Off (%)</option>
                           <option value="FixedOff">Fixed Off (₹100)</option>
                        </select>
                     </div>
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="value">Discount <span class="text-red-600">*</span></label>
                        <input id="value" v-model="value" class="form-input" placeholder="10" required step="0.1" type="number">
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="min_order_value">Min Order Value <span class="text-red-600">*</span></label>
                        <input id="min_order_value" v-model="min_order_value" class="form-input" placeholder="1000" required type="number">
                     </div>
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="from">From <span class="text-red-600">*</span></label>
                        <input id="from" v-model="from" class="form-input" placeholder="10" required type="date">
                     </div>
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="to">To <span class="text-red-600">*</span></label>
                        <input id="to" v-model="to" class="form-input" placeholder="10" required type="date">
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="description">Description <span class="text-red-600">*</span></label>
                        <textarea id="description" v-model="description" class="form-input" required></textarea>
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="user_email">User Email (Optional)</label>
                        <input id="user_email" v-model="user_email" class="form-input" placeholder="CARTREFS20" type="email">
                     </div>
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Brands <span class="text-red-600">*</span></label>
                        <Multiselect v-model="brands" :multiple="true" :options="brandDropdown" :showLabels="false" label="name" trackBy="id"/>
                     </div>
                     <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="background_color">Background Color</label>
                        <input id="background_color" v-model="background_color" class="form-input" required type="color">
                     </div>
                     <div class="mb-5 md:w-1/6 w-1/2 md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="flash_sale">All Brands </label>
                        <label class="relative inline-flex items-center cursor-pointer">
                           <input id="flash_sale" v-model="is_coupon_for_all" :checked="parseInt(is_coupon_for_all) === 1" class="sr-only peer" type="checkbox" value="1">
                           <Checkbox/>
                        </label>
                     </div>
                     <div class="mb-5 md:w-1/6 w-1/2 md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="flash_sale">Use with cashback </label>
                        <label class="relative inline-flex items-center cursor-pointer">
                           <input id="flash_sale" v-model="is_uwc" :checked="parseInt(is_uwc) === 1" class="sr-only peer" type="checkbox" value="1">
                           <Checkbox/>
                        </label>
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
                        <button :disabled="!pagination.prev_page_url" class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50"
                                title="Previous"
                                @click="fetchCoupon(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50"
                                title="Next"
                                @click="fetchCoupon(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div v-if="keyword"
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchCoupon();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text"
                               @keydown.enter="fetchCoupon()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchCoupon()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count" class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchCoupon()">
                           <option v-for="(count, index) in $store.state.row_counts" :key="index" :value="count.toLowerCase()" class="bg-white">
                              {{ count }}
                           </option>
                        </select>
                     </div>
                  </div>
               </div>
               <template v-if="loading">
                  <Skeleton/>
               </template>
               <template v-else-if="coupons && coupons.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              S.no
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              Code
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-40">
                              Type
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">
                              Discount
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">
                              Min. Value
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-40">
                              From
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                              To
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                              UWC
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                              All Brands
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                              Status
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                              Creation On
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                              Last Update
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Actions
                           </div>
                        </div>
                        <div v-for="(coupon, index) in coupons" v-bind:key="index" :class="{ 'bg-primary-200': coupon.id === editId }" class="table-row table-body hover:bg-primary-100">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">
                              {{ pagination.from + index }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              {{ coupon.code }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ coupon.type }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ coupon.value }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ coupon.min_order_value }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1" v-html="formatSimpleDate(coupon.from)"/>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1" v-html="formatSimpleDate(coupon.to)"/>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="coupon.id" :status="!!coupon.is_coupon_for_all" :update="(id, value) => { updateStatus(id, value, 'is_coupon_for_all') }"/>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="coupon.id" :status="!!coupon.is_uwc" :update="(id, value) => { updateStatus(id, value, 'is_uwc') }"/>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="coupon.id" :status="!!coupon.status" :update="(id, value) => { updateStatus(id, value, 'status') }"/>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(coupon.created_at)"></div>
                              <div class="text-sm">({{ timeAgo(coupon.created_at) }})</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(coupon.updated_at)"></div>
                              <div class="text-sm">({{ timeAgo(coupon.updated_at) }})</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex gap-4 items-center justify-center">
                                 <a class="font-medium cursor-pointer text-yellow-500" href="javascript:void(0)" type="button"
                                    @click="editCoupon(coupon.id)">
                                    <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                 </a>
                                 <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                                    @click="deleteCoupon(coupon.id)">
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
                        <Pagination :fetchNewData="fetchCoupon" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Coupons Found !</p>
                  </div>
               </template>
            </div>
         </div>
      </div>
   </div>
</template>

<script>
   import Checkbox from "../components/Checkbox.vue";

   export default {
      name: "Coupon",
      components: {Checkbox},
      data() {
         return {
            loading: true,
            coupons: [{}],
            all_brands: [],
            //filter
            code: '',
            type: 'PercentageOff',
            value: '',
            description: '',
            min_order_value: '',
            from: '',
            to: '',
            brands: [],
            background_color: '',
            is_coupon_for_all: 0,
            is_uwc: 0,
            user_email: '',
            //filter
            keyword: '',
            row_count: this.$store.state.defaultRowCount,
            pagination: {},
            editId: '',
         }
      },
      computed: {
         brandDropdown() {
            return this.all_brands.map(brand => ({
               "id": brand.id,
               "name": brand.name
            }));
         }
      },
      methods: {
         clear() {
            this.type = '';
            this.value = '';
            this.code = '';
            this.description = '';
            this.min_order_value = '';
            this.from = '';
            this.to = '';
            this.brands = [];
            this.background_color = '';
            this.is_coupon_for_all = 0;
            this.is_uwc = 0;
            this.user_email = '';
            this.editId = '';
         },
         editCoupon(id) {
            axios
                .get('/admin/coupon/' + id)
                .then(res => {
                   this.editId = res.data.data.id;
                   this.type = res.data.data.type;
                   this.value = res.data.data.value;
                   this.code = res.data.data.code;
                   this.description = res.data.data.description;
                   this.min_order_value = res.data.data.min_order_value;
                   this.from = res.data.data.from;
                   this.to = res.data.data.to;
                   this.brands = res.data.data.brands.map(brand => ({"id":brand.id, "name":brand.name}))
                   this.background_color = res.data.data.background_color;
                   this.is_coupon_for_all = res.data.data.is_coupon_for_all;
                   this.is_uwc = res.data.data.is_uwc;
                   this.user_email = res.data.data.user_email;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         updateStatus(id, value, field) {
            let data = {};
            data[field] = value;
            axios.put('/admin/coupon/' + id, data)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   let index = this.coupons.findIndex(coupon => coupon.id === id)
                   this.$set(this.coupons, index, res.data.data)
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         deleteCoupon(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            axios
                .delete('/admin/coupon/' + id)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchCoupon();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchCoupon();
                })
         },
         editOrCreateCoupon() {
            let url, data;
            data = {
               type: this.type,
               value: this.value,
               code: this.code,
               description: this.description,
               min_order_value: this.min_order_value,
               from: this.from,
               to: this.to,
               brands: this.brands.map(brand => brand.id),
               background_color: this.background_color,
               is_coupon_for_all: this.is_coupon_for_all,
               is_uwc: !!this.is_uwc,
               user_email: !!this.user_email,
            }
            if (this.editId) {
               url = '/admin/coupon/' + this.editId;
               data._method = 'PUT';
               data.id = this.editId;
            } else {
               url = '/admin/coupon'
            }
            axios.post(url, data)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.clear();
                   this.fetchCoupon();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchCoupon();
                })
         },
         fetchCoupon(url) {
            this.loading = true;
            url = url || '/admin/coupon'
            axios
                .get(url, {
                   params: {
                      rows: this.row_count,
                      keyword: this.keyword.trim()
                   }
                })
                .then(res => {
                   this.loading = false;
                   this.coupons = res.data.data || [];
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
         fetchBrand() {
            axios
                .get('/admin/brand', {
                   params: {
                      rows: 'all',
                      status: 1,
                   }
                })
                .then(res => {
                   this.all_brands = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
      },
      created() {
         this.fetchBrand();
         this.fetchCoupon();
      },
   }
</script>

<style lang="scss" scoped></style>
