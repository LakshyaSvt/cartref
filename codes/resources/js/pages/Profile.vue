<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <h1 v-if="editId && $store.state.user" class="text-center text-5xl tracking-wide underline uppercase">{{ $store.state.user.name }}</h1>
         <div class="mt-6 pt-2">
            <a class="inline-flex items-center gap-2 px-4 py-2 text-base font-bold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-gray-800 hover:bg-black hover:text-white"
               @click="$router.go(-1)">
               <i class="fi fi-rr-arrow-left text-base w-4 h-5"></i>
               Back
            </a>
         </div>
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-user"></i>
            <h3 v-if="editId" class="text-start my-8">Edit User</h3>
            <h3 v-else class="text-start my-8">Add User</h3>
         </div>
         <div class="p-2 overflow-x-auto my-2">
            <div class="block">
               <form @submit.prevent="submitForm">
                  <div class="flex flex-col md:flex-row gap-8">
                     <div class="w-full flex flex-col shadow-md sm:rounded-lg bg-white px-4 py-2 rounded-md">
                        <div class="w-full my-2">
                           <label class="block mb-2 text-sm font-bold text-gray-900" for="title"
                                  title="A concise name describing a product for easy identification and marketing.">
                              Name <span class="text-red-600">*</span>
                           </label>
                           <input id="title" v-model="user.name" class="form-input" placeholder="John Doe" required type="text">
                        </div>
                        <div class="w-full my-2">
                           <label class="block mb-2 text-sm font-bold text-gray-900" for="email">
                              Email <span class="text-red-600">*</span>
                           </label>
                           <input id="email" v-model="user.email" class="form-input" placeholder="john@gmail.com" required type="email">
                        </div>
                        <div class="flex gap-4 w-full my-2">
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Mobile <span class="text-red-600">*</span></label>
                              <input v-model="user.mobile" class="form-input" placeholder="7410XXX852" required type="tel">
                           </div>
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Password
                                 <span class="text-red-600">*</span>
                              </label>
                              <div class="relative">
                                 <input v-model="user.password" :required="!editId" :type="passwordType ? 'text' : 'password'" class="form-input" placeholder="Password">
                                 <i v-if="passwordType" class="absolute right-2.5 top-2.5 fi-rr-eye text-base w-4 h-5 cursor-pointer" @click="togglePasswordType()"></i>
                                 <i v-else class="absolute right-2.5 top-2.5 fi-rs-crossed-eye text-base w-4 h-5 cursor-pointer" @click="togglePasswordType()"></i>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="flex flex-col md:flex-row gap-8 my-4">
                     <div class="flex flex-col md:w-2/3 w-full">
                        <div class="flex flex-col w-full my-2 shadow-md sm:rounded-lg bg-white px-4 py-2 rounded-md">
                           <h1 class="text-xl font-bold text-black">Store Address</h1>
                           <hr class="border border-gray-200 mt-1 mb-2">
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Street Address 1</label>
                              <textarea id="description" v-model="user.street_address_1" class="form-input h-28" placeholder="Flat No / House No / Apartment / Building"></textarea>
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Street Address 2</label>
                              <textarea id="description" v-model="user.street_address_2" class="form-input h-28" placeholder="Colony / Area"></textarea>
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Landmark</label>
                              <textarea id="description" v-model="user.landmark" class="form-input h-28" placeholder="Hospital / Restaurant"></textarea>
                           </div>
                           <div class="flex flex-col md:flex-row gap-4 w-full mt-2 mb-1">
                              <div class="md:w-1/2 w-full">
                                 <label class="block mb-2 text-sm font-bold text-gray-900" for="pincode">Pincode <span class="text-red-600">*</span></label>
                                 <input id="pincode" v-model="user.pincode" class="form-input" placeholder="2820XX" required step="1" type="number">
                              </div>
                              <div class="md:w-1/2 w-full">
                                 <label class="block mb-2 text-sm font-bold text-gray-900" for="offer_price">City <span class="text-red-600">*</span></label>
                                 <input id="city" v-model="user.city" class="form-input" placeholder="Agra" required type="text">
                              </div>
                           </div>
                           <div class="flex flex-col md:flex-row gap-4 w-full mt-2 mb-1">
                              <div class="md:w-1/2 w-full">
                                 <label class="block mb-2 text-sm font-bold text-gray-900" for="state">State <span class="text-red-600">*</span></label>
                                 <input id="state" v-model="user.state" class="form-input" placeholder="UP" required type="text">
                              </div>
                              <div class="md:w-1/2 w-full">
                                 <label class="block mb-2 text-sm font-bold text-gray-900" for="country">Country <span class="text-red-600">*</span></label>
                                 <input id="country" v-model="user.country" class="form-input" placeholder="India" required type="text">
                              </div>
                           </div>
                        </div>
                        <div class="flex flex-col w-full my-2 shadow-md sm:rounded-lg bg-white px-4 py-2 rounded-md">
                           <h1 class="text-xl font-bold text-black">Brand Information</h1>
                           <hr class="border border-gray-200 mt-1 mb-2">
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900"> Which all brands you will be sellng? (Brand Name)</label>
                              <textarea id="brands" v-model="user.brands" class="form-input h-28" placeholder=""></textarea>
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Brand Description</label>
                              <textarea id="brand_description" v-model="user.brand_description" class="form-input h-28"></textarea>
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Brand Background Color</label>
                              <input v-model="user.brand_bg_color" class="form-input" type="color">
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-1 text-sm font-bold text-gray-900">Brand Logo</label>
                              <img v-if="user && user.brand_logo"
                                   :src="$store.state.storageUrl + user.brand_logo"
                                   alt="main-img"
                                   class="w-100 border h-[23rem] max:h-full w-full rounded-md shadow-md cursor-zoom-in"
                                   @click="imageModal($store.state.storageUrl + user.brand_logo)"
                                   @error="imageLoadError"/>
                              <div class="my-2">
                                 <input id="brand_logo_img" accept="image/*" class="form-input" name="avatar" type="file" @change="handleBrandLogoChange($event)">
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="flex flex-col md:w-1/3 w-full">
                        <div class="flex flex-col w-full my-2 shadow-md sm:rounded-lg bg-white px-4 py-2 rounded-md">
                           <h1 class="text-xl font-bold text-black">Company Information</h1>
                           <hr class="border border-gray-200 mt-1">
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Display Name</label>
                              <input v-model="user.brand_name" class="form-input" placeholder="ABCD Pvt Ltd" type="text">
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Registered Company Name</label>
                              <input v-model="user.company_name" class="form-input" placeholder="ABCD Private Limited Company" type="text">
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">GST Number</label>
                              <input v-model="user.gst_number" class="form-input" placeholder="AZXXL01KJ78SSOXX" type="text">
                           </div>
                           <div class="w-full my-2">
                              <div>
                                 <label class="block mb-1 text-sm font-bold text-gray-900">GST Certificate</label>
                                 <img v-if="user && user.gst_certificate"
                                      :src="$store.state.storageUrl + user.gst_certificate"
                                      alt="main-img"
                                      class="w-100 border h-[23rem] max:h-full w-full rounded-md shadow-md cursor-zoom-in"
                                      @click="imageModal($store.state.storageUrl + user.gst_certificate)"
                                      @error="imageLoadError"/>
                              </div>
                              <div class="my-2">
                                 <input id="gst_certificate_img" accept="image/*" class="form-input" name="gst_certificate" type="file" @change="handleGstCertificateChange($event)">
                              </div>
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Pancard Number</label>
                              <input v-model="user.company_pancard_number" class="form-input" placeholder="DPUYI8SHDSXX" type="text">
                           </div>
                           <div class="w-full my-2">
                              <div>
                                 <label class="block mb-1 text-sm font-bold text-gray-900">Company Pancard</label>
                                 <img v-if="user && user.company_pancard"
                                      :src="$store.state.storageUrl + user.company_pancard"
                                      alt="main-img"
                                      class="w-100 border h-[23rem] max:h-full w-full rounded-md shadow-md cursor-zoom-in"
                                      @click="imageModal($store.state.storageUrl + user.company_pancard)"
                                      @error="imageLoadError"/>
                              </div>
                              <div class="my-2">
                                 <input id="company_pancard_img" accept="image/*" class="form-input" name="company_pancard" type="file" @change="handlePancardChange($event)">
                              </div>
                           </div>
                           <div class="w-full my-2">
                              <div>
                                 <label class="block mb-1 text-sm font-bold text-gray-900">Signature</label>
                                 <img v-if="user && user.signature"
                                      :src="$store.state.storageUrl + user.signature"
                                      alt="main-img"
                                      class="w-100 border h-[23rem] max:h-full w-full rounded-md shadow-md cursor-zoom-in"
                                      @click="imageModal($store.state.storageUrl + user.signature)"
                                      @error="imageLoadError"/>
                              </div>
                              <div class="my-2">
                                 <input id="signature_img" accept="image/*" class="form-input" name="signature" type="file" @change="handleSignatureChange($event)">
                              </div>
                           </div>
                        </div>
                        <div class="flex flex-col w-full my-2 shadow-md sm:rounded-lg bg-white px-4 py-2 rounded-md">
                           <h1 class="text-xl font-bold text-black">Bank Account Details</h1>
                           <hr class="border border-gray-200 mt-1 mb-2">
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Bank Name</label>
                              <input v-model="user.bank_name" class="form-input" placeholder="Axis Bank" type="text">
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Account Number</label>
                              <input v-model="user.account_number" class="form-input" placeholder="0000122333444XX" type="text">
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">IFSC Code</label>
                              <input v-model="user.ifsc_code" class="form-input" placeholder="UTIB000XXX" type="text">
                           </div>
                           <div class="w-full my-2">
                              <label class="block mb-2 text-sm font-bold text-gray-900">Bank Address</label>
                              <textarea v-model="user.bank_address" class="form-input h-24" placeholder="Sanjay Place"></textarea>
                           </div>
                           <div class="w-full my-2">
                              <div v-if="user && Array.isArray(user.cancelled_check) && (user.cancelled_check).length > 0">
                                 <label class="block mb-1 text-sm font-bold text-gray-900">Cancelled Check</label>
                                 <a :href="$store.state.storageUrl + (user.cancelled_check)[0]?.download_link" class="text-base px-2 py-1 text-primary-500 hover:underline"
                                    target="_blank">
                                    {{ (user.cancelled_check)[0]?.original_name }}
                                 </a>
                              </div>
                              <div class="my-2">
                                 <input id="cancelled_check_img" class="form-input" name="cancelled_check" type="file" @change="handleCancelledChequeChange($event)">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="text-center">
                     <button class="submit-btn" type="submit"> {{ this.editId ? 'Update' : 'Create' }}</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <ImageModal :hide="closeImageModal" :img="imgModal" :show="showModal"></ImageModal>
   </div>
