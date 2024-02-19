<template>
   <div>
      <navbar></navbar>
      <side-bar></side-bar>
      <div id="main" class="mt-12 xl:mt-20 md:pl-[3.35rem] mb-6">
         <router-view></router-view>
      </div>
      <Modal v-if="showcases.length > 0" :hide="closeNotifyModal" :show="show_modal" title="Heyyyy!! You just received a new Showroom At Home Order">
         <div class="clear-right overflow-x-auto">
            <div class="table border-solid border border-gray-500 mx-auto my-4">
               <div class="table-row table-head">
                  <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1">
                     Order No
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
               </div>
               <div v-for="(showcase, index) in showcases" :key="showcase.id" class="table-row table-body hover:bg-primary-100 bg-white">
                  <div class="table-cell border-t border-gray-500 text-sm font-semibold px-4 text-center">
                     {{ showcase.order_id }}<br>
                     <div v-html="'('+timeAgo(showcase.created_at)+')'"></div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-left py-1">
                     <div v-if="showcase.product" class="">
                        <div class="flex gap-1 items-center text-start text-gray-900 whitespace-nowrap dark:text-white w-[70%]">
                           <img :src="$store.state.storageUrl + showcase?.color_image" alt="product-img"
                                class="w-14 h-14 border rounded-[50%]"
                                @click="imageModal($store.state.storageUrl + showcase?.color_image)"
                                @error="imageLoadError">
                           <div class="pl-2 w-4/5">
                              <div :title="showcase.product?.name" class="text-base font-medium overflow-hidden whitespace-nowrap text-ellipsis hover:underline w-60">
                                 <a :href="showcase.color_link" target="_blank">
                                    {{ showcase.product.name ?? '-' }}
                                 </a>
                              </div>
                              <div class="font-normal text-gray-800">Size:- {{ showcase.size }}</div>
                              <div class="font-normal text-gray-800">Color:- {{ showcase.color }}</div>
                              <div class="font-normal text-gray-800">Brand:- {{ showcase.product.brand_id ?? '-' }}</div>
                              <div class="font-normal text-gray-800">SKU:- {{ showcase.product_sku }}</div>
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
               </div>
            </div>
         </div>
         <!-- Modal footer -->
         <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
            <form :action="`/showcase-at-home/my-orders/order/${showcases[0].order_id}/accept-order`" method="get">
               <div class="text-center">
                  <button class="submit-btn" type="submit">Accept</button>
               </div>
            </form>
         </div>
      </Modal>
      <ImageModal :hide="closeImageModal" :img="imgModal" :show="showImageModal"></ImageModal>
   </div>
</template>

<script>
   import Modal from "@components/Modal.vue";

   export default {
      name: "Main",
      components: {Modal},
      data() {
         return {
            channel: 'my-channel@' + this.$attrs.user.id,
            event: 'my-event',
            order_id: '',
            showcases: [],
            show_modal: false,
            imgModal: '',
            showImageModal: false,
         };
      },
      methods: {
         closeNotifyModal() {
            this.show_modal = false;
         },
         imageModal(img) {
            this.showImageModal = true;
            this.imgModal = img;
         },
         closeImageModal() {
            this.showImageModal = false;
         },
         fetchOrders() {
            axios.get(`/fetch/showroom-orders/${this.order_id}`)
                .then(res => {
                   this.showcases = res.data.showcases;
                   if (this.showcases.length > 0) {
                      this.show_modal = true;
                      this.playBeep();
                      this.notifyMe(`#${this.order_id} Order`);
                   }
                })
         },
         notifyMe(title) {
            if (Notification.permission !== 'granted')
               Notification.requestPermission();
            else {
               let notification = new Notification(title, {
                  icon: this.$store.state.favicon,
                  body: 'New Showroom At Home Order Received',
               });
            }
         },

         playBeep() {
            let audio = document.createElement('audio');
            audio.style.display = "none";
            audio.src = window.location.origin + '/mp3/new_order_beep.wav';
            audio.autoplay = true;
            audio.loop = true;
            document.body.appendChild(audio);
         },

         subscribePusher() {
            Pusher
                .subscribe(this.channel)
                .bind(this.event, (data) => {
                   this.order_id = data;
                   this.fetchOrders();
                   document.body.scrollTop = document.documentElement.scrollTop = 0;
                })
         }
      },
      created() {
         this.subscribePusher();
         this.$store.state.user = this.$attrs?.user;
      }
   }
</script>