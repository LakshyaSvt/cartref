<template>
   <div>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-shopping-cart"></i>
            <h3 class="text-start my-8">Carts</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg">
            <div class="block">
               <div class="flex flex-wrap items-center justify-between py-4">
                  <div class="flex flex-wrap text-base text-gray-700 gap-2">
                     <div>
                        {{ pagination.from || '0' }} - {{ pagination.to || '0' }} of {{ pagination.total || '0' }}
                     </div>
                     <div>
                        <button :disabled="!pagination.prev_page_url" class="prev-next-btn" title="Previous" @click="fetchCart(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn" title="Next" @click="fetchCart(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search"
                               placeholder="Search"
                               type="text" @keydown.enter="fetchWishlist()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchCart()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select
                            v-model="row_count"
                            class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchCart()">
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
               <template v-else-if="carts && carts.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">S.No.</div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Customer
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Cart
                              Items
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Date</div>
                        </div>
                        <div v-for="(wishlist, index) in carts" v-bind:key="index"
                             class="table-row table-body hover:bg-primary-100 bg-white">

                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">{{
                                 index + 1
                              }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              <template v-if="wishlist.user">
                                 <div class="mx-2">{{ wishlist.user.name }}</div>
                                 <div class="whitespace-nowrap">
                                    <i class="fi fi-sr-envelope"></i> {{ wishlist.user.email }}
                                 </div>
                                 <div v-if="wishlist.user.mobile" class="whitespace-nowrap">
                                    <i class="fi fi-sr-phone-flip"></i> +91 {{ wishlist.user.mobile }}
                                 </div>
                              </template>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-left py-1">
                              <template v-if="wishlist.wishlist_data.data">
                                 <div v-for="(wishlist, index) in wishlist.wishlist_data.data" v-bind:key="index" class="flex gap-2 my-2">
                                    <div
                                        class="flex gap-1 items-center text-start text-gray-900">
                                       <img
                                           :src="$store.state.storageUrl + wishlist.product.image"
                                           alt="Shoes image"
                                           class="w-14 h-14 border border-gray-400 p-1 rounded-[50%]"
                                           @click="imageModal($store.state.storageUrl + wishlist.product.image)"
                                           @error="imageLoadError">
                                       <div class="pl-2">
                                          <div class="text-base font-medium overflow-hidden w-100 hover:underline">
                                             <a :href="'/product/'+wishlist.product.slug" target="_blank">
                                                {{ wishlist.product ? wishlist.product.name : '-' }}
                                             </a>
                                          </div>
                                          <div class="font-normal text-gray-500">
                                             {{ wishlist.product ? wishlist.product.brand_id : '-' }}
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </template>
                              <template v-else>
                                 <div>
                                    <p class="text-center text-2xl">No Cart Found !</p>
                                 </div>
                              </template>
                           </div>
                           <div
                               class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900">{{ formatSimpleDate(wishlist.created_at) }}</div>
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
                        <Pagination :fetchNewData="fetchCart" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Cart Found !</p>
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
      name: "Cart",
      data() {
         return {
            loading: true,
            carts: [],
            keyword: '',
            row_count: this.$store.state.defaultRowCount,
            showModal: false,
            imgModal: '',
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
         fetchCart(url) {
            this.loading = true;
            url = url || '/admin/cart'
            axios.get(url, {
               params: {
                  rows: this.row_count,
                  keyword: this.keyword,
               }
            })
                .then(res => {
                   this.loading = false;
                   this.carts = res.data.data;
                   let {data, ...pagination} = res.data;
                   pagination.links.pop();
                   pagination.links.shift();
                   this.pagination = pagination;
                })
                .catch(err => {
                   this.loading = false;
                })
         }
      },
      created() {
         this.fetchCart();
      },
   }
</script>

<style lang="scss" scoped>

</style>