</template>

<script>
   import Checkbox from "@components/Checkbox.vue";

   export default {
      name: "Profile",
      components: {Checkbox},
      data() {
         return {
            loading: false,
            dataLoading: false,
            showModal: false,
            imgModal: '',
            passwordType: 0,
            editId: this.$store.state.user.id,
            roles: [],

            user: {
               //basic-text-fields
               name: '',
               email: '',
               gender: '',
               mobile: '',
               password: '',
               role_id: '',
               street_address_1: '',
               street_address_2: '',
               landmark: '',
               pincode: '',
               city: '',
               state: '',
               country: '',
               //side-info
               avatar: '',
               brand_store_rating: '',
               status: '',
               brand_name: '',
               company_name: '',
               gst_number: '',
               gst_certificate: '',
               company_pancard_number: '',
               company_pancard: '',
               signature: '',
               //bank-info
               bank_name: '',
               account_number: '',
               ifsc_code: '',
               bank_address: '',
               cancelled_check: '',
               //brand-info
               brands: '',
               brand_description: '',
               brand_logo: '',
               brand_bg_color: '',
            },
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
         togglePasswordType() {
            this.passwordType = !this.passwordType;
         },
         uploadImageToServer(formData) {
            return new Promise((resolve, reject) => {
               //uploading to db
               const config = {
                  headers: {'Content-Type': 'multipart/form-data'}
               }
               this.loading = true;
               axios.post(`/user/upload-images`, formData, config)
                   .then((res) => {
                      this.loading = false;
                      this.show_toast(res.data.status, res.data.msg);
                      resolve(res.data.data)
                   })
                   .catch(err => {
                      this.loading = false;
                      err.handleGlobally && err.handleGlobally();
                      reject(err);
                   })
            })
         },
         handleImageChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            const formData = new FormData();
            formData.append('images[0]', files[0]);
            this.uploadImageToServer(formData).then(res => {
               this.user.avatar = res[0];
               $('#avatar_img').val('')
            });
         },
         handleBrandLogoChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            const formData = new FormData();
            formData.append('images[0]', files[0]);
            this.uploadImageToServer(formData).then(res => {
               this.user.brand_logo = res[0];
               $('#brand_logo_img').val('')
            });
         },
         handleGstCertificateChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            const formData = new FormData();
            formData.append('images[0]', files[0]);
            this.uploadImageToServer(formData).then(res => {
               this.user.gst_certificate = res[0];
               $('#gst_certificate_img').val('')
            });
         },
         handleSignatureChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            const formData = new FormData();
            formData.append('images[0]', files[0]);
            this.uploadImageToServer(formData).then(res => {
               this.user.signature = res[0];
               $('#signature_img').val('')
            });
         },
         handlePancardChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            const formData = new FormData();
            formData.append('images[0]', files[0]);
            this.uploadImageToServer(formData).then(res => {
               this.user.company_pancard = res[0];
               $('#company_pancard_img').val('')
            });
         },
         handleCancelledChequeChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            const formData = new FormData();
            formData.append('file', files[0]);
            this.loading = true;
            axios.post(`/user/upload-check`, formData)
                .then((res) => {
                   this.loading = false;
                   this.show_toast(res.data.status, res.data.msg);
                   this.user.cancelled_check = res.data.data;
                   $('#cancelled_check_img').val('')
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         submitForm() {
            this.loading = true;
            let formData = this.user;
            formData.id = this.editId;
            if (this.user.cancelled_check) {
               formData.cancelled_check = JSON.stringify(formData.cancelled_check);
            }

            axios
                .post('/user/edit', formData)
                .then(res => {
                   this.loading = false;
                   if (res.data.data)
                      this.$store.state.user = res.data.data;
                   this.show_toast(res.data.status, res.data.msg);
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchUser() {
            this.loading = true;
            this.user = JSON.parse(JSON.stringify(this.$store.state.user));
            this.user.cancelled_check = JSON.parse(this.$store.state.user.cancelled_check) || [];
            this.loading = false;
         },
      }
      ,
      created() {
         this.fetchUser();
      }
   }
</script>

<style lang="scss" scoped>

</style>