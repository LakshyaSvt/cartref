<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <div class="flex flex-wrap justify-between items-center">
            <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
               <i class="fi fi-rr-box-open"></i>
               <h3 class="text-start my-8">Products</h3>

               <form id="excel-download" :action="$store.state.url+'/seller/products/action'" class="hidden" method="post">
                  <input name="_token" type="hidden" v-bind:value="$store.state.csrf">
                  <input name="action" type="hidden" value="App\Actions\Download">
                  <input class="selected_ids" name="ids" type="hidden" v-bind:value="selected_ids.toString()">
               </form>
               <a class="inline-flex items-center gap-2 px-4 py-2 mx-1 text-sm text-center text-white align-middle transition-all rounded cursor-pointer bg-amber-500 hover:bg-amber-600"
                  href="javascript:void(0)"
                  onclick="document.getElementById('excel-download').submit()">
                  <i class="fi fi-rr-download text-base w-4 h-5"></i>
                  Excel Download
               </a>
               <router-link
                   :to="{name:'product-bulk-upload'}"
                   class="inline-flex items-center gap-2 px-4 py-2 mx-1 text-sm text-center text-white align-middle transition-all rounded cursor-pointer bg-green-500 hover:bg-green-600">
                  <i class="fi fi-rr-upload text-base w-4 h-5"></i>
                  Excel Upload
               </router-link>
            </div>
            <div>
               <router-link
                   :to="{ name: 'product-create'}"
                   class="flex items-center gap-2 px-4 py-2 text-base font-bold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-gray-900 hover:bg-black"
               >
                  Add Product
                  <i class="fi fi-rr-arrow-up-right-from-square text-base w-6 h-6"></i>
               </router-link>
            </div>
         </div>
         <div class="bg-primary-200 p-2 md:p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="md:flex justify-between px-2 md:px-2 py-2 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl lg:py-4">
               <div class="">
                  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="stats-count">
                              <div class="mx-auto">{{ product_count?.total || '0' }}</div>
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Total</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="stats-count">
                              <div class="mx-auto">{{ product_count?.active || '0' }}</div>
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Active</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="stats-count">
                              <div class="mx-auto">{{ product_count?.inactive || '0' }}</div>
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">InActive</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="stats-count">
                              <div class="mx-auto">{{ product_count?.pfv || '0' }}</div>
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Pending For Verification</p>
                        </div>
                     </div>
                  </div>
               </div>
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
                        <button :disabled="!pagination.prev_page_url" class="prev-next-btn" title="Previous" @click="fetchProduct(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn" title="Next" @click="fetchProduct(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
