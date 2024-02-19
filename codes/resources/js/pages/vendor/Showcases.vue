<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-house-chimney"></i>
            <h3 class="text-start my-8">Showcase Orders</h3>
         </div>
         <div class="bg-primary-200 p-2 overflow-x-auto shadow-md sm:rounded-lg mb-4">
            <div class="px-2 md:px-4 py-2 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-12 lg:px-8 lg:py-4">
               <h3 class="text-sm md:text-2xl text-left text-semibold mb-2 whitespace-nowrap">
                  Order Haulage
                  <i class="fi fi-rr-shipping-fast"></i>
               </h3>
               <div class="grid gap-2 grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                  <div :class="{'animate-fade border-2 !border-green-400':showcase_count.new_order > 0, 'border-2 border-primary-400': status ===  'New Order'}" class="stats-card"
                       @click="status = 'New Order'">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.new_order || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">New Orders</p>
                     </div>
                  </div>
                  <div :class="{'border-2 border-primary-400': status ===  'Accepted'}" class="stats-card" @click="status = 'Accepted'">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.accepted || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Accepted</p>
                     </div>
                  </div>
                  <div :class="{'border-2 border-primary-400': status ===  'Non Acceptance'}" class="stats-card" @click="status = 'Non Acceptance'">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.non_acceptance || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Non Acceptance</p>
                     </div>
                  </div>
                  <div :class="{'border-2 border-primary-400': status ===  'Out For Showcase'}" class="stats-card" @click="status = 'Out For Showcase'">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.pickup || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Pickup</p>
                     </div>
                  </div>
                  <div :class="{'border-2 border-primary-400': status ===  'Showcased,Moved to Bag'}" class="stats-card" @click="status = 'Showcased,Moved to Bag'">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.handover || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Handover</p>
                     </div>
                  </div>
                  <div :class="{'border-2 border-primary-400': status ===  'Purchased'}" class="stats-card" @click="status = 'Purchased'">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.purchased || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Purchased</p>
                     </div>
                  </div>
                  <div :class="{'border-2 border-primary-400': status ===  'Returned'}" class="stats-card" @click="status = 'Returned'">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.returned || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Returned</p>
                     </div>
                  </div>
                  <div :class="{'border-2 border-primary-400': status ===  'Cancelled'}" class="stats-card" @click="status = 'Cancelled'">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.cancelled || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Cancelled</p>
                     </div>
                  </div>
                  <div :class="{'border-2 border-primary-400': status ===  ''}" class="stats-card" @click="status = ''">
                     <div class="px-2 xl:px-4 py-2">
                        <div class="stats-count">
                           {{ showcase_count?.all || '0' }}
                        </div>
                        <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">All</p>
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
                        <button :disabled="!pagination.prev_page_url" class="prev-next-btn" title="Previous" @click="fetchShowcases(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn" title="Next" @click="fetchShowcases(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="relative">
                        <select v-model="status" class="filter-dropdown !w-auto" title="Status">
                           <option class="bg-gray-100" value="">All</option>
                           <option class="bg-gray-100" value="New Order">New Order</option>
                           <option class="bg-gray-100" value="Accepted">Accepted</option>
                           <option class="bg-gray-100" value="Out For Showcase">Out For Showcase</option>
                           <option class="bg-gray-100" value="Showcased,Moved to Bag">Handover</option>
                           <option class="bg-gray-100" value="Showcased">Showcased</option>
                           <option class="bg-gray-100" value="Moved to Bag">Moved to Bag</option>
                           <option class="bg-gray-100" value="Purchased">Purchased</option>
                           <option class="bg-gray-100" value="Non Acceptance">Non Acceptance</option>
                           <option class="bg-gray-100" value="Cancelled">Cancelled</option>
                           <option class="bg-gray-100" value="Returned">Returned</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div v-if="keyword" class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchShowcases();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text" @keydown.enter="fetchShowcases()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer" @click="fetchShowcases()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count" class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchShowcases()">
                           <option v-for="(count, index) in $store.state.row_counts" :key="index" :value="count.toLowerCase()" class="bg-white">
                              {{ count }}
                           </option>
                        </select>
                     </div>
                  </div>
               </div>
               <template v-if="dataLoading">
                  <Skeleton/>
               </template>
               <template v-else-if="showcases && showcases.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1 px-2">
                              Actions
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              S.no
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Order No
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Date
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Products
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Qty
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Amount
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Logistic
                              Details
                           </div>
                        </div>
                        <div v-for="(showcase, index) in showcases" :key="showcase.id" class="table-row table-body hover:bg-primary-100 bg-white">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                              <template v-if="!showcase.is_order_accepted && showcase.order_status === 'New Order' && showcase.order_status !== 'Non Acceptance'">
                                 <div class="mx-auto">
                                    <span class="text-green-600 font-semibold">New Order !!</span>
                                    <a :href="`/showcase-at-home/my-orders/order/${showcase.order_id}/accept-order`" class="inline-flex items-center gap-2 px-4 py-2 text-base font-bold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-green-500 hover:bg-green-600"
                                       target="_blank">
                                       <i class="fi fi-rr-check text-base w-4 h-5"></i>
                                       Accept
                                    </a>
                                 </div>
                              </template>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">{{ pagination.from + index }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-4 text-center">{{ showcase.order_id }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1" v-html="formDateTime(showcase.created_at)"></div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-left py-1">
                              <div v-if="showcase.product" class="flex gap-2 max-w-xl">
                                 <div class="flex gap-1 items-center text-start text-gray-900 whitespace-nowrap dark:text-white w-[70%]">
                                    <img :src="$store.state.storageUrl + showcase?.color_image" alt="product-img"
                                         class="w-14 h-14 border rounded-[50%]"
                                         @click="imageModal($store.state.storageUrl + showcase?.color_image)"
                                         @error="imageLoadError">
                                    <div class="pl-2 w-4/5">
                                       <div :title="showcase.product?.name" class="text-base font-medium overflow-hidden whitespace-nowrap text-ellipsis hover:underline">
                                          <a :href="showcase.color_link" target="_blank">
                                             {{ showcase.product.name ?? '-' }}
                                          </a>
                                       </div>
                                       <div class="font-normal text-gray-800">Size:- {{ showcase.size }}</div>
                                       <div class="font-normal text-gray-800">Color:- {{ showcase.color }}</div>
                                       <div class="font-normal text-gray-800">Brand:- {{ showcase.product.brand_id ?? '-' }}</div>
                                    </div>
                                 </div>
                                 <div>
                                    <div class="flex items-center gap-2">
                                       <div class="font-medium">SKU:</div>
                                       <div class="font-normal text-gray-800">{{ showcase.product_sku }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                       <div class="font-medium">Item Id:</div>
                                       <div class="font-normal text-gray-800">{{ showcase.product_id }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                       <div class="font-medium">HSN:</div>
                                       <div class="font-normal text-gray-800">{{ showcase.product?.productsubcategory?.hsn }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                       <div class="font-medium">Type:</div>
                                       <div class="font-normal text-gray-800">{{ showcase.type }}</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 p-1 text-center">
                              <div class="font-semibold text-gray-600">{{ showcase.qty }}</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 p-1 text-center">
                              <div class="font-semibold text-black">₹{{ showcase.product_offerprice }}</div>
                              <div class="text-base text-gray-800">{{ showcase.order_method }}</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm p-1 pb-4 text-left whitespace-nowrap">
                              <div class="flex flex-col gap-2">
                                 <div :class="'font-semibold text-base '+showcase.status_color_class">{{ showcase.new_order_status }}</div>
                                 <div v-if="showcase.order_substatus" class="font-normal text-gray-800">{{ showcase.order_substatus }}</div>
                              </div>
                              <div v-if="Number(showcase.nac_charges) > 0" class="flex flex-wrap gap-1 text-base text-red-500">
                                 <div class="font-semibold" title="Non Acceptance charges (10.35%)">NAC :-</div>
                                 <div class="font-normal">₹{{ Number(showcase.nac_charges) }}/-</div>
                              </div>
                              <!--Delivery Boy-->
                              <div v-if="showcase.deliveryboy_id && showcase.deliveryboy" class="my-2">
                                 <div class="flex flex-col">
                                    <div class="font-semibold">Delivery Boy :</div>
                                    <div class="font-normal text-gray-800">{{ showcase.deliveryboy.name }}</div>
                                 </div>
                                 <div class="flex flex-wrap items-center">
                                    <a :href="'tel:'+showcase.deliveryboy?.mobile">
                                       <i class="fi fi-sr-phone-flip"></i> +91 {{ showcase.deliveryboy?.mobile }}
                                    </a>
                                 </div>
                              </div>
                              <div v-else class="flex flex-wrap items-center gap-1">
                                 <div class="font-semibold">Delivery Boy:- Not Assigned</div>
                              </div>
                              <!--Delivery Head-->
                              <div v-if="showcase.deliveryhead_id && showcase.deliveryhead" class="my-2">
                                 <div class="flex flex-col">
                                    <div class="font-semibold">Delivery Head :</div>
                                    <div class="font-normal text-gray-800">{{ showcase.deliveryhead.name }}</div>
                                 </div>
                                 <div class="flex flex-wrap items-center">
                                    <a :href="'tel:'+showcase.deliveryhead?.mobile">
                                       <i class="fi fi-sr-phone-flip"></i> +91 {{ showcase.deliveryhead?.mobile }}
                                    </a>
                                 </div>
                              </div>
                              <div v-else class="flex flex-wrap items-center gap-2">
                                 <div class="font-semibold">Delivery Head:- Not Assigned</div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center justify-between py-4">
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
                     <Pagination :fetchNewData="fetchShowcases" :pagination="pagination"/>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Orders Found !</p>
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
      name: "Showcases",
      components: {Checkbox},
      data() {
         return {
            loading: true,
            dataLoading: true,
            showModal: false,
            showcase_count: {},
            showcases: [{}],
            imgModal: '',
            keyword: '',
            status: '',
            row_count: this.$store.state.product_filter?.row_count,
            pagination: {},
         }
      },
      watch: {
         status(newValue, oldValue) {
            this.fetchShowcases();
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
         deleteShowcase(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            this.loading = true;
            axios.delete('/vendor/showcase/' + id)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchShowcases();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchShowcases();
                })
         },
         fetchShowcases(url) {
            this.fetchShowcaseCount();
            this.dataLoading = true;
            url = url || '/vendor/showcase'
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
                   this.showcases = res.data.data;
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
         fetchShowcaseCount() {
            axios.get('/vendor/showcase/count')
                .then(res => {
                   this.showcase_count = res.data;
                })
         }
      },
      created() {
         this.fetchShowcases();
      },
   }
</script>
