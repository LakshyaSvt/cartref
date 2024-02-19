<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-circle-h"></i>
            <h3 class="text-start my-8">Home Sliders</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="block">
               <form @submit.prevent="editOrCreateHomeSliders()">
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="image"
                               title="Those immediately above the category in the hierarchy">Category <span
                            class="text-red-600">*</span></label>
                        <select v-model="category" class="form-input" required>
                           <option selected value="">Select Category</option>
                           <option v-for="(category) in categories" :key="category" :value="category">{{ category
                              }}
                           </option>
                        </select>
                     </div>
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="url">Slug <span
                            class="text-red-600">*</span></label>
                        <input id="url" v-model="url" :placeholder="$store.state.url" class="form-input" required
                               type="url">
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="background_image"
                               title="An image belonging to a specific type, often distinguished by shared characteristics or features.">
                           Background Image (Desktop)</label>
                        <input id="background_image" accept="image/*" class="form-input"
                               placeholder="western-wear" type="file" @change="handleBgImageChange($event)">
                     </div>
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="mobile_background_image"
                               title="An image belonging to a specific type, often distinguished by shared characteristics or features.">
                           Background Image (Mobile)</label>
                        <input id="mobile_background_image" accept="image/*" class="form-input"
                               placeholder="western-wear" type="file" @change="handleMbBgImageChange($event)">
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="w-full my-2">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="admin_comments">Page
                           Description </label>
                        <RichTextEditor :initial_value="page_description"
                                        :setVal="(val) => page_description = val"/>
                     </div>
                  </div>
                  <div class="text-center">
                     <button class="submit-btn" type="submit">
                        {{ this.editId ? 'Update' : 'Create' }}
                     </button>
                     <button class="clear-btn" type="button"
                             @click="clear()">
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
                        <button :disabled="!pagination.prev_page_url"
                                class="prev-next-btn" title="Previous"
                                @click="fetchHomeSliders(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url"
                                class="prev-next-btn" title="Next"
                                @click="fetchHomeSliders(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="relative">
                        <select v-model="status" class="block appearance-none w-32 leading-tight h-full cursor-pointer text-black bg-white border border-gray-400 focus:outline-none hover:shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-none font-medium rounded-lg text-sm px-3 py-2" title="Status"
                                @change="fetchHomeSliders()">
                           <option class="bg-gray-100" value="">All</option>
                           <option class="bg-gray-100" value="1">Published</option>
                           <option class="bg-gray-100" value="0">Un Published</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div v-if="keyword"
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchHomeSliders();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text"
                               @keydown.enter="fetchHomeSliders()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchHomeSliders()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select
                            v-model="row_count"
                            class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchHomeSliders()">
                           <option v-for="(count, index) in $store.state.row_counts" :key="index"
                                   :value="count.toLowerCase()" class="bg-white">
                              {{ count }}
                           </option>
                        </select>
                     </div>
                  </div>
               </div>
               <template v-if="dataLoading">
                  <Skeleton/>
               </template>
               <template v-else-if="sliders && sliders.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div
                               class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              S.No.
                           </div>
                           <div
                               class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Image (Desktop)
                           </div>
                           <div
                               class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Image (Mobile)
                           </div>
                           <div
                               class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Category
                           </div>
                           <div
                               class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-72">
                              Url
                           </div>
                           <div
                               class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Description
                           </div>
                           <div
                               class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Status
                           </div>
                           <div
                               class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Last Update
                           </div>
                           <div
                               class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Actions
                           </div>
                        </div>
                        <div v-for="(slider, index) in sliders" v-bind:key="slider.id"
                             :class="{ 'bg-primary-200': slider.id === editId }"
                             class="table-row table-body hover:bg-primary-100">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">
                              {{ pagination.from + index }}
                           </div>
                           <div
                               class="table-cell border-t border-l border-gray-500 text-sm p-1 text-center !align-middle">
                              <img v-if="slider.background_image"
                                   :alt="slider.name"
                                   :src="$store.state.storageUrl + slider.background_image"
                                   class="w-14 h-14 border border-gray-400 mx-auto p-1 rounded-[50%]"
                                   @click="imageModal($store.state.storageUrl + slider.background_image)" @error="imageLoadError">
                              <p v-else class="text-center text-gray-800">--No Image--</p>
                           </div>
                           <div
                               class="table-cell border-t border-l border-gray-500 text-sm p-1 text-center !align-middle">
                              <img v-if="slider.mb_background_image"
                                   :alt="slider.name"
                                   :src="$store.state.storageUrl + slider.mb_background_image"
                                   class="w-14 h-14 border border-gray-400 mx-auto p-1 rounded-[50%]"
                                   @click="imageModal($store.state.storageUrl + slider.mb_background_image)" @error="imageLoadError">
                              <p v-else class="text-center text-gray-800">--No Image--</p>
                           </div>
                           <div
                               class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-1 text-center">
                              {{ slider.category || '-' }}
                           </div>
                           <div
                               class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-4 py-2 text-center">
                              <a :href="slider.url" class="cursor-pointer" target="_blank">
                                 <i
                                     class="fi fi-rr-arrow-up-right-from-square text-primary-500 text-sm mr-1"></i>
                              </a>
                              {{ slider.url || '-' }}
                           </div>
                           <div :title="slider?.page_description"
                                class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-1 text-center" v-html="slider?.page_description">
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="slider.id" :status="!!slider.status" :update="updateStatus"/>
                           </div>
                           <div
                               class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(slider.updated_at)">
                              </div>
                              <div class="text-sm">({{ timeAgo(slider.updated_at) }})</div>
                           </div>
                           <div
                               class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex gap-4 items-center justify-center">
                                 <a class="font-medium cursor-pointer text-yellow-500" href="#" type="button"
                                    @click="editHomeSliders(slider.id)">
                                    <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                 </a>
                                 <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                                    @click="deleteHomeSliders(slider.id)">
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
                        <Pagination :fetchNewData="fetchHomeSliders" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Sliders Found !</p>
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
      name: "HomeSliders",
      data() {
         return {
            loading: false,
            dataLoading: true,
            categories: [
               "Home",
               "Men",
               "Women",
               "Home Décor",
               "Beauty",
               "Accessories"
            ],
            sliders: [{page_description: "---"}],
            background_image: '',
            mb_background_image: '',
            category: '',
            page_description: '',
            url: '',
            //filter
            keyword: '',
            status: '',
            row_count: this.$store.state.defaultRowCount,
            showModal: false,
            imgModal: '',
            pagination: {},
            editId: '',
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
         handleBgImageChange(e) {
            let file = e.target?.files[0];
            if (file) {
               this.background_image = file;
            }
         },
         handleMbBgImageChange(e) {
            let file = e.target?.files[0];
            if (file) {
               this.mb_background_image = file;
            }
         },
         updateStatus(id, status) {
            axios.put('/admin/home-slider/' + id, {status})
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   let index = this.sliders.findIndex(slide => slide.id === id)
                   this.$set(this.sliders, index, res.data.data)
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         clear() {
            this.category = '';
            this.page_description = '';
            this.url = '';
            this.background_image = '';
            this.mb_background_image = '';
            this.editId = '';
            $('form').trigger("reset");
         },
         editHomeSliders(id) {
            this.loading = true;
            axios.get('/admin/home-slider/' + id)
                .then(res => {
                   this.editId = res.data.data.id;
                   this.category = res.data.data.category;
                   this.page_description = res.data.data.page_description;
                   this.url = res.data.data.url;
                   this.loading = false;
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         deleteHomeSliders(id) {
            this.loading = true;
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            axios.delete('/admin/home-slider/' + id)
                .then(res => {
                   this.loading = true;
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchHomeSliders();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         editOrCreateHomeSliders() {
            let url = '/admin/home-slider';
            let formData = new FormData();

            // Basic fields
            formData.append('category', this.category);
            formData.append('page_description', this.page_description);
            formData.append('url', this.url);

            //Image fields
            if (this.background_image) {
               formData.append('background_image', this.background_image)
            }
            if (this.mb_background_image) {
               formData.append('mb_background_image', this.mb_background_image)
            }
            //if the action is EDIT
            if (this.editId) {
               url = '/admin/home-slider/' + this.editId;
               formData.append('_method', 'PUT');
               formData.append('id', this.editId);
            }

            const headers = {'Content-Type': 'multipart/form-data'};
            this.loading = true;
            axios.post(url, formData, {headers})
                .then(res => {
                   this.loading = false;
                   this.show_toast(res.data.status, res.data.msg);
                   this.clear();
                   this.fetchHomeSliders();
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchHomeSliders(url) {
            this.dataLoading = true;
            url = url || '/admin/home-slider'
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
                   this.sliders = res.data.data;
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
         this.fetchHomeSliders();
      },
   }
</script>

<style lang="scss" scoped></style>
