<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-boxes"></i>
            <h3 class="text-start my-8">Orders</h3>
            <button v-if="isSchedulePickup" class="inline-flex items-center gap-2 ml-2 px-4 py-2 text-sm text-center text-white align-middle transition-all rounded cursor-pointer bg-amber-500 hover:bg-amber-600"
                    @click="$router.go(-1)">
               <i class="fi fi-rr-truck-moving text-base w-4 h-5"></i>
               Schedule Pickup
            </button>
            <button v-if="isGenerateLabel" class="inline-flex items-center gap-2 ml-2 px-4 py-2 text-sm text-center text-white align-middle transition-all rounded cursor-pointer bg-primary-500 hover:bg-primary-600"
                    @click="$router.go(-1)">
               <i class="fi fi-rr-document text-base w-4 h-5"></i>
               Generate Label
            </button>
            <button v-if="isMarkAsShipped" class="inline-flex items-center gap-2 ml-2 px-4 py-2 text-sm text-center text-white align-middle transition-all rounded cursor-pointer bg-green-500 hover:bg-green-600"
                    @click="$router.go(-1)">
               <i class="fi fi-rr-truck-moving text-base w-4 h-5"></i>
               Mark as Shipped
            </button>
            <button v-if="isCancelShipment" class="inline-flex items-center gap-2 ml-2 px-4 py-2 text-sm text-center text-white align-middle transition-all rounded cursor-pointer bg-red-500 hover:bg-red-600"
                    @click="$router.go(-1)">
               <i class="fi fi-rr-cross-small text-base w-4 h-5"></i>
               Cancel Shipment
            </button>
         </div>
         <div class="bg-primary-200 p-2 md:p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="flex justify-between px-2 md:px-4 py-2 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-12 lg:px-8 lg:py-4">
               <div class="w-[45%]">
                  <h3 class="text-sm md:text-2xl text-left text-semibold mb-2 whitespace-nowrap">
                     Order Manifestation
                     <i class="fi fi-rr-shipping-fast"></i>
                  </h3>
                  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.new_order || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">New Orders</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.ready_to_dispatch || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Ready To Dispatch</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.pending_pickup || '0' }}
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
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.in_transit || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">In Transist</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.delivered || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Delivered</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.cancelled || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Cancelled</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="flex justify-between px-2 md:px-4 py-2 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-12 lg:px-8 lg:py-4">
               <div class="w-[45%]">
                  <h3 class="text-sm md:text-2xl text-left text-semibold mb-2 whitespace-nowrap">
                     Returns
                     <i class="fi fi-rr-undo"></i>
                  </h3>
                  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.rto || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Return To Origin</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.customer_return || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Customer Return</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.return_delivered || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Return Delivered</p>
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
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.completed_in_90_days || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Completed in 90 days</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.all || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Total Orders</p>
                        </div>
                     </div>
                     <div class="stats-card">
                        <div class="px-2 xl:px-4 py-2">
                           <div class="flex items-center w-10 h-10 rounded-full text-black bg-white text-lg font-semibold">
                              {{ order_count?.others || '0' }}
                           </div>
                           <p class="mb-2 whitespace-nowrap text-sm leading-5 text-gray-900">Others</p>
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
                        <button :disabled="!pagination.prev_page_url" class="prev-next-btn" title="Previous" @click="fetchOrders(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn" title="Next" @click="fetchOrders(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="flex items-center gap-2 w-auto mr-1">
                        <i class="fi fi-rr-trash text-xl py-2"></i>
                        <label class="block text-sm font-bold text-gray-900" for="deleted">Trashed </label>
                        <label class="relative inline-flex items-center cursor-pointer">
                           <input id="deleted" v-model="show_deleted" :checked="!!show_deleted" class="sr-only peer" type="checkbox" value="1" @change="fetchOrders()">
                           <Checkbox/>
                        </label>
                     </div>
                     <div class="relative">
                        <select v-model="status" class="filter-dropdown !w-auto" title="Status" @change="fetchOrders()">
                           <option class="bg-gray-100" value="">All</option>
                           <option class="bg-gray-100" value="New Order">New Order</option>
                           <option class="bg-gray-100" value="Ready To Dispatch">Ready To Dispatch</option>
                           <option class="bg-gray-100" value="Scheduled For Pickup">Scheduled For Pickup</option>
                           <option class="bg-gray-100" value="Shipped">Shipped</option>
                           <option class="bg-gray-100" value="Delivered">Delivered</option>
                           <option class="bg-gray-100" value="Cancelled">Cancelled</option>
                           <option class="bg-gray-100" value="RTO">RTO</option>
                           <option class="bg-gray-100" value="Requested For Return">Requested For Return</option>
                           <option class="bg-gray-100" value="Return Request Accepted">Return Request Accepted</option>
                           <option class="bg-gray-100" value="Return Request Rejected">Return Request Rejected</option>
                           <option class="bg-gray-100" value="Returned">Returned</option>
                           <option class="bg-gray-100" value="Completed">Completed</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div v-if="keyword" class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchOrders();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text" @keydown.enter="fetchOrders()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer" @click="fetchOrders()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count" class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchOrders()">
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
               <template v-else-if="orders && orders.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1 px-2">
                              <div class="flex items-center">
                                 <input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" type="checkbox">
                              </div>
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              S.No.
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Order No
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 px-8">
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
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 px-10">
                              Pickup and Delivery
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Customer
                              Details
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 px-10">
                              Seller
                              Information
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 px-10">
                              Logistic
                              Details
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 px-6">
                              Actions
                           </div>
                        </div>
                        <div v-for="(order, index) in orders" :key="order.id" class="table-row table-body hover:bg-primary-100 bg-white">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                              <div class="flex items-center">
                                 <input v-model="selected_ids" :value="order.id" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" name="checkbox_input[]" type="checkbox">
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">{{ pagination.from + index }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 relative">{{ order.order_id }}</div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center" v-html="formDateTime(order.created_at)"></div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-left py-1">
                              <div v-if="order.product" class="flex gap-2 max-w-xl">
                                 <div class="flex gap-1 items-center text-start text-gray-900 whitespace-nowrap dark:text-white w-[70%]">
                                    <img :src="$store.state.storageUrl + order.color_image" alt="img"
                                         class="w-14 h-14 border rounded-[50%]"
                                         @click="imageModal($store.state.storageUrl + order.color_image)">
                                    <div class="pl-2 w-4/5">
                                       <div :title="order.product?.name" class="text-base font-medium overflow-hidden whitespace-nowrap text-ellipsis hover:underline">
                                          <a :href="order.color_link" target="_blank">
                                             {{ order.product.name ?? '-' }}
                                          </a>
                                       </div>
                                       <div class="font-normal text-gray-800">Size:- {{ order.size }}</div>
                                       <div class="font-normal text-gray-800">Color:- {{ order.color }}</div>
                                       <div class="font-normal text-gray-800">Brand:- {{ order.product.brand_id ?? '-' }}</div>
                                    </div>
                                 </div>
                                 <div>
                                    <div class="flex items-center gap-2">
                                       <div class="font-medium">SKU:</div>
                                       <div class="font-normal text-gray-800">{{ order.product_sku }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                       <div class="font-medium">Item Id:</div>
                                       <div class="font-normal text-gray-800">{{ order.product_id }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                       <div class="font-medium">HSN:</div>
                                       <div class="font-normal text-gray-800">{{ order.product?.productsubcategory?.hsn }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                       <div class="font-medium">Type:</div>
                                       <div class="font-normal text-gray-800">{{ order.type }}</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 p-1 text-center">
                              <div class="font-semibold text-gray-600">{{ order.qty }}</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 p-1 text-center">
                              <div class="font-semibold text-black">₹{{ order.product_offerprice }}</div>
                              <div class="text-base text-gray-500">{{ order.order_method }}</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm p-1 pb-4 text-left">
                              <div class="flex flex-col gap-2">
                                 <div :class="'font-semibold text-base '+order.status_color_class">{{ order.order_status }}</div>
                                 <div v-if="order.order_substatus" class="font-semibold text-sm text-gray-800">{{ order.order_substatus }}</div>
                              </div>
                              <div v-if="order.late_fees" class="my-1" title="Late Shipment Fees" v-html="order.late_fees"></div>
                              <p v-if="order.shipping_provider">~by {{ order.shipping_provider }}</p>
                              <div v-if="order.order_awb" class="flex flex-wrap my-1">
                                 <div>AWB:-</div>
                                 <p class="text-primary-400">{{ order.order_awb }}</p>
                              </div>
                              <div v-if="order.shipping_id" class="flex flex-wrap my-1">
                                 <div>Shipping ID:-</div>
                                 <p class="text-primary-400">{{ order.shipping_id }}</p>
                              </div>
                              <div v-if="order.shipping_label" class="my-1">
                                 <i class="fi fi-rr-download text-primary-600 text-sm mr-1"></i>
                                 <a :href="order.shipping_label" class="text-primary-400 hover:text-primary-500 hover:underline">
                                    Shipping Label</a>
                              </div>
                              <div v-if="order.manifest_url" class="my-1">
                                 <i class="fi fi-rr-download text-primary-600 text-sm mr-1"></i>
                                 <a :href="order.manifest_url" class="text-primary-400 hover:text-primary-500 hover:underline">
                                    Manifest</a>
                              </div>
                              <template v-if="order.order_awb" class="my-1">
                                 <i class="fi fi-rr-download text-primary-600 text-sm mr-1"></i>
                                 <a class="text-primary-400 hover:text-primary-500 hover:underline" href="javascript:void(0)"
                                    onclick="document.getElementById('downloadtaxinvoiceForm').submit()">
                                    Tax Invoice
                                 </a>
                                 <form id="downloadtaxinvoiceForm" action="/sellers/orders/downloadtaxinvoice" class="hidden" method="post" target="_blank">
                                    <input :value="csrf" name="_token" type="hidden">
                                    <input :value="order.order_awb" name="order_awb" type="hidden">
                                 </form>
                              </template>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 p-1 text-sm text-center">
                              <div class="flex items-center gap-2">
                                 <div class="font-semibold">Name:</div>
                                 <div class="font-normal text-gray-500">{{ order.customer_name }}</div>
                              </div>
                              <div class="flex items-center gap-2">
                                 <a :href="'mailto:'+order?.customer_email" class="hover:underline">
                                    <i class="fi fi-sr-envelope mr-2"></i>{{ order.customer_email || '-' }}
                                 </a>
                              </div>
                              <div class="text-gray-500 font-normal text-left">
                                 <p>
                                    <span class="font-semibold text-black">Address: </span>
                                    {{ order.dropoff_streetaddress1 + ' ' + order.dropoff_streetaddress2 + ', ' + order.dropoff_city + ' - ' + order.dropoff_pincode }} <br>
                                    {{ order.dropoff_state + ' - ' + order.dropoff_country }}
                                 </p>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm p-1 pb-4 text-center">
                              <div class="flex items-center gap-2">
                                 <div class="font-semibold">Name:</div>
                                 <div class="font-normal text-gray-800">{{ order.vendor ? order.vendor.name : '-' }}</div>
                              </div>
                              <div class="flex items-center gap-2">
                                 <div class="font-semibold">Brand:</div>
                                 <div class="font-normal text-gray-800">{{ order.vendor ? order.vendor.brand_name : '-' }}</div>
                              </div>
                              <div class="flex items-center gap-2">
                                 <div class="font-normal text-gray-800">
                                    <a :href="'mailto:'+order.vendor?.email" class="hover:underline">
                                       <i class="fi fi-sr-envelope mr-2"></i>{{ order.vendor ? order.vendor.email : '-' }}
                                    </a>
                                 </div>
                              </div>
                              <div class="flex items-center gap-2">
                                 <div class="font-normal text-gray-800">
                                    <a :href="'tel:+91'+order.vendor?.mobile" class="hover:underline">
                                       <i class="fi fi-sr-phone-flip"></i> +91 {{ order.vendor ? order.vendor.mobile : '-' }}
                                    </a>
                                 </div>
                              </div>
                              <div class="text-gray-800 font-normal text-start">
                                 <p>
                                    <span class="font-semibold text-black">Address: </span>
                                    {{ order.dropoff_streetaddress1 + ' ' + order.dropoff_streetaddress2 + ', ' + order.dropoff_city + ' - ' + order.dropoff_pincode }}
                                    {{ order.dropoff_state + ' - ' + order.dropoff_country }}
                                 </p>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm p-1 pb-4 text-left">
                              <div class="text-gray-500 font-normal text-start">
                                 <div class="flex flex-wrap my-1">
                                    <strong class="text-black font-bold">Delivery Date: </strong>
                                    <p>{{ formatSimpleDate(order.exp_delivery_date) }}</p>
                                 </div>
                                 <div class="flex flex-wrap my-1">
                                    <strong class="text-black font-bold">Pickup Scheduled Date: </strong>
                                    <p>{{ formatSimpleDate(order.pickup_scheduled_date) }}</p>
                                 </div>
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex flex-col gap-2 items-center justify-center">
                                 <button class="font-medium cursor-pointer text-red-500" type="button" @click="deleteOrder(order.id)">
                                    <i class="fi fi-rr-trash w-5 h-5 text-xl"></i>
                                 </button>
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
                     <Pagination :fetchNewData="fetchOrders" :pagination="pagination"/>
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
   import Checkbox from "../components/Checkbox.vue";

   export default {
      name: "Orders",
      components: {Checkbox},
      data() {
         return {
            //csrf token
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            dataLoading: true,
            showModal: false,
            order_count: {},
            orders: [{}],
            imgModal: '',
            show_deleted: 0,
            keyword: '',
            status: '',
            row_count: this.$store.state.product_filter?.row_count,
            pagination: {},
            selected_ids: [],
         }
      },
      computed: {
         isSchedulePickup() {
            if (this.selected_ids.length <= 0) {
               return false;
            }
            let statuses = ['New Order', 'Under Manufacturing'];
            let flag = true;
            this.orders
                .filter(order => this.selected_ids.includes(order.id))
                .forEach(order => {
                   if (!statuses.includes(order.order_status)) {
                      flag = false;
                   }
                })
            return flag;
         },
         isGenerateLabel() {
            if (this.selected_ids.length <= 0) {
               return false;
            }
            let statuses = ['Scheduled For Pickup'];
            let flag = true;
            this.orders
                .filter(order => this.selected_ids.includes(order.id))
                .forEach(order => {
                   if (!statuses.includes(order.order_status)) {
                      flag = false;
                   }
                })
            return flag;
         },
         isCancelShipment() {
            if (this.selected_ids.length <= 0) {
               return false;
            }
            let statuses = ['Scheduled For Pickup','Ready to Dispatch'];
            let flag = true;
            this.orders
                .filter(order => this.selected_ids.includes(order.id))
                .forEach(order => {
                   if (!statuses.includes(order.order_status)) {
                      flag = false;
                   }
                })
            return flag;
         },
         isMarkAsShipped(){
            if (this.selected_ids.length <= 0) {
               return false;
            }
            let statuses = ['Ready to dispatch'];
            let flag = true;
            this.orders
                .filter(order => this.selected_ids.includes(order.id))
                .forEach(order => {
                   if (!statuses.includes(order.order_status)) {
                      flag = false;
                   }
                })
            return flag;
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
         deleteOrder(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            this.loading = true;
            axios.delete('/admin/order/' + id)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchOrders();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchOrders();
                })
         },
         fetchOrders(url) {
            this.dataLoading = true;
            url = url || '/admin/order'
            axios.get(url, {
               params: {
                  row_count: this.row_count,
                  keyword: this.keyword.trim(),
                  status: this.status,
                  show_deleted: Number(this.show_deleted),
               }
            })
                .then(res => {
                   this.dataLoading = false;
                   this.loading = false;
                   this.orders = res.data.data;
                   let {data, ...pagination} = res.data;
                   pagination.links.pop();
                   pagination.links.shift();
                   this.pagination = pagination;
                   this.selected_ids = [];
                })
                .catch(err => {
                   this.dataLoading = false;
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchOrderCount() {
            axios.get('/admin/order/count')
                .then(res => {
                   this.order_count = res.data;
                })
         }
      },
      created() {
         this.fetchOrders();
         this.fetchOrderCount();
      },
   }
</script>