<template>
   <div>
      <div class="container mx-auto my-2 px-4">
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-chart-tree-map"></i>
            <h3 class="text-start my-8">Post</h3>
         </div>
         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="block">
               <form @submit.prevent="editOrCreatePost()">
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="style" title="The name is how it appears on your site.">Title
                           <span class="text-red-600">*</span>
                        </label>
                        <input id="style" v-model="title" class="form-input" placeholder="Floral Print" required type="text" @change="slug = slugify(title)">
                     </div>
                     <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900" for="slug"
                               title="The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.">
                           Slug <span class="text-red-600">*</span></label>
                        <input id="slug" v-model="slug" class="form-input" placeholder="floral-print" required
                               type="text">
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Body</label>
                        <RichTextEditor :initial_value="body" :setVal="(val) => body = val" height="400"/>
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Category <span class="text-red-600">*</span></label>
                        <div class="relative">
                           <select id="style_id" v-model="category_id" class="form-input appearance-none" required>
                              <option selected value="">Select Category</option>
                              <option v-for="(category) in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                           </select>
                           <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                              <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                           </div>
                        </div>
                     </div>
                     <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Image </label>
                        <input id="img" accept="image/*" class="form-input" name="main_img" type="file" @change="handleImageChange($event)">
                     </div>
                  </div>
                  <div class="md:flex mb-3">
                     <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Meta Description </label>
                        <textarea id="description" v-model="meta_description" class="form-input h-36" placeholder=""></textarea>
                     </div>
                     <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                        <label class="block mb-2 text-sm font-bold text-gray-900">Excerpt <small>Small description of this post</small></label>
                        <textarea id="excerpt" v-model="excerpt" class="form-input h-36" placeholder=""></textarea>
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
                        <button :disabled="!pagination.prev_page_url" class="prev-next-btn"
                                title="Previous"
                                @click="fetchPost(pagination.prev_page_url)">
                           <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                        </button>
                        <button :disabled="!pagination.next_page_url" class="prev-next-btn"
                                title="Next"
                                @click="fetchPost(pagination.next_page_url)">
                           <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                     <div class="relative">
                        <select v-model="status" class="filter-dropdown" title="Status" @change="fetchPost()">
                           <option class="bg-gray-100" value="">All</option>
                           <option class="bg-gray-100" value="PUBLISHED">PUBLISHED</option>
                           <option class="bg-gray-100" value="DRAFT">DRAFT</option>
                           <option class="bg-gray-100" value="PENDING">PENDING</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                           <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                        </div>
                     </div>
                     <label class="sr-only" for="table-search">Search</label>
                     <div class="relative">
                        <div v-if="keyword"
                             class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchPost();">
                           <i class="fi fi-rr-cross-small mr-1"></i>
                        </div>
                        <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                           <i class="fi fi-rr-search mr-1"></i>
                        </div>
                        <input v-model="keyword" class="search" placeholder="Search" type="text" @keydown.enter="fetchPost()">
                     </div>
                     <div class="flex border border-gray-600 rounded-lg bg-white">
                        <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer" @click="fetchPost()">
                           <i class="ffi fi-rr-refresh mr-1"></i>
                        </button>
                        <select v-model="row_count" class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchPost()">
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
               <template v-else-if="posts && posts.length > 0">
                  <div class="clear-right overflow-x-auto">
                     <div class="table border-solid border border-gray-500 w-full">
                        <div class="table-row table-head">
                           <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">
                              S.No.
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Image
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Title
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Slug
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Category
                           </div>
                           <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                              Featured
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
                        <div v-for="(post, index) in posts" v-bind:key="index" :class="{ 'bg-primary-200': post.id === editId }" class="table-row table-body hover:bg-primary-100">
                           <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">
                              {{ pagination.from + index }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              <img v-if="post.image" :alt="post.name"
                                   :src="$store.state.storageUrl + post.image"
                                   class="w-14 h-14 border border-gray-400 mx-auto p-1 rounded-[50%]"
                                   @click="imageModal($store.state.storageUrl + post.image)" @error="imageLoadError">
                              <p v-else class="text-center text-gray-800">--No Image--</p>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              {{ post.title }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                              {{ post.slug }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 font-semibold text-sm px-1 text-center py-1">
                              {{ post.category ? post.category.name : '-' }}
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="post.id" :status="!!post.featured" :update="(id, value) => { updateStatus(id, value, 'featured') }"/>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                              <StatusCheckbox :id="post.id" :status="post.status === 'PUBLISHED'" :update="(id, value) => { updateStatus(id, value, 'status') }"/>
                              <div>{{ post.status }}</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                              <div class="font-normal text-gray-900" v-html="formDateTime(post.updated_at)"></div>
                              <div class="text-sm">({{ timeAgo(post.updated_at) }})</div>
                           </div>
                           <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                              <div class="flex gap-4 items-center justify-center">
                                 <a class="font-medium cursor-pointer text-yellow-500" href="javascript:void(0)" type="button" @click="editPost(post.id)">
                                    <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                 </a>
                                 <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button" @click="deletePost(post.id)">
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
                        <Pagination :fetchNewData="fetchPost" :pagination="pagination"/>
                     </div>
                  </div>
               </template>
               <template v-else>
                  <div>
                     <p class="text-center text-2xl">No Posts Found !</p>
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
      name: "Post",
      data() {
         return {
            loading: true,

            posts: [{}],
            categories: [],
            title: '',
            slug: '',
            category_id: '',
            body: '',
            meta_description: '',
            meta_keywords: '',
            seo_title: '',
            excerpt: '',
            image: '',

            keyword: '',
            status: '',
            showModal: false,
            imgModal: '',
            row_count: this.$store.state.defaultRowCount,
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
         handleImageChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            this.image = files[0];
         },
         updateStatus(id, value, field) {
            let data = {};
            data[field] = value;
            axios.put('admin/post/' + id, data)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   let index = this.posts.findIndex(style => style.id === id)
                   this.$set(this.posts, index, res.data.data)
                })
                .catch(err => {
                   this.dataLoading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         clear() {
            this.title = '';
            this.slug = '';
            this.category_id = '';
            this.body = '';
            this.meta_description = '';
            this.meta_keywords = '';
            this.seo_title = '';
            this.excerpt = '';
            this.image = '';
            $('form').reset();
         },
         editPost(id) {
            axios.get('admin/post/' + id)
                .then(res => {
                   this.editId = res.data.data.id;
                   this.title = res.data.data.title;
                   this.slug = res.data.data.slug;
                   this.category_id = res.data.data.category_id;
                   this.body = res.data.data.body;
                   this.meta_description = res.data.data.meta_description;
                   this.meta_keywords = res.data.data.meta_keywords;
                   this.seo_title = res.data.data.seo_title;
                   this.excerpt = res.data.data.excerpt;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         deletePost(id) {
            if (!confirm("Are you sure you want to delete ?")) {
               return false;
            }
            axios.delete('admin/post/' + id)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.fetchPost();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchPost();
                })
         },
         editOrCreatePost() {
            let url = 'admin/post';
            let formData = new FormData();

            formData.append('title', this.title);
            formData.append('slug', this.slug);
            formData.append('category_id', this.category_id);
            formData.append('body', this.body);
            formData.append('meta_description', this.meta_description);
            formData.append('meta_keywords', this.meta_keywords);
            formData.append('seo_title', this.seo_title);
            formData.append('excerpt', this.excerpt);
            formData.append('image', this.image);

            if (this.editId) {
               url = 'admin/post/' + this.editId;
               formData.append('_method', 'PUT');
               formData.append('id', this.editId)
            }

            axios.post(url, formData)
                .then(res => {
                   this.show_toast(res.data.status, res.data.msg);
                   this.clear();
                   this.fetchPost();
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                   this.fetchPost();
                })
         },
         fetchPost(url) {
            this.loading = true;
            url = url || 'admin/post'
            axios.get(url, {
               params: {
                  rows: this.row_count,
                  keyword: this.keyword.trim(),
                  status: this.status,
               }
            })
                .then(res => {
                   this.loading = false;
                   this.posts = res.data.data || [];
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
         fetchCategory() {
            this.loading = true;
            axios.get('admin/post/category', {
               params: {
                  rows: 'all',
                  status: 1,
               }
            })
                .then(res => {
                   this.loading = false;
                   this.categories = res.data.data || [];
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         }
      },
      created() {
         this.fetchPost();
         this.fetchCategory();
      },
   }
</script>

<style lang="scss" scoped></style>
