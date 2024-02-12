<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-folder-tree"></i>
            <h3 class="text-start my-8">Sub Category</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="block">
               <form @submit.prevent="editOrCreateSubCategory()">
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="sub_category"
                               title="The name is how it appears on your site.">Sub Category
                           <span class="text-red-600">*</span>
                        </label>
                        <input id="sub_category" v-model="name" class="form-input" placeholder="Western Wear" required
                               type="text">
                     </div>
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="slug"
                               title="The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.">Slug
                           <span class="text-red-600">*</span></label>
                        <input id="slug" v-model="slug" class="form-input" placeholder="western-wear" required type="text">
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="image"
                               title="An image belonging to a specific type, often distinguished by shared characteristics or features.">Category
                           Image</label>
                        <input id="image" accept="image/*" class="form-input" placeholder="western-wear"
                               type="file" @change="handleImageChange($event)">
                     </div>
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="image"
                               title="Those immediately above the category in the hierarchy">Parent Category <span
                            class="text-red-600">*</span></label>
                        <select v-model="parent_category_id" class="form-input" placeholder="western-wear" required>
                           <option selected value="">Select Parent Category</option>
                           <option v-for="(parent, index) in parent_category" :key="index" :value="parent.id">{{ parent.name }}
                           </option>
                        </select>
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="hsn"
                               title="A numerical code used to classify products in international trade">HSN (Harmonized System of
                           Nomenclature)
                           <span class="text-red-600">*</span>
                        </label>
                        <input id="hsn" v-model="hsn" class="form-input" placeholder="8471" type="text">
                     </div>
                     <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="gst"
                               title="GST is a mandatory financial charge imposed by the government on goods and services">GST (%)
                           <span class="text-red-600">*</span>
                        </label>
                        <input id="gst" v-model="gst" class="form-input" placeholder="18" type="number">
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
                        <button :disabled="!pagination.prev_page_url" class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50"
                                title="Previous"
                                @click="fetchSubCategory(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50"
                                title="Next" @click="fetchSubCategory(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="relative">
                        <select v-model="status" class="block appearance-none w-32 leading-tight h-full cursor-pointer text-black bg-white border border-gray-400 focus:outline-none hover:shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-none font-medium rounded-lg text-sm px-3 py-2" title="Status"
                                @change="fetchSubCategory()">
                           <option class="bg-gray-100" value="">All</option>
                           <option class="bg-gray-100" value="1">Published</option>
                           <option class="bg-gray-100" value="0">Un Published</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div v-if="keyword"
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchSubCategory();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text"
                               @keydown.enter="fetchSubCategory()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                @click="fetchSubCategory()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count"
                                class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchSubCategory()">
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
               <template v-else-if="sub_category && sub_category.length > 0">
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
                              Image
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Parent Category
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Name
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Slug
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              HSN
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              gst
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Status
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Last Update
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Actions
                           </div>
                        </div>
                        <div v-for="(c, index) in sub_category" v-bind:key="index"
                             :class="{ 'bg-primary-200': c.id === editId }" class="table-row table-body hover:bg-primary-100">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                              <div class="flex items-center">
                                 <input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" type="checkbox">
                              </div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">
                              {{ pagination.from + index }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm p-1 text-center !align-middle">
                              <img v-if="c.image" :alt="c.name"
                                   :src="$store.state.storageUrl + c.image"
                                   class="w-14 h-14 border border-gray-400 mx-auto p-1 rounded-[50%]"
                                   @click="imageModal($store.state.storageUrl + c.image)" @error="imageLoadError">
                              <p v-else class="text-center text-gray-800">--No Image--</p>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-1 text-center">
                              {{ c.category ? c.category.name : '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              {{ c.name || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ c.slug || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ c.hsn || '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              {{ c.gst || '0' }}%
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="c.id" :status="!!c.status" :update="updateStatus"/>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(c.updated_at)"></div>
                              <div class="text-sm">({{ timeAgo(c.updated_at) }})</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex gap-4 items-center justify-center">
                                 <a class="font-medium cursor-pointer text-yellow-500" href="#" type="button"
                                    @click="editSubCategory(c.id)">
                                    <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                 </a>
                                 <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                                    @click="deleteSubCategory(c.id)">
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
                        <Pagination :fetchNewData="fetchSubCategory" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Sub Categories Found !</p>
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
      name: "SubCategory",
      data() {
         return {
            loading: false,
            dataLoading: true,
            parent_category: [],
            sub_category: [{}],
            name: '',
            slug: '',
            hsn: '',
            gst: '',
            image: '',
            parent_category_id: '',
            keyword: '',
            status: '',
            row_count: this.$store.state.defaultRowCount,
            showModal: false,
            imgModal: '',
            pagination: {},
            editId: '',
         }
      },
      watch: {
         name: function () {
            this.slug = this.slugify(this.name);
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
         handleImageChange(e) {
            let file = e.target?.files[0];
            if (file) {
               this.image = file;
            }
         },
         updateStatus(id, status) {
            axios.put('/admin/sub-category/' + id, {status})
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   let index = this.sub_category.findIndex(sub_cat => sub_cat.id === id)
                   this.$set(this.sub_category, index, res.data.data)
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         clear() {
            this.name = '';
            this.slug = '';
            this.hsn = '';
            this.gst = '';
            this.image = '';
            this.parent_category_id = '';
            this.editId = '';
            $('form').trigger("reset");
         },
         editSubCategory(id) {
            this.loading = true;
            axios.get('/admin/sub-category/' + id)
                .then(res => {
                   this.editId = res.data.data.id;
                   this.name = res.data.data.name;
                   this.slug = res.data.data.slug;
                   this.hsn = res.data.data.hsn;
                   this.gst = res.data.data.gst;
                   this.parent_category_id = res.data.data.category_id;
                   this.loading = false;
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         deleteSubCategory(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            this.loading = true;
            axios.delete('/admin/sub-category/' + id)
                .then(res => {
                   this.loading = true;
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchSubCategory();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         editOrCreateSubCategory() {
            let url = '/admin/sub-category';
            let formData = new FormData();

            // Basic fields
            formData.append('name', this.name.trim());
            formData.append('slug', this.slug);
            formData.append('hsn', this.hsn);
            formData.append('gst', this.gst);
            formData.append('category_id', this.parent_category_id);

            //Image field
            if (this.image) {
               formData.append('image', this.image)
            }
            //if the action is EDIT
            if (this.editId) {
               url = '/admin/sub-category/' + this.editId;
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
                   this.fetchSubCategory();
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchParentCategory() {
            this.dataLoading = true;
            axios.get('/admin/category', {
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
         fetchSubCategory(url) {
            this.dataLoading = true;
            url = url || '/admin/sub-category'
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
                   this.sub_category = res.data.data;
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
         this.fetchParentCategory();
         this.fetchSubCategory();
      },
   }
</script>

<style lang="scss" scoped></style>