<!--                     <div class="relative">-->
<!--                        <select v-model="seller_id" class="filter-dropdown" title="Sellers" @change="fetchProduct()">-->
<!--                           <option selected value="">All Sellers</option>-->
<!--                           <option v-for="(seller, index) in sellers" :key="index" :value="seller.id">{{ seller.name }}</option>-->
<!--                        </select>-->
<!--                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">-->
<!--                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>-->
<!--                        </div>-->
<!--                     </div>-->
                     <div class="relative">
                        <select v-model="parent_category_id" class="w-32 filter-dropdown" title="Category"
                                @change="fetchProduct()">
                           <option selected value="">All Categories</option>
                           <option v-for="(parent, index) in parent_category" :key="index" :value="parent.id">{{ parent.name }}
                           </option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <div class="relative">
                        <select v-model="status" class="w-28 filter-dropdown" title="Status" @change="fetchProduct()">
                           <option class="bg-gray-100" value="">All Status</option>
                           <option class="bg-gray-100" value="Pending For Verification">Pending For Verification</option>
                           <option class="bg-gray-100" value="Accepted">Accepted</option>
                           <option class="bg-gray-100" value="Rejected">Rejected</option>
                           <option class="bg-gray-100" value="Updated">Updated</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <div class="relative">
                        <div v-if="keyword"
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchProduct();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" title="Search" type="text"
                               @change="fetchProduct()" @keydown.enter="fetchProduct()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer" title="Reload"
                                @click="fetchProduct()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count"
                                class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer"
                                title="Page Limit" @change="fetchProduct()">
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
               <template v-else-if="products && products.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1 px-2">
                              <div class="flex items-center">
                                 <input :checked="selected_ids.length === products.length" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" type="checkbox"
                                        @change="selectAll($event)">
                              </div>
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              S.No.
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Image
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Product Information
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Price
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              QC Status
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Last Update
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Actions
                           </div>
                        </div>
                        <div v-for="(product, index) in products" v-bind:key="index"
                             class="table-row table-body hover:bg-primary-100">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                              <div class="flex items-center">
                                 <input v-model="selected_ids" :value="product.id" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" name="checkbox_input[]"
                                        type="checkbox">
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">
                              {{ pagination.from + index }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm p-1 text-center !align-middle">
                              <img v-if="product.image" :src="$store.state.storageUrl + product.image"
                                   alt="product"
                                   class="w-20 h-20 border border-gray-400 mx-auto p-1 rounded-[50%]"
                                   @click="imageModal($store.state.storageUrl + product.image)" @error="imageLoadError">
                              <p v-else class="text-center text-gray-800">--No Image--</p>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-1 text-center">
                              <div class="flex flex-col items-center text-start text-gray-900 whitespace-nowrap px-2">
                                 <div
                                     :title="product?.name"
                                     class="w-full text-base font-semibold cursor-pointer hover:underline overflow-hidden whitespace-nowrap text-ellipsis">
                                    <a :href="'/product/' + product.slug" target="_blank">
                                       {{ product?.name ? product.name.substr(0, 60) : '-' }}{{ product?.name.length > 60 ? '...' : '' }}
                                    </a>
                                 </div>
                                 <div class="flex gap-8 w-full">
                                    <div class="">
                                       <div class="flex items-center gap-2">
                                          <div class="font-medium">Category:</div>
                                          <div class="font-normal text-gray-500">{{ product.productcategory ?
                                              product.productcategory.name : '-' }}
                                          </div>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <div class="font-medium">Sub Category:</div>
                                          <div class="font-normal text-gray-500">{{ product.productsubcategory ?
                                              product.productsubcategory.name : '-' }}
                                          </div>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <div class="font-medium">Brand:</div>
                                          <div class="font-normal text-gray-500">{{ product.brand_id ?? '-' }}
                                          </div>
                                       </div>
                                    </div>
                                    <div>
                                       <div class="flex items-center gap-2">
                                          <div class="font-medium">SKU:</div>
                                          <div class="font-normal text-gray-500">{{ product.sku ?? '-' }}
                                          </div>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <div class="font-medium">HSN:</div>
                                          <div class="font-normal text-gray-500">{{ product.productsubcategory ?
                                              product.productsubcategory.hsn : '-' }}
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-2 text-center !align-middle">
                              <div class="text-base font-semibold text-black">₹{{ product.offer_price ?
                                  product.offer_price.toLocaleString("en-US") : '0.00' }}/-
                              </div>
                              <div v-if="product.mrp" class="text-sm text-gray-700">
                                 <del>₹{{ product.mrp.toLocaleString("en-US") }}</del>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div>
                                 <label>{{ product.admin_status }}</label>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(product.updated_at)">
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex gap-4 mx-2 items-center justify-center">
                                 <router-link :to="{ name: 'product-edit', params: { id: product.id } }"
                                              class="font-medium cursor-pointer text-yellow-500">
                                    <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                 </router-link>
                                 <router-link :to="{ name: 'product-colors', params: { id: product.id } }"
                                              class="font-medium cursor-pointer text-blue-500">
                                    <i class="fi fi-rr-eye w-5 h-5 text-xl"></i>
                                 </router-link>
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
                        <Pagination :fetchNewData="fetchProduct" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Products Found !</p>
                  </div>
               </template>
            </div>
         </div>
      </div>
      <ImageModal :hide="closeImageModal" :img="imgModal" :show="showModal"></ImageModal>
   </div>
</template>

<script>
   export default {
      name: "Products",
      data() {
         return {
            selected_ids: [],
            loading: false,
            dataLoading: true,
            product_count: {},
            products: [{}],
            parent_category: [],
            sub_category: [],
            sellers: [],
            parent_category_id: this.$store.state.vendor_product_filter?.parent_category_id,
            sub_category_id: this.$store.state.vendor_product_filter?.sub_category_id,
            keyword: this.$store.state.vendor_product_filter?.keyword,
            status: this.$store.state.vendor_product_filter?.status,
            row_count: this.$store.state.vendor_product_filter?.row_count,
            showModal: false,
            imgModal: '',
            pagination: {},
         }
      },
      methods: {
         selectAll(e) {
            if (e.target.checked) {
               this.selected_ids = this.products.map(product => product.id);
            } else {
               this.selected_ids = [];
            }
         },
         excelDownload() {

         },
         excelUpload() {

         },
         imageModal(img) {
            this.showModal = true;
            this.imgModal = img;
         },
         closeImageModal() {
            this.showModal = false;
         },
         handleImageChange(e) {
            let file = e.target?.files[0];
            if (file) {
               this.image = file;
            }
         },
         fetchParentCategory() {
            this.dataLoading = true;
            axios.get('/vendor/category', {
               params: {
                  rows: 'all',
                  status: 1,
               }
            })
                .then(res => {
                   this.dataLoading = false;
                   this.parent_category = res.data.data;
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchProduct(url) {
            url = url || '/vendor/product'
            this.dataLoading = true;
            let filters = {
               row_count: this.row_count,
               keyword: this.keyword.trim(),
               seller_id: this.seller_id,
               status: this.status,
               parent_category_id: this.parent_category_id,
               sub_category_id: this.sub_category_id,
            };
            this.$store.state.vendor_product_filter = filters;
            this.$store.state.vendor_product_filter.url = url;

            axios.get(url, {params: filters})
                .then(res => {
                   this.products = res.data.data || [];
                   let {data, ...pagination} = res.data;
                   pagination.links.pop();
                   pagination.links.shift();
                   this.pagination = pagination;
                   this.dataLoading = false;
                   this.loading = false;
                })
                .catch(err => {
                   this.dataLoading = false;
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchProductCount() {
            axios.get('/vendor/product/count')
                .then(res => {
                   this.product_count = res.data;
                })
         }
      },
      created() {
         this.fetchProductCount();
         this.fetchProduct(this.$store.state.vendor_product_filter.url);
         this.fetchParentCategory();
      },
   }
</script>

<style lang="scss" scoped></style>
