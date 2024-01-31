<template>
   <div>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-boxes"></i>
            <h3 class="text-start my-8">Showcase Orders</h3>
         </div>
         <div class="bg-primary-200 p-2 md:p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div
                class="flex justify-between px-2 md:px-4 py-2 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-12 lg:px-8 lg:py-4">
               <div class="w-[45%]">
                  <h3 class="text-sm md:text-2xl text-left text-semibold mb-2 whitespace-nowrap">
                     Order Manifestation
                     <i class="fi fi-rr-shipping-fast"></i>
                  </h3>
                  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              100
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">New Orders</p>
                        </div>
                     </div>
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              200
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Ready To Dispatch</p>
                        </div>
                     </div>
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              400
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Pending Pickup</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="w-[45%]">
                  <h3 class="text-sm md:text-2xl text-left text-semibold mb-2 whitespace-nowrap">
                     Order Haulage
                     <i class="fi fi-rr-person-carry-box"></i>
                  </h3>
                  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              100
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">In Transist</p>
                        </div>
                     </div>
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              200
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Delivered</p>
                        </div>
                     </div>
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              400
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Cancelled</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div
                class="flex justify-between px-2 md:px-4 py-2 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-12 lg:px-8 lg:py-4">
               <div class="w-[45%]">
                  <h3 class="text-sm md:text-2xl text-left text-semibold mb-2 whitespace-nowrap">
                     Returns
                     <i class="fi fi-rr-undo"></i>
                  </h3>
                  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              100
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">New Orders</p>
                        </div>
                     </div>
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              200
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Ready To Dispatch</p>
                        </div>
                     </div>
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              400
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Pending Pickup</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="w-[45%]">
                  <h3 class="text-sm md:text-2xl text-left text-semibold mb-2 whitespace-nowrap">
                     Journey at Cartrefs
                     <i class="fi fi-rr-route"></i>
                  </h3>
                  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              100
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">In Transist</p>
                        </div>
                     </div>
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              200
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Delivered</p>
                        </div>
                     </div>
                     <div
                         class="flex flex-col justify-between overflow-hidden text-left transition-shadow duration-200 bg-white text-black shadow-xl    rounded-xl group hover:shadow-2xl hover:shadow-primary-500">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              400
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Cancelled</p>
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
                        <button :disabled="!pagination.prev_page_url" @click="fetchShowcases(pagination.prev_page_url)"
                                title="Previous"
                                class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" @click="fetchShowcases(pagination.next_page_url)"
                                title="Next" class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="relative">
                        <select title="Status" v-model="status" @change="fetchShowcases()"
                                class="block appearance-none w-32 leading-tight h-full cursor-pointer text-black bg-white border border-gray-400 focus:outline-none hover:shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-none font-medium rounded-lg text-sm px-3 py-2">
                           <option class="bg-gray-100" value="">All</option>
                           <option class="bg-gray-100" value="New Order">New Order</option>
                           <option class="bg-gray-100" value="Accepted">Accepted</option>
                           <option class="bg-gray-100" value="Non Acceptance">Non Acceptance</option>
                           <option class="bg-gray-100" value="Cancelled">Cancelled</option>
                           <option class="bg-gray-100" value="Showcased">Showcased</option>
                           <option class="bg-gray-100" value="Out For Showcase">Out For Showcase</option>
                           <option class="bg-gray-100" value="Moved to Bag">Moved to Bag</option>
                           <option class="bg-gray-100" value="Purchased">Purchased</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <label for="table-search" class="sr-only">Search</label>
                     <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer"
                             @click="keyword = ''; fetchShowcases();" v-if="keyword">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" v-else>
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input type="text" v-model="keyword" class="search" placeholder="Search"
                               @keydown.enter="fetchShowcases()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchShowcases()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer"
                                @change="fetchShowcases()" v-model="row_count">
                           <option :value="count.toLowerCase()" v-for="(count, index) in $store.state.row_counts" :key="index"
                                   class="bg-white">
                              {{ count }}
                           </option>
                        </select>
                     </div>
                  </div>
               </div>
               <template v-if="dataLoading">
                  <Skeleton />
               </template>
               <template v-else-if="showcases && showcases.length > 0">
               <div class="clear-right overflow-x-auto">
                  <div class="table border-solid border border-gray-500 w-full">
                     <div class="table-row table-head">
                        <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1 px-2">
                           <div class="flex items-center">
                              <input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded  " type="checkbox">
                           </div>
                        </div>
                        <div class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">
                           S.No.
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
                           Customer
                           Details
                        </div>
                        <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                           Seller
                           Information
                        </div>
                        <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                           Logistic
                           Details
                        </div>
                        <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                           Actions
                        </div>
                     </div>
                     <div v-for="(showcase, index) in showcases" :key="showcase.id" class="table-row table-body hover:bg-primary-100 bg-white">
                        <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                           <div class="flex items-center">
                              <input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" type="checkbox">
                           </div>
                        </div>
                        <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">{{ pagination.from + index }}</div>
                        <div class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-4 text-center">{{ showcase.order_id }}</div>
                        <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1" v-html="formDateTime(showcase.created_at)"></div>
                        <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-left py-1">
                           <div v-if="showcase.product" class="flex gap-2 max-w-xl">
                              <div class="flex gap-1 items-center text-start text-gray-900 whitespace-nowrap dark:text-white w-[70%]">
                                 <img :src="$store.state.storageUrl + showcase.color_image" alt="product-img"
                                      class="w-14 h-14 border rounded-[50%]"
                                      @click="imageModal($store.state.storageUrl + showcase.color_image)">
                                 <div class="pl-2 w-4/5">
                                    <div :title="showcase.product?.name" class="text-base font-medium overflow-hidden whitespace-nowrap text-ellipsis hover:underline">
                                       <a :href="'/product/'+showcase.color_link" target="_blank">
                                          {{ showcase.product.name ?? '-' }}
                                       </a>
                                    </div>
                                    <div class="font-normal text-gray-800">{{ showcase.size }}</div>
                                    <div class="font-normal text-gray-800">{{ showcase.color }}</div>
                                    <div class="font-normal text-gray-800">{{ showcase.product.brand_id ?? '-' }}</div>
                                 </div>
                              </div>
                              <div>
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
                        <div class="table-cell border-t border-l border-gray-500 p-1 text-sm text-center">
                           <div class="flex items-center gap-2">
                              <div class="font-semibold">Name:</div>
                              <div class="font-normal text-gray-800">{{ showcase.customer_name }}</div>
                           </div>
                           <div class="flex items-center gap-2">
                              <div class="font-semibold">Email:</div>
                              <div class="font-normal text-gray-800">{{ showcase.customer_email }}</div>
                           </div>
                           <div class="text-gray-800 font-normal text-left">
                              <p>
                                 {{ showcase.dropoff_streetaddress1 + ' ' + showcase.dropoff_streetaddress2 + ', ' + showcase.dropoff_city + ' - ' + showcase.dropoff_pincode }} <br>
                                 {{ showcase.dropoff_state + ' - ' + showcase.dropoff_country }}
                              </p>
                           </div>
                        </div>

                        <div class="table-cell border-t border-l border-gray-500 text-sm p-1 pb-4 text-center">
                           <div class="flex items-center gap-2">
                              <div class="font-semibold">Name:</div>
                              <div class="font-normal text-gray-800">{{ showcase.vendor ? showcase.vendor.name : '-' }}</div>
                           </div>
                           <div class="flex items-center gap-2">
                              <div class="font-semibold">Brand:</div>
                              <div class="font-normal text-gray-800">{{ showcase.vendor ? showcase.vendor.brand_name : '-' }}</div>
                           </div>
                           <div class="flex items-center gap-2">
                              <div class="font-semibold">Email:</div>
                              <div class="font-normal text-gray-800">{{ showcase.vendor ? showcase.vendor.email : '-' }}</div>
                           </div>
                           <div class="flex items-center gap-2">
                              <div class="font-semibold">Mobile:</div>
                              <div class="font-normal text-gray-800"><a :href="'tel:'+showcase.vendor?.mobile">{{ showcase.vendor ? showcase.vendor.mobile : '-' }}</a></div>
                           </div>
                           <div class="text-gray-800 font-normal text-start">
                              <p>
                                 {{ showcase.dropoff_streetaddress1 + ' ' + showcase.dropoff_streetaddress2 + ', ' + showcase.dropoff_city + ' - ' + showcase.dropoff_pincode }}
                                 {{ showcase.dropoff_state + ' - ' + showcase.dropoff_country }}
                              </p>
                           </div>
                        </div>
                        <div class="table-cell border-t border-l border-gray-500 text-sm p-1 pb-4 text-left whitespace-nowrap">
                           <div class="flex flex-col gap-2">
                              <div :class="'font-semibold '+showcase.status_color_class">{{ showcase.new_order_status }}</div>
                              <div v-if="showcase.order_substatus" class="font-normal text-gray-800">{{ showcase.order_substatus }}</div>
                           </div>
                           <div class="flex flex-wrap gap-1 text-base text-red-500" v-if="Number(showcase.nac_charges) > 0" >
                              <div class="font-semibold" title="Non Acceptance charges (10.35%)">NAC :-</div>
                              <div class="font-normal">₹{{ Number(showcase.nac_charges) }}/-</div>
                           </div>
                           <div v-if="showcase.deliveryboy_id && showcase.deliveryboy" class="flex flex-wrap items-center gap-1">
                              <div class="font-semibold">Delivery Boy :</div>
                              <div class="font-normal text-gray-800">{{ showcase.deliveryboy.name }}</div>
                           </div>
                           <div v-else class="flex flex-wrap items-center gap-1">
                              <div class="font-semibold">Not Assigned</div>
                           </div>
                           <div v-if="showcase.deliveryhead_id && showcase.deliveryhead" class="flex flex-wrap items-center gap-1">
                              <div class="font-normal text-gray-800">
                                 <a :href="'tel:'+showcase.deliveryhead?.mobile">
                                    <i class="fi fi-sr-phone-flip"></i> +91 {{ showcase.deliveryhead?.mobile }}
                                 </a>
                              </div>
                           </div>
                           <div v-else class="flex flex-wrap items-center gap-2">
                              <div class="font-semibold">Not Assigned</div>
                           </div>
                        </div>
                        <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                           <div class="flex flex-col gap-2 items-center justify-center">
                              <a class="font-medium cursor-pointer text-blue-500" href="#"
                                 type="button">
                                 <i class="fi fi-rr-eye w-5 h-5 text-xl"></i>
                              </a>
                              <a class="font-medium cursor-pointer text-yellow-500" href="#"
                                 type="button">
                                 <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                              </a>
                              <a class="font-medium cursor-pointer text-red-500" href="#"
                                 type="button">
                                 <i class="fi fi-rr-trash w-5 h-5 text-xl"></i>
                              </a>
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
   export default {
      name: "Showcases",
      data() {
         return {
            loading: false,
            dataLoading: true,
            showModal: false,
            showcases: [{}],
            imgModal: '',
            keyword: '',
            status: '',
            row_count: this.$store.state.product_filter?.row_count,
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
         fetchShowcases(url) {
            this.dataLoading = true;
            url = url || '/admin/showcase'
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
         }
      },
      created() {
         this.fetchShowcases();
      },
   }
</script>
